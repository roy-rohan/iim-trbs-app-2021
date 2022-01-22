<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Sponser.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate sponser object
    $sponser = new Sponser($db);
    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $sponser->sponser_id = $data->id;
    $sponser->read_single();
    if ($sponser->created_at) {
        $image->entity_id = $sponser->sponser_id;
        $image->entity_type = "sponser";
        $image->deleteByEntity();
        if ($sponser->delete()) {
            echo json_encode(
                array('message' => 'Sponser Deleted')
            );
        } else {
            $exp = new CustomException("Sponser not found with id: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("Sponser not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
