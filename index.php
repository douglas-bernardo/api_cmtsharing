<?php

//para uso em servidor webpack
// header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Headers: Content-Type");
//****** */

header('Content-type: application/json; charset=utf-8');

require __DIR__ . '/vendor/autoload.php';

class RestServer
{
    public static function run($request)
    {
        $class = isset($request['class']) ? $request['class'] : '';
        $method = isset($request['method']) ? $request['method'] : '';
        $response = null;
        try {
            if (class_exists($class)) {
                if (method_exists($class, $method)) {
                    $response = call_user_func(array($class, $method), $request);
                    $total  = (isset($response['total']))       ? $response['total'] : null;
                    $filter = (isset($response['filter_view'])) ? $response['filter_view'] : null;
                    $error  = (isset($response['error']))       ? $response['error'] : null;

                    return json_encode(
                        array('status' => 'success',
                            'environment' => CONF_ENVIRONMENT,
                            'total' => $total,
                            'filter_view' => $filter,
                            'error' => $error,
                            'data' => $response['data']
                        )
                    );

                } else {
                    $error_msg = "Method {$class}::{$method} not found";
                    return json_encode(
                        array('status' => 'error',
                              'environment' => CONF_ENVIRONMENT,
                              'data' => $error_msg)
                    );
                }

            } else {
                $error_msg = "Class {$class} not found";
                return json_encode(array('status' => 'error', 'environment' => CONF_ENVIRONMENT, 'data' => $error_msg));
            }

        } catch (Exception $e) {
            return json_encode(array('status' => 'error', 'environment' => CONF_ENVIRONMENT, 'data' => $e->getMessage()));
        }
    }
}

print RestServer::run($_REQUEST);