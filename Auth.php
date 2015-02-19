<?php

namespace pipekung\classes;

use Yii;
use pipekung\classes\Curl;
use pipekung\classes\Db;
use dektrium\user\models\User;
use dektrium\user\helpers\Password;
use mdm\admin\models\AuthItem;
use yii\db\Connection;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Auth {

	public $username;
	public $password;
	public $model;
	public $allowAddUser;

	public function __construct($model, $allowAddUser = true) {
		$this->model = $model;
		$this->username = $model->login;
		$this->password = $model->password;
		$this->allowAddUser = $allowAddUser;
	}

	public function login() {
		$authen = false;
		if ($this->db()) 
			$authen = true;
		elseif ($this->ldap()) 
			$authen = true;

		return $authen;
	}

	public function addUser($ldap) {
		$profile = Db::getUserProfileFromHr($ldap->citizen);
		$user = new User;
		$user->id = $profile['id'];
		$user->username = $this->username;
		$user->email = "{$this->username}@kku.ac.th";
		$user->password_hash = Password::hash($this->password);
		$user->save(false);

		$connection = new Connection(Yii::$app->db);
		$connection->createCommand('INSERT INTO auth_assignment VALUES(:item_name, :user_id, :created_at)', [
		    ':item_name' => 'User',
		    ':user_id' => $profile['id'],
		    ':created_at' => time(),
		])->execute();

		$connection->createCommand('UPDATE profile SET 
			name = :name,
			address = :address,
			phone = :phone,
			faculty_id = :faculty_id,
			position_id = :position_id,
			position_type_id = :position_type_id,
			level_id = :level_id,
			division_id = :division_id
			WHERE user_id = :user_id
		', [
			':name' => "{$profile['title']}{$profile['firstname']} {$profile['lastname']}",
		    ':address' => "{$profile['homeadd']} {$profile['moo']} {$profile['tok']} {$profile['soi']} {$profile['street']} {$profile['district']} {$profile['amphur']} {$profile['province']} {$profile['zipcode']}",
		    ':phone' => $profile['telephone'],
		    ':faculty_id' => Db::mapFaculty($profile['faculty'])['id'],
		    ':position_id' => Db::mapPosition($profile['posi'])['id'],
		    ':position_type_id' => Db::mapPositionType($profile['positype'])['id'],
		    ':level_id' => Db::mapLevel($profile['level'])['id'],
		    ':division_id' => Db::mapDivision($profile['division'])['id'],
		    ':user_id' => $profile['id'],
		])->execute();
	}

	public function ldap() {
		$authen = false;
		$ua = base64_encode($_SERVER['HTTP_USER_AGENT']);
	    $apiKey = Yii::$app->params['apiKey'];
	    $password = base64_encode(md5($this->password));
	    $url = Yii::$app->params['authUrl'] . "/{$ua}/{$apiKey}/{$password}/{$this->username}/json";
	    $ldap = json_decode(Curl::getData($url));
    	if (isset($ldap->success) && $ldap->success) {
    		$user = User::findOne(['username' => $this->username]);
    		if (empty($user) || $user === null) {
    			if ($this->allowAddUser) $this->addUser($ldap);
    		} else {
 				User::updateAll(['password_hash' => Password::hash($this->password)], ['username' => $this->username]);
    		}
    		if ($this->db()) $authen = true;
		} 

		return $authen;
	}

	public function db() {
		return $this->model->login();
	}

}