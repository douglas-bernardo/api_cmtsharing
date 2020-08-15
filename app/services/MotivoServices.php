<?php

use Source\Models\CmMap;

class MotivoServices
{
    private static $view = 'motivots';

    public static function getData()
    {
        $cm_data = new CmMap(self::$view);
        return $cm_data->all();
    }
}