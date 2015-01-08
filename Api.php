<?php

namespace pipekung\classes;

use Yii;
use pipekung\classes\Curl;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Api {
    
    public static function getCitizenId($username) {
	    $apiKey = Yii::$app->params['apiKey'];
	    $url = Yii::$app->params['getCitizenIdUrl'] . "/{$apiKey}/{$username}";
	    $data = json_decode(Curl::getData($url));
	    return empty($data->citizenId) ? null : $data->citizenId;
    }
    
}
