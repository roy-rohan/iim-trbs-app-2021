<?php
// define("ERROR_HANDLING_STATUS", "PROD");
define("ERROR_HANDLING_STATUS", "DEV");

include_once __DIR__ . "/logger.php";

error_reporting(E_ALL);

function error_handler($errno, $errstr, $errfile, $errline)
{
    // $errstr may need to be escaped:
    $errstr = htmlspecialchars($errstr);
    $error_message_prod = "Unknown error occured. Please contact system administrator.";
    $error_message_dev = "Error $errno occured on line $errline in file $errfile: $errstr.";
    message_logger($error_message_dev);

    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting, so let it fall
        // through to the standard PHP error handler
        return false;
    }

    switch ($errno) {

        case E_USER_ERROR:
            header('HTTP/1.1 500 Internal Server Error');
            echo json_encode(array("message" => "Error occured: $errstr."));
            exit(1);

        default:
            header('HTTP/1.1 500 Internal Server Error');
            if (ERROR_HANDLING_STATUS != "DEV")
                echo json_encode(array("message" => $error_message_prod));
            else
                echo json_encode(array("message" => $error_message_dev));
            exit(1);
    }
    return true;
}

set_error_handler("error_handler");
