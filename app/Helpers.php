<?php

namespace App;

class Helpers 
{
    public static function percentage(Float $target, Float $total)
    {
        if(($total - $target) >= 0) {
            $percentage = ($target/$total) * 100;
            return [
                "success" => true,
                "result" => $percentage
            ];
        }else{
            return [
                "success" => false,
                "message" => "The total cannot be less than the target"
            ];
        }
    }

    public static function percentageDiff(Float $first, Float $second)
    {
        $diff = $first - $second;
        if($diff > 0) {
            return self::percentage($diff, $first);
        }else{
            return [
                "success" => false,
                "message" => "The $first cannot be less than the $second"
            ];
        }
    }
}

?>