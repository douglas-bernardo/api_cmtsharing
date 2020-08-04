<?php

use Source\Models\CmMap;

class OcorrenciaService
{
    /**
     * Provide view file name stored on /resources
     *
     * @var string
     */    
    private static $view = 'cm_ocorrencias_ts_renegociacao_app';

    /**
     * That property provide what fields will be fetched from cm view;
     *
     * @var array
     */
    private static $fields = [
        "idvendaxcontrato",
        "idvendats",
        "numero_ocorrencia", 
        "status", 
        "ts_motivo_id", 
        "dtocorrencia",
        "ts_cliente_id",
        "nome_cliente",
        "numero_projeto",
        "numero_contrato",
        "nome_projeto",
        "valor_venda_view",
        "ts_usuario_resp_id",
        "ts_usuario_resp_nome"
    ];

    public static function import()
    {
        $cm_data = new CmMap(self::$view);
        $cm_data->setFields(self::$fields);
        return $cm_data->all();
    }
}