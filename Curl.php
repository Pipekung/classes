<?php

namespace pipekung\classes;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Curl {

	CONST RETURNTRANSFER = true;
	CONST FOLLOWLOCATION = true;
	CONST TIMEOUT = 3;
	CONST SSL_VERIFYPEER = false;
	CONST SSL_VERIFYHOST = false;

	public function __construct() {

	}

	public static function getData($url, $type='GET', $data=[], $options=[]) {
        $jsonData = json_encode($data);
        $ch = curl_init();
        $options = !empty($options) ? $options : [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => self::RETURNTRANSFER,
            CURLOPT_FOLLOWLOCATION => self::FOLLOWLOCATION,
            CURLOPT_TIMEOUT => self::TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => self::SSL_VERIFYPEER,
            CURLOPT_SSL_VERIFYHOST => self::SSL_VERIFYHOST,
            CURLOPT_CUSTOMREQUEST => $type,
            CURLOPT_HTTPHEADER => empty($data) ? [] : [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData)
            ],
        ];
        curl_setopt_array($ch, $options);
        
        $contentData = curl_exec($ch);
        $error = curl_error($ch);

        curl_close($ch);
        return empty($error) ? $contentData : $error;
    }

}