<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';
include_once __DIR__ . "/../../services/mail/ContactUs/ContactUs.php";

use Laminas\Config\Factory;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
    // Instantiate blog app_user object
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    try {
        $config = Factory::fromFile('../../auth/config.php', true);
        $mail = new ContactUs();
        $mail->setTo($config->get('tenantEmailId'), $config->get('tenantEmailName'));
        $mail->setFrom($data->email, $data->name);
        $mail->setName($data->name);
        $mail->setEmail($data->email);
        $mail->setMobile($data->mobile);
        $mail->setSubject($data->subject);
        $mail->setMessage($data->message);
        $mail->sendMail();
        echo json_encode(
            array('message' => 'Mail sent successfully.')
        );
    } catch (Exception $e) {
        $exp = new CustomException('Something went wrong.');
        message_logger($e->getMessage());
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
