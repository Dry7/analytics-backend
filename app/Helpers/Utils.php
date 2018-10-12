<?php

declare(strict_types=1);

namespace App\Helpers;

class Utils
{
    public static function string2null($val)
    {
        return $val == ''  ? null : $val;
    }
}