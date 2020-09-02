<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Source\Models\CmMap;

/**
 * Este serviço retorna as informações relativas as parcelas de contratos no TS
 * de acor
 */
class TSLancamentosServices
{

    /**
     * Provide view file name stored on /resources
     *
     * @var string
     */    
    const VIEW_NAME = 'cm_resumo_fin_parcelas_app';

    public static function getData($param)
    {
        if (isset($param['idvendats'])) {

            $filter = 'V.IDVENDATS = ' . $param['idvendats']; 

        } else {
            return ['error' => 'parameter not informed or invalid', 'data' => null];
        }

        $view = file_get_string_sql(self::VIEW_NAME);
        $cm_data = new CmMap($view);
        $cm_data->setParameter('PARAM_FILTER', $filter);
        $data = $cm_data->all();
        
        if (isset($data->exception)) {
            $logger = new Logger('lancamentos_service');
            $logger->pushHandler(
                new StreamHandler(__DIR__ . '/../../tmp/wser_cm_lancamentos_service.txt', 
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