<?php

require_once __DIR__ . '/vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$log = new Logger('app');
$log->pushHandler(new StreamHandler('logs/app.log', Logger::WARNING));

class ErrorHandler {
    public static function handle($error, $message = null, $file = null, $line = null) {
        $response = [
            'status' => 'error',
            'message' => $message ?? $error->getMessage(),
            'code' => $error->getCode() ?? 500
        ];

        if (getenv('APP_DEBUG') === 'true') {
            $response['debug'] = [
                'file' => $file ?? $error->getFile(),
                'line' => $line ?? $error->getLine(),
                'trace' => $error->getTrace()
            ];
        }

        header('Content-Type: application/json');
        http_response_code($response['code']);
        echo json_encode($response);
        exit;
    }
}

set_error_handler([ErrorHandler::class, 'handle']);
set_exception_handler([ErrorHandler::class, 'handle']);
// register_shutdown_function(function() {
//     $error = error_get_last();       
//     if ($error) {
//         ErrorHandler::handle(new Exception($error['message'], $error['type']), $error['message'], $error['file'], $error['line']);
//     }
// });
//
// register_shutdown_function(function() {
//     $error = error_get_last();
//     if ($error) {
//         ErrorHandler::handle(new Exception($error['message'], $error['type']), $error['message'], $error['file'], $error['line']);
//     }
// });
//
// set_exception_handler(function($exception) {
//     ErrorHandler::handle($exception);
// });
//
?>