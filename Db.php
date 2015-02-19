<?php
namespace pipekung\classes;

use Yii;
use yii\db\Query;
use pipekung\classes\Common;
use app\models\HrFaculty;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Db {

	public static function getUserInFaculty($faculty_id=null, $year=null) {
		$rows = (new Query)
			->select(['id',
				'name' => 'CONCAT(title, firstname, \' \', lastname)',
				'position_type' => 'positype',
				'position_name' => 'posi',
			])
			->from('hr_person')
			->where([
				'faculty' => empty($faculty_id) ? self::getFacultyByUser()['name'] : HrFaculty::findOne(['id' => $faculty_id])->name, 
				'statuslist' => 'ปฏิบัติราชการ'
			])
			->orderBy('positype, posi, firstname, lastname')
			->all(Yii::$app->db_kku)
		;
		foreach ($rows as $k => $val) {
			$store = (new Query)
				->select('leave_store, 
					SUM(t6.count_day) AS relax_day, 
					(leave_store - SUM(t6.count_day)) AS relax_summary
				')
				->from('hr_leave_store t1 JOIN hr_leave t6 ON (t1.user_id = t6.user_id AND t6.fiscal_year = :fiscal_year AND t6.leave_type_id = 2)')
				->where('t1.user_id = :user_id AND t1.fiscal_year = :fiscal_year')
				->addParams([':user_id' => $val['id'], ':fiscal_year' => empty($year) ? Common::getFiscalYear() : $year])
				->one()
			;
			if (! empty($store)) {
				$rows[$k]['leave_store'] = $store['leave_store'];
				$rows[$k]['relax_day'] = $store['relax_day'];
				$rows[$k]['relax_summary'] = $store['relax_summary'];
			} else {
				$rows[$k]['leave_store'] = '0';
				$rows[$k]['relax_day'] = '0';
				$rows[$k]['relax_summary'] = '0';
			}
		}
		return $rows;
	}

	public static function getFacultyByUser($user_id=null) {
		$result = (new Query)
			->select('t2.id, t2.code, t2.name')
			->from('profile t1 JOIN hr_faculty t2 ON (t1.faculty_id = t2.id)')
			->where(['user_id' => empty($user_id) ? Yii::$app->user->id : $user_id])
			->one()
		;
		return $result;
	}

	public static function countLeaveDay($id, $user_id=null) {
		$result = (new Query)
			->select('SUM(t1.count_day)')
			->from('hr_leave t1, (SELECT `id`, `leave_type_id`, `end_date` FROM `hr_leave` WHERE `id` = :id) t2')
			->where('t1.leave_type_id = t2.leave_type_id AND t1.end_date < t2.end_date')
			->addParams([':id' => $id])
			->scalar()
		;
		return $result ? $result : '0';
	}

	public static function getRelaxDay($user_id=null, $fiscal_year=null) {
		$result = (new Query)
			->select('leave_store')
			->from('hr_leave_store')
			->where([
				'user_id' => empty($user_id) ? Yii::$app->user->id : $user_id,
				'fiscal_year' => empty($fiscal_year) ? Common::getFiscalYear() : $fiscal_year,
			])
			->scalar()
		;
		return $result ? $result : '0';
	}

	public static function getUserProfile($user_id=null) {
		$result = (new Query)
			->select('*')
			->from('profile')
			->where(['user_id' => empty($user_id) ? Yii::$app->user->id : $user_id])
			->one()
		;
		return $result;
	}

	public static function getUserProfileFromHr($citizen_id) {
		$result = (new Query)
			->select('*')
			->from('hr_person')
			->where(['citizen_id' => $citizen_id])
			->one(Yii::$app->db_kku)
		;
		return $result;
	}

	public static function mapFaculty($q) {
		$result = (new Query)
			->select('*')
			->from('hr_faculty')
			->where('id = :q OR code = :q OR name = :q')
			->addParams([':q' => $q])
			->one()
		;
		return $result;
	}

	public static function mapPosition($q) {
		$result = (new Query)
			->select('*')
			->from('hr_position')
			->where('id = :q OR name = :q')
			->addParams([':q' => $q])
			->one()
		;
		return $result;
	}

	public static function mapPositionType($q) {
		$result = (new Query)
			->select('*')
			->from('hr_position_type')
			->where('id = :q OR name = :q')
			->addParams([':q' => $q])
			->one()
		;
		return $result;
	}

	public static function mapLevel($q) {
		$result = (new Query)
			->select('*')
			->from('hr_level')
			->where('name = :q')
			->addParams([':q' => $q])
			->one()
		;
		return $result;
	}

	public static function mapDivision($q) {
		$result = (new Query)
			->select('*')
			->from('hr_division')
			->where('id = :q OR name = :q')
			->addParams([':q' => $q])
			->one()
		;
		return $result;
	}

}
