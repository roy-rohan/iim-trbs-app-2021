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
    $contactRole = new ContactRole($db);
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $contactRole->contact_role_id = $data->contact_role_id;

    $contactPerson = new ContactPerson($db);
    $contactPerson->poc = $data->poc;
    $contactPerson->email = $data->email;
	$contactPerson->visible = $data->visible;
    $contactRole->read_single();
    if ($contactRole->designation) {
        $contactPerson->contact_role_id = $contactRole->contact_role_id;
        $contactPerson->contact_person_id = $data->contact_person_id;

        if ($contactPerson->update()) {
            echo json_encode(
                array('message' => 'Contact Person Updated')
            );
        }
    } else {
        $contactRole->designation = $data->designation;
        $contactRole->priority = $data->priority;
        $new_contact_role_id = $contactRole->create();
        if ($new_contact_role_id) {
            $contactPerson->contact_role_id = $new_contact_role_id;
            if ($contactPerson->update()) {
                echo json_encode(
                    array('message' => 'Contact Person Updated')
                );
            } else {
                $exp = new CustomException('Contact Person Information not updated.');
                $exp->sendBadRequest();
                exit(1);
            }
        } else {
            $exp = new CustomException('New Contact Role could not be created.');
            $exp->sendBadRequest();
            exit(1);
        }
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
