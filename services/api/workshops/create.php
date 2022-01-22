<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Workshop.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate workshop object
    $workshop = new Workshop($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $workshop = copyObject(
        $data,
        $workshop,
        [
            "image_url",
            "workshop_id", "created_at",
			"timeline_image_url",
            "updated_at"
        ]
    );

    // Create workshop
    $new_workshop_id = $workshop->create();
    if ($new_workshop_id) {
        echo json_encode(
            array('message' => 'workshop Created', 'id' => $new_workshop_id)
        );
    } else {
        $exp = new CustomException('workshop not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
