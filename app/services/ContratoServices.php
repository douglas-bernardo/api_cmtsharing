<?php

use Source\Models\CmMap;

class ContratoServices
{
    private static $view = 'cm_resumo_financeiro_dtvenda_app';

    public static function getData($param)
    {
        // $parameters = [];
        // $parameters['idvendaxcontrato'] = 128386;
        if (isset($param['idvendaxcontrato'])) {
            $cm_data = new CmMap(self::$view, ['PARAM_IDVENDAXCONTRATO', $param['idvendaxcontrato']]);
            $data = $cm_data->load();
            return $data;
        } else {
            return "Error: parametro não informado ou inválido";
        }

    }

}
