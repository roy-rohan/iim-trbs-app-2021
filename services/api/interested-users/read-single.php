<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InterestedUser.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog interestedUser object
    $interestedUser = new InterestedUser($db);

    // Get ID
    $interestedUser->interested_user_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get interestedUser
    $interestedUser->read_single();

    if (!$interestedUser->email_id) {
        echo json_encode(array('message' => 'No User Found'));
    } else {
        $interested_user_arr = array(
            'interested_user_id' => $interestedUser->interested_user_id,
            'first_name' => $interestedUser->first_name,
            'last_name' => $interestedUser->last_name,
            'email_id' => $interestedUser->email_id,
            'mobile_no' => $interestedUser->mobile_no,
            'state_id' => $interestedUser->state_id,
            'state' => $interestedUser->state,
            'college_id' => $interestedUser->college_id,
            'college' => $interestedUser->college,
            'event_name' => $interestedUser->event_name,
            'created_at' => $interestedUser->created_at,
            'updated_at' => $interestedUser->updated_at
        );

        echo json_encode($interested_user_arr);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
