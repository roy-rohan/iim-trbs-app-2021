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

    $data = json_decode(file_get_contents("php://input"));

    // Get token
    authenicateRequest($data->token);

    $app_user->app_user_id = $data->user_id;
    $app_user->password = $data->old_password;

    if ($app_user->verifyPassword()) {
        $app_user->password = $data->new_password;
        $app_user->changePassword();
        echo json_encode(array("message" => "Password changed successfully."));
    } else {
        $exp = new CustomException('Password did not match.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
