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
        $app_user->read_by_email();

        $config = Factory::fromFile('../../auth/config.php', true);

        if ($app_user->email_validated == 0) {
            $mail = new ProfileActivation();
            $link = generateProfileActivationLink($app_user->app_user_id);
            $mail->setTo($app_user->email_id, $app_user->first_name);
            $mail->setActivationLink($link);
            $mail->setName($app_user->first_name);
            $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
            $mail->sendMail();

            $exp = new CustomException('Account not active. Click the verification link that has been sent to your email id to activate.');
            $exp->sendUnauthorizedRequest();
        } else {
            $mail = new ForgotPassword();
            $link = generatePasswordChangeLink($app_user->email_id);
            $mail->setTo($app_user->email_id, '');
            $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
            $mail->setActionLink($link);
            $mail->sendMail();

            echo json_encode(array("message" => "Mail sent for password change."));
        }
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

function generatePasswordChangeLink($email_id)
{
    $data = ["email_id" => $email_id];
    $token = encodeToken($data);
    $config = Factory::fromFile('../../auth/config.php', true);
    return $config->get('frontendUrl') . "/vpcl?token=" . $token;
}

function generateProfileActivationLink($user_id)
{
    $data = ["user_id" => $user_id];
    $token = encodeToken($data);
    $config = Factory::fromFile('../../auth/config.php', true);
    // TO BE REMOVED IN PROD
    // return "http://localhost/iim-app-services/api/users/activate.php?token=$token";
    return $config->get('frontendUrl') . "/user/activate?token=" . $token;
}
