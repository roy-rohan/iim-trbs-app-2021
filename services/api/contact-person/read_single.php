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

if ($_SERVER['REQUEST_METHOD'] == "GET") {

    $contactRole_model = new ContactRole($db);
    $contactPerson_model = new ContactPerson($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $contactPerson_model->contact_person_id =
        isset($_GET['id']) ? $_GET['id'] : die();;
    $contactPerson_model->read_single();
    if ($contactPerson_model->contact_person_id) {
        $contactRole_model->contact_role_id = $contactPerson_model->contact_role_id;
        $contactRole_model->read_single();
        if ($contactRole_model->designation) {
            $contactPersonResponse = array(
                'contact_person_id' => $contactPerson_model->contact_person_id,
                'contact_role_id' => $contactRole_model->contact_role_id,
                'poc' => $contactPerson_model->poc,
                'designation' => $contactRole_model->designation,
                'priority' => $contactRole_model->priority,
                'email' => $contactPerson_model->email,
				'visible' => $contactPerson_model->visible
            );
            echo json_encode($contactPersonResponse);
        } else {
            $exp = new CustomException('Contact Person Information not found.');
            $exp->sendBadRequest();
            exit(1);
        }
    } else {
        $exp = new CustomException('Contact Person Information not found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
