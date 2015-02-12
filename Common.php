<?php
namespace pipekung\classes;

use Yii;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Common {
    
    public static function getClassName($model) {
        $model = get_class($model);
        $model = explode('\\', $model);
        $model = $model[count($model)-1];
        return strtolower($model);
    }
    
    public static function getFiscalYear() {
    	return (date('m') >= 10) ? (date('Y') + 544) : (date('Y') + 543);
    }

}
