<?php

use Source\Models\CmMap;

class OcorrenciaServices
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
        "numero_ocorrencia",
        "idvendats", 
        "idvendaxcontrato",        
        "status",
        "idmotivots", 
        "dtocorrencia",
        "idpessoa_cliente",
        "nome_cliente",
        "numeroprojeto",
        "numerocontrato",
        "nomeprojeto",
        "valor_venda",
        "idusuario_resp",
        "nomeusuario_resp",
        "idusuario_cadastro",
        "nomeusuario_cadastro"
    ];

    public static function getData()
    {
        $cm_data = new CmMap(self::$view);
        $cm_data->setFields(self::$fields);
        return $cm_data->all();
    }
}