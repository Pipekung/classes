<?php
namespace pipekung\classes;

use Yii;
use yii\db\Query;
use pipekung\classes\Common;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Db {

	public static function getUserInFaculty($faculty_id=null, $year=null) {
		$query = (new Query)
			->select('t1.id, t2.name, t4.name AS position_type, t3.name AS position_name, leave_store')
			->from('user t1 
				JOIN profile t2 ON (t1.id = t2.user_id) 
				LEFT JOIN hr_position t3 ON (t3.id = t2.position_id) 
				LEFT JOIN hr_position_type t4 ON (t4.id = t2.position_type_id)
				LEFT JOIN hr_leave_store t5 ON (t1.id = t5.user_id AND t5.fiscal_year = :fiscal_year)
			')
			->where([
				't2.faculty_id' => empty($faculty_id) ? self::getFacultyByUser() : $faculty_id,
			])
			->addParams([':fiscal_year' => empty($year) ? Common::getFiscalYear() : $year])
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

	public static function countLeaveDay($id, $user_id=null) {
		$query = (new Query)
			->select('SUM(t1.count_day)')
			->from('hr_leave t1, (SELECT `id`, `leave_type_id`, `end_date` FROM `hr_leave` WHERE `id` = :id) t2')
			->where('t1.leave_type_id = t2.leave_type_id AND t1.end_date < t2.end_date')
			->addParams([':id' => $id])
		;
		$cmd = $query->createCommand();
		$result = $cmd->queryScalar();
		return $result;
	}

	public static function getRelaxDay($user_id=null, $fiscal_year=null) {
		$query = (new Query)
			->select('leave_store')
			->from('hr_leave_store')
			->where([
				'user_id' => empty($user_id) ? Yii::$app->user->id : $user_id,
				'fiscal_year' => empty($fiscal_year) ? Common::getFiscalYear() : $fiscal_year,
			])
		;
		$cmd = $query->createCommand();
		$result = $cmd->queryScalar();
		return $result ? $result : '0';
	}

}
