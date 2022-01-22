<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/InterestedUser.php';


if ($_SERVER['REQUEST_METHOD'] == "DELETE") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate interested_user object
    $interested_user = new InterestedUser($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $interested_user->interested_user_id = $data->id;

    // Delete interested_user
    if ($interested_user->delete()) {
        echo json_encode(
            array('message' => 'Interested User Deleted')
        );
    } else {
        $exp = new CustomException("Intrested User not found with id: " . $this->interested_user_id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
