<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';

use Laminas\Config\Factory;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog app_user object
    $app_user = new AppUser($db);

    $data = json_decode(file_get_contents("php://input"));


    $app_user->email_id = $data->email_id;
    $app_user->password = $data->password;

    if ($app_user->checkAppUser()) {
        $app_user->changePasswordByEmail();
        echo json_encode(array("message" => "Password changed successfully."));
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
