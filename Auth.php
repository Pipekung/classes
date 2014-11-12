<?php

namespace pipekung\classes;

use Yii;
use pipekung\classes\Curl;
use dektrium\user\models\User;
use dektrium\user\helpers\Password;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Auth {

	public $username;
	public $password;
	public $allowAddUser;

	public function __construct($username, $password, $allowAddUser = true) {
		$this->username = $username;
		$this->password = $password;
		$this->allowAddUser = $allowAddUser;
	}

	public function addUser() {
		$user = new User;
		$user->username = $this->username;
		$user->email = "{$this->username}@kku.ac.th";
		$user->password_hash = Password::hash($this->password);
		$user->save(false);
	}

	public function ldap() {
		$ua = base64_encode($_SERVER['HTTP_USER_AGENT']);
	    $apiKey = Yii::$app->params['apiKey'];
	    $password = base64_encode(md5($this->password));
	    $url = Yii::$app->params['authUrl'] . "/{$ua}/{$apiKey}/{$password}/{$this->username}/json";
	    $auth = json_decode(Curl::getData($url));
    	if (isset($auth->success) && $auth->success) {
    		$user = User::findOne(['username' => $this->username]);
    		if (empty($user) || $user === null) {
    			if ($this->allowAddUser) $this->addUser();
    		} else {
 				User::updateAll(['password_hash' => Password::hash($this->password)], ['username' => $this->username]);
    		}
 			return true;
		} return false;
	}

}