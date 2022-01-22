<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';
include_once __DIR__ . "/../../services/mail/ForgotPassword/ForgotPassword.php";

use Laminas\Config\Factory;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog app_user object
    $app_user = new AppUser($db);

    $data = json_decode(file_get_contents("php://input"));


    $app_user->email_id = $data->email_id;

    if ($app_user->checkAppUser()) {
        $config = Factory::fromFile('../../auth/config.php', true);
        // send the confirmation email
        // $newUserPassword =
        //     uniqid("PASS");
        // $app_user->password = $newUserPassword;
        // $app_user->changePasswordByEmail();
        $link = generateProfileActivationLink($app_user->email_id);
        $mail = new ForgotPassword();
        $mail->setTo($app_user->email_id, '');
        $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
        $mail->setPasswordChangeLink($link);
        $mail->sendMail();
        echo json_encode(array("message" => "Mail sent for password change."));
    } else {
        header("HTTP/1.1 401");
        $expMessage = "User not found with given email id.";
        message_logger($expMessage);
        echo json_encode(array("message" => $expMessage));
        exit(1);
    }
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    $expMessage = $_SERVER['REQUEST_METHOD'] . " method not allowed.";
    message_logger($expMessage);
    echo json_encode(array("message" => $expMessage));
    exit(1);
}

function generateProfileActivationLink($email_id)
{
    $data = ["email_id" => $email_id];
    $token = encodeToken($data);
    $config = Factory::fromFile('../../auth/config.php', true);
    return $config->get('frontendUrl') . "/vpcl?token=" . $token;
}
