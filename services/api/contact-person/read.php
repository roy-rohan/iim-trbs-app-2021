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

    $contactRole_model = new ContactRole($db);
    $contactPerson_model = new ContactPerson($db);
    // (object)array("filters" => array((object)array("field_name" => 'order_id', "value" => $order_model->order_id, "op" => "=")), "filter_op" => "AND", "sort" => array())
    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $result = $contactPerson_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Users array
        $contactPerson_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $contactRole_model->contact_role_id = $row['contact_role_id'];
            $contactRole_model->read_single();
            if ($contactRole_model->designation) {
                $contactPerson = array(
                    'contact_person_id' => $row['contact_person_id'],
                    'poc' => $row['poc'],
                    'email' => $row['email'],
                    'contact_role_id' => $row['contact_role_id'],
                    'designation' => $contactRole_model->designation,
                    'priority' => $contactRole_model->priority
                );
            }
            // Push to "data"
            array_push($contactPerson_arr, $contactPerson);
        }

        message_logger("Contact Role data fetched.");
        // Turn to JSON & output
        echo json_encode($contactPerson_arr);
    } else {
        // No Users
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
