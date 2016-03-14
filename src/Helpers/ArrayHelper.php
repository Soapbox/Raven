<?php

namespace SoapBox\Raven\Helpers;

class ArrayHelper
{
    public static function get(array $array, $key, $default = null)
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }
}
