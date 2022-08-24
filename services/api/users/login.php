<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . "/../../auth/token_utils.php";
include_once __DIR__ . "/../../services/mail/ProfileActivation/ProfileActivation.php";

use Laminas\Config\Factory;

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $sql = 'SELECT app_user_id, first_name, password, email_validated, email_id
        FROM   app_user
        WHERE  login_id = :login_id
        AND is_active = :is_active';

    $stmt = $db->prepare($sql);
    $stmt->bindParam(":login_id", $data->login_id);
    $active_flag = 1;
    $stmt->bindParam(":is_active", $active_flag);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        $exp = new CustomException('Something went wrong.' . $e);
        $exp->sendBadRequest();
        exit(1);
    }


    $rs = $stmt->fetch();

    $config = Factory::fromFile('../../auth/config.php', true);

    if ($rs) {

        if($rs['email_validated'] == 0) {
            $profileActivationLink = generateProfileActivationLink($rs['app_user_id']);
            $mail = new ProfileActivation();
            $mail->setTo($rs['email_id'], $rs['first_name']);
            $mail->setFrom($config->get('tenantEmailId'), $config->get('tenantEmailName'));
            $mail->setActivationLink($profileActivationLink);
            $mail->setName($rs['first_name']);
            $mail->sendMail();

            $exp = new CustomException('Account not active. Click the verification link that has been sent to your email id to activate.');
            $exp->sendUnauthorizedRequest();
            exit(1);
        }

        if (password_verify($data->password, $rs['password'])) {
            $data = [
                "user_id" => $rs['app_user_id'],
                "first_name" => $rs['first_name']
            ];
            $token = encodeToken($data);
            $response = ['token' => $token, 'message' => 'login successfull'];
            echo json_encode($response);
        } else {
            $exp = new CustomException('Username/Password not valid.');
            $exp->sendUnauthorizedRequest();
            exit(1);
        }
    } else {
        $exp = new CustomException('Username/Password not valid.');
        $exp->sendUnauthorizedRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
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
