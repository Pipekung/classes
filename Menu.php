<?php
namespace pipekung\classes;

use Yii;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Menu {
    
    public static function Active($name, $id=[], $unactive=[]) {
    	if (in_array(Yii::$app->controller->id, $unactive) || in_array(Yii::$app->controller->module->id, $unactive)) return false;

    	if (Yii::$app->controller->module->id==$name && empty($id)) return true;
    	elseif (Yii::$app->controller->module->id==$name && in_array(Yii::$app->controller->id, $id)) return true;
    	elseif (Yii::$app->controller->id==$name && empty($id)) return true;
    	elseif (Yii::$app->controller->id==$name && in_array(Yii::$app->controller->action->id, $id)) return true;

        return false;
    }
    
}
