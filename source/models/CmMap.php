<?php

namespace Source\Models;

use Source\Database\ActiveRecord;

class CmMap extends ActiveRecord
{

    public function __construct($view, $param = null) 
    {
        // $sql = file_get_string_sql($view);
        // if ($param) {
        //     $sql = str_replace($param[0], $param[1], $sql);
        // }
        parent::__construct($view);
    }
    
}