<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InterestedUser.php';
include_once __DIR__ . '/../../models/College.php';
include_once __DIR__ . '/../../models/State.php';

if ($_SERVER['REQUEST_METHOD'] == "PATCH") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate interestedUser object
    $interested_user = new InterestedUser($db);
    $college = new College($db);
    $state = new State($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to update
    $interested_user->interested_user_id = $data->interested_user_id;

    $interested_user->first_name = $data->first_name;
    $interested_user->last_name = $data->last_name;
    $interested_user->email_id = $data->email_id;
    $interested_user->mobile_no = $data->mobile_no;

    // Check if college exists
    $college->college_id = $data->college_id;
    $college->read_single();
    if (!$college->name) {
        $college->name = $data->college_name;
        $new_college_id = $college->create();
        if (!$new_college_id) {
            echo json_encode(
                array('message' => 'College record was not created.')
            );
            return;
        } else {
            $interested_user->college_id = $new_college_id;
        }
    } else {
        $interested_user->college_id = $data->college_id;
    }

    // Check if state exists
    $state->state_id = $data->state_id;
    $state->read_single();
    if (!$state->name) {
        echo json_encode(
            array('message' => 'State value not valid.')
        );
        return;
    } else {
        $interested_user->state_id = $data->state_id;
    }


    // Update interestedUser
    if ($interested_user->update()) {
        echo json_encode(
            array('message' => 'Interested User Updated')
        );
    } else {
        echo json_encode(
            array('message' => 'Interested User Not Updated')
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
