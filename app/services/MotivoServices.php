<?php

use Source\Models\CmMap;

class MotivoService
{
    private static $view = 'motivots';

    public static function import()
    {
        $cm_data = new CmMap(self::$view);
        return $cm_data->all();
    }
}