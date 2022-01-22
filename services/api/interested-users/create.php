<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/College.php';
include_once __DIR__ . '/../../models/State.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate interested_user object
    $interested_user = new InterestedUser($db);
    $college = new College($db);
    $state = new State($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $interested_user->first_name = $data->first_name;
    $interested_user->last_name = $data->last_name;
    $interested_user->email_id = $data->email_id;
    $interested_user->mobile_no = $data->mobile_no;
    $interested_user->event_name = $data->event_name;

    $college->college_id = $data->college_id;
    if ($college->college_id != null) {
        // Check if college exists
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
    }

    $state->state_id = $data->state_id;
    if ($state->state_id != null) {
        // Check if state exists
        $state->read_single();
        if (!$state->name) {
            echo json_encode(
                array('message' => 'State value not valid.')
            );
            return;
        } else {
            $interested_user->state_id = $data->state_id;
        }
    }

    // Create interested_user
    $new_user_id = $interested_user->create();
    if ($new_user_id) {
        echo json_encode(
            array('message' => 'User Created', 'id' => $new_user_id)
        );
    } else {
        echo json_encode(
            array('message' => 'User Not Created')
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
