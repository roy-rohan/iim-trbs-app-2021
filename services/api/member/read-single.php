<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Member.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog member object
    $member = new Member($db);

    // Get ID
    $member->member_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get member
    $member->read_single();

    if (!$member->created_at) {
        echo json_encode(array('message' => 'No Member Found'));
    } else {
        $member_arr = generateResponseArray($member, []);
        echo json_encode($member_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
