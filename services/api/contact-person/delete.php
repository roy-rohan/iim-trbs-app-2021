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

    $contactPerson_model = new ContactPerson($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $contactPerson_model->contact_person_id = $data->contact_person_id;
    $contactPerson_model->read_single();
    if ($contactPerson_model->poc) {
        if ($contactPerson_model->delete()) {
            echo json_encode(array('message' => "Contact Person Deleted."));
        } else {
            $exp = new CustomException('Contact Person information could not be deleted.');
            $exp->sendBadRequest();
            exit(1);
        }
    } else {
        $exp = new CustomException('Contact Person information not found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
