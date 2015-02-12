<?php
namespace pipekung\classes;

use Yii;
use yii\db\Query;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Db {

	public static function getUserInFaculty($faculty_id=null) {
		$query = (new Query)
			->select('t1.id, t2.name, t4.name AS position_type, t3.name AS position_name')
			->from('user t1 
				JOIN profile t2 ON (t1.id = t2.user_id) 
				LEFT JOIN hr_position t3 ON (t3.id = t2.position_id) 
				LEFT JOIN hr_position_type t4 ON (t4.id = t2.position_type_id)')
			->where(['t2.faculty_id' => empty($faculty_id) ? self::getFacultyByUser() : $faculty_id])
		;
		$cmd = $query->createCommand();
		$rows = $cmd->queryAll();
		return $rows;
	}

	public static function getFacultyByUser($user_id=null) {
		$query = (new Query)
			->select('faculty_id')
			->from('profile')
			->where(['user_id' => empty($user_id) ? Yii::$app->user->id : $user_id])
		;
		$cmd = $query->createCommand();
		$result = $cmd->queryScalar();
		return $result;
	}

}
