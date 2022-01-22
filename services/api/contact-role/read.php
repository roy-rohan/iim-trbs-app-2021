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

    $result = $contactRole_model->read($data);

    // Get row count
    $num = $result->rowCount();

    // Check if any user exists
    if ($num > 0) {
        // Users array
        $contactRole_arr = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $contactPersonResult = $contactPerson_model->read((object)array("filters" => array((object)array("field_name" => 'contact_role_id', "value" => $row['contact_role_id'], "op" => "=")), "filter_op" => "AND", "sort" => array()));

            // Get row count
            $contactPersonResultNum = $contactPersonResult->rowCount();

            $contactPerson_arr = array();
            if ($contactPersonResultNum > 0) {
                while ($contactPersonRow = $contactPersonResult->fetch(PDO::FETCH_ASSOC)) {
                    $contactPerson = array(
                        'contact_person_id' => $contactPersonRow['contact_person_id'],
                        'poc' => $contactPersonRow['poc'],
                        'email' => $contactPersonRow['email'],
						'visible' => $contactPersonRow['visible']
                    );
                    array_push($contactPerson_arr, $contactPerson);
                }
            }

            $contactRole = array(
                'contact_role_id' => $row['contact_role_id'],
                'designation' => $row['designation'],
                'priority' => $row['priority'],
                'contact_people' => $contactPerson_arr,
            );

            // Push to "data"
            array_push($contactRole_arr, $contactRole);
        }

        message_logger("Contact Role data fetched.");
        // Turn to JSON & output
        echo json_encode($contactRole_arr);
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
