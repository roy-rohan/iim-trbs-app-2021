<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Headers: Authorization');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/User/AppUser.php';
include_once __DIR__ . '/../../auth/token_utils.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    $data = json_decode(file_get_contents("php://input"));
    try {
        $user_data = decodeToken($data->token);
    } catch (Exception $e) {
        $exp = new CustomException($e->getMessage());
        $exp->sendBadRequest();
        exit(1);
    }

    echo json_encode(array("message" => "OK"));
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
