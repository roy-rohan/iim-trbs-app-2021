<?php

function message_logger($message)
{
    $log  = "-------------------------" . PHP_EOL .
        "time: " . date("F j, Y, g:i a") . PHP_EOL .
        "message: " . $message . PHP_EOL .
        "-------------------------" . PHP_EOL;
    file_put_contents(__DIR__ . '/../logs/log_' . date("j.n.Y") . '.log', $log, FILE_APPEND);
}


trait Logger
{
    function messageLogger($message)
    {
        message_logger($message);
    }
}
