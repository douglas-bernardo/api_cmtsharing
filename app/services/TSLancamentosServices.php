<?php

use Source\Models\CmMap;

class TSLancamentosServices
{
    private static $view = 'cm_resumo_fin_parcelas_app';

    public static function getData($param)
    {
        if (isset($param['idvendats'])) {
            $cm_data = new CmMap(self::$view, ['PARAM_IDVENDATS', $param['idvendats']]);
            $data = $cm_data->all();
            return $data;
        } else {
            return "Error: parametro não informado ou inválido";
        }
    }
}