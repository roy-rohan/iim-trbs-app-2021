<?php

include_once __DIR__ . "/../../utilities/logger.php";

class CustomException
{

    private $exp_code;
    private $exp_message;

    // Constructor with DB
    public function __construct($message)
    {
        $this->exp_message = $message;
    }

    private function sendResponse()
    {
        header("HTTP/1.1 $this->exp_code");
        message_logger($this->exp_message);
        echo json_encode(array("message" => $this->exp_message));
        exit(1);
    }

    function sendServerException()
    {
        $this->exp_code = "500 Internal Server Error";
        $this->sendResponse();
    }


    function sendUnauthorizedRequest()
    {
        $this->exp_code = "401 Unauthorized";
        $this->sendResponse();
    }

    function sendForbiddenRequest()
    {
        $this->exp_code = "403 Forbidden";
        $this->sendResponse();
    }

    function sendBadRequest()
    {
        $this->exp_code = "404 Not Found";
        $this->sendResponse();
    }

    function sendMethodNotAllowedRequest()
    {
        $this->exp_code = "405 Method Not Allowed";
        $this->sendResponse();
    }
}
