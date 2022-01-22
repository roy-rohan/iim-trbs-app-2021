<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();
    // Instantiate blog app_user object
    $app_user = new AppUser($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // extract user_id from the token
    try {
        $userData = decodeToken($data->token);
        $app_user->email_id = $userData->email_id;
        if ($app_user->checkAppUser()) {
            echo json_encode(array('message' => 'Link is validated successfully.', 'email_id' => $userData->email_id));
        } else {
            $exp = new CustomException('Something went wrong.');
            $exp->sendBadRequest();
            exit(1);
        }
    } catch (Exception $error) {
        $exp = new CustomException('Something went wrong.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
