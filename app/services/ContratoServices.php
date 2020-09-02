<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Source\Models\CmMap;

class ContratoServices
{

    /**
     * Provide view file name stored on /resources
     *
     * @var string
     */    
    const VIEW_NAME = 'cm_resumo_financeiro_dtvenda_app';

    public static function getData($param)
    {
        if (isset($param['idvendaxcontrato']) && !empty($param['idvendaxcontrato'])) {
            $filter = 'VC.IDVENDAXCONTRATO = ' . $param['idvendaxcontrato'];
        } else {
            return ['error' => 'parameter not informed or invalid', 'data' => null];
        }

        $view = file_get_string_sql(self::VIEW_NAME);
        $cm_data = new CmMap($view);
        $cm_data->setParameter( 'PARAM_FILTER', $filter );
        $data = $cm_data->load();

        if (isset($data->exception)) {
            $logger = new Logger('contrato_services');
            $logger->pushHandler(
                new StreamHandler(__DIR__ . '/../../tmp/wser_cm_contrato_services.txt',
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
