<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . "/../../auth/token_utils.php";

use Laminas\Config\Factory;

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $data = json_decode(file_get_contents("php://input"));

    $sql = 'SELECT app_user_id, first_name, password
        FROM   app_user
        WHERE  login_id = :login_id AND is_active = :is_active 
        AND email_validated = :email_validated';

    $stmt = $db->prepare($sql);
    $stmt->bindParam(":login_id", $data->login_id);
    $activeFlag = 1;
    $stmt->bindParam(":is_active", $activeFlag);
    $emailValidatedFlag = 1;
    $stmt->bindParam(":email_validated", $emailValidatedFlag);

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
