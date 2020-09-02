<?php

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Source\Models\CmMap;

class MotivoServices
{
    
    /**
     * Provide view file name stored on /resources
     *
     * @var string
     */    
    const VIEW_NAME = 'cm_motivots';

    public static function getData()
    {
        $view = file_get_string_sql(self::VIEW_NAME);
        $filter = null;
        $cm_data = new CmMap($view);
        $data = $cm_data->all();

        if (isset($data->exception)) {
            $logger = new Logger('motivo_services');
            $logger->pushHandler(
                new StreamHandler(__DIR__ . '/../../tmp/wser_cm_motivo_service.txt',
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