<?php
namespace pipekung\classes;

use Yii;

/**
 * @author Pipekung Specialz <chanja@kku.ac.th>
 * @since 2.0
 */
class Date {
    
    public static function conv2th($date) {
        str_replace('/', '-', $date);
        if ($date == '0000-00-00') return $date;
        list($year, $month, $day) = explode('-', $date);
        
        return "{$day}-{$month}-". ($year+543);
    }

    public static function conv2thmonth($date, $monthFormat = 'short') {
        $thaiMonth = ($monthFormat == 'short') 
        ? array(
            "0" => "",
            "01" => "ม.ค.",
            "02" => "ก.พ.",
            "03" => "มี.ค.",
            "04" => "เม.ย.",
            "05" => "พ.ค.",
            "06" => "มิ.ย.",
            "07" => "ก.ค.",
            "08" => "ส.ค.",
            "09" => "ก.ย.",
            "10" => "ต.ค.",
            "11" => "พ.ย.",
            "12" => "ธ.ค."
        )
        : array(
            "0" => "",
            "01" => "มกราคม",
            "02" => "กุมภาพันธ์",
            "03" => "มีนาคม",
            "04" => "เมษายน",
            "05" => "พฤษภาคม",
            "06" => "มิถุนายน",
            "07" => "กรกฎาคม",
            "08" => "สิงหาคม",
            "09" => "กันยายน",
            "10" => "ตุลาคม",
            "11" => "พฤศจิกายน",
            "12" => "ธันวาคม"
        );
        str_replace('/', '-', $date);
        if ($date == '0000-00-00') return $date;
        list($year, $month, $day) = explode('-', date('Y-m-d', strtotime($date)));

        return ($day*1)." ".$thaiMonth[$month]." ".($year+543);
    }
    
    public static function conv2en($date) {
        str_replace('/', '-', $date);
        if ($date == '0000-00-00') return $date;
        list($day, $month, $year) = explode('-', $date);

        return ($year-543) . "-{$month}-{$day}";
    }
    
}
