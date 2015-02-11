<?php
namespace pipekung\classes;

use Yii;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Menu {
    
    public static function Active($name, $id='*') {
    	if (Yii::$app->controller->module->id==$name && $id=='*') return true;
    	elseif (Yii::$app->controller->module->id==$name && Yii::$app->controller->id==$id) return true;
        return false;
    }
    
}
