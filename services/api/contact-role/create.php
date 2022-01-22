<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/ContactRole.php';
include_once __DIR__ . '/../../models/ContactPerson.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate ContactRole object
    $contactRoleModel = new ContactRole($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $contactRoleModel->designation = $data->designation;
    $contactRoleModel->priority = $data->priority;
    $new_contact_role_id = $contactRoleModel->create();

    if ($new_contact_role_id) {
        echo json_encode(
            array('message' => 'Contact Role Added', 'id' => $new_contact_role_id)
        );
    } else {
        $exp = new CustomException('Contact Role record not created.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
