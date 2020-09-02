<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Source\Models\CmMap;

/**
 * Este serviço retorna as ocorrências abertas no timesharing direcionadas
 * ao departamento de Renegociação Vacation. A consulta é realizada com base
 * em uma view e pode ser costumizada conforme necessidade. 
 */
class OcorrenciaServices
{
    /**
     * Provide view file name stored on /resources
     *
     * @var string
     */    
    const VIEW_NAME = 'cm_ocorrencias_ts_renegociacao_app';

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

    /**
     * Existem dois modos principais de consulta:
     * Se o parâmetro last_ocorrencia_id é fornecido, serão retornadas todas as ocorrências
     * cujo id seja maior que o valor do parâmetro last_ocorrencia_id.
     * Se o parâmetro dtocorrencia é fornecido, serão retornadas as ocorrências cuja data de
     * criação seja maior ou igual a data fornecida no parâmetro.
     *
     * @param mixed $param
     * @return array|object
     * @throws Exception
     */
    public static function getData($param)
    {
        if (isset($param['last_ocorrencia_id']) && !empty($param['last_ocorrencia_id'])) {
            $value    = $param['last_ocorrencia_id'];
            $filter = 'AND O.IDOCORRENCIA > ' . $value;
        } elseif (isset($param['dtocorrencia']) && !empty($param['dtocorrencia'])) {
            $value    = $param['dtocorrencia'];
            $filter  = "AND TO_DATE(TO_CHAR(O.DTOCORRENCIA,'dd/mm/yyyy'), 'dd/mm/yyyy') >= ";
            $filter .= "TO_DATE('{$value}', 'dd/mm/yyyy')";
        } else {
            return ['error' => 'parameter not informed or invalid', 'data' => null];
        }

        $view = file_get_string_sql(self::VIEW_NAME);
        $cm_data = new CmMap($view);
        $cm_data->setFields(self::$fields);
        $cm_data->setParameter( 'PARAM_FILTER', $filter );
        $data = $cm_data->all();
        
        if (isset($data->exception)) {
            $logger = new Logger('ocorrencia_service');
            $logger->pushHandler(
                new StreamHandler(__DIR__ . '/../../tmp/wser_cm_ocorrencia_service.txt',
                Logger::DEBUG)
            );           
            $logger->info('Import error', ['description' => $data]);
        }

        return [
            'total' => $cm_data->getTotal(),
            'filter_view' => $filter,
            'data' => $data 
        ];
    }
}