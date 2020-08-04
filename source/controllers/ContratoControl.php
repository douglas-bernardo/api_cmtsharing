<?php

namespace Source\Controllers;

use Source\Models\CmMap;

class ContratoControl
{
    
    public function getData($param)
    {
        $cm_data = new CmMap('cm_resumo_financeiro_dtvenda_app', array('PARAM_IDVENDAXCONTRATO', $param['idvendaxcontrato']));
        return $cm_data->load();
    }

    public static function test()
    {
        return "Test API REST Successful";
    }
}