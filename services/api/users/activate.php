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
    $userData = decodeToken($data->token);
    $app_user->app_user_id = $userData->user_id;
    if ($app_user->activateUser()) {
        $data = [
            "user_id" => $userData->user_id,
        ];
        $token = encodeToken($data);
        echo json_encode(array('token' => $token, 'message' => 'Account has been activated.'));
    } else {
        $exp = new CustomException('Something went wrong.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
