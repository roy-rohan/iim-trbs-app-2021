<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Certificate.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog certificate object
    $certificate = new Certificate($db);

    // Get ID
    $certificate->certificate_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get certificate
    $certificate->read_single();

    if (!$certificate->created_at) {
        echo json_encode(array('message' => 'No Certificate Found'));
    } else {
        $certificate_arr = generateResponseArray($certificate, []);
        echo json_encode($certificate_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
