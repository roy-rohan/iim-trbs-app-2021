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
    $contactRole_model = new ContactRole($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $contactRole_model->contact_role_id = $data->contact_role_id;
    $contactRole_model->read_single();
    if ($contactRole_model->designation) {
        $result = $contactPerson_model->read((object)array("filters" => array((object)array("field_name" => 'contact_role_id', "value" => $contactRole_model->contact_role_id, "op" => "=")), "filter_op" => "AND", "sort" => array()));
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $contactPerson_model->contact_person_id = $row['contact_person_id'];
            $contactPerson_model->delete();
        }
        if ($contactRole_model->delete()) {
            echo json_encode(array('message' => "Contact Role Deleted."));
        } else {
            $exp = new CustomException('Contact Role could not be deleted.');
            $exp->sendBadRequest();
            exit(1);
        }
    } else {
        $exp = new CustomException('Contact Role not found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
