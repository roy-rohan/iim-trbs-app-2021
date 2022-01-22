<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Member.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate member object
    $member = new Member($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $member = copyObject(
        $data,
        $member,
        [
            "image_url",
            "member_id", "created_at",
            "updated_at"
        ]
    );

    // Create member
    $new_member_id = $member->create();
    if ($new_member_id) {
        echo json_encode(
            array('message' => 'Member Created', 'id' => $new_member_id)
        );
    } else {
        $exp = new CustomException('Member not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
