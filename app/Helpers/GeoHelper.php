<?php

namespace App\Helpers;

class GeoHelper
{
    // خوارزمية Ray Casting لفحص هل النقطة داخل مضلع
    public static function isPointInPolygon($point, $polygon): bool
    {
        $x = $point['lat'];
        $y = $point['lng'];

        $inside = false;
        $count = count($polygon);

        for ($i = 0, $j = $count - 1; $i < $count; $j = $i++) {

            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];

            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $y) != ($yj > $y)) &&
                ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 0.00000001) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }
        }

        return $inside;
    }
}