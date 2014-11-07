<?php
namespace pipekung\classes;

use Yii;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Menu {
    
    public static function Active($id) {
        return (Yii::$app->controller->id==$id);
    }
    
}
