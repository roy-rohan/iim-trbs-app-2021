<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $image->image_id = $data->id;
    $image->read_single();
    if ($image->image_id) {
        if (unlink("../.." . $image->path)) {
            $image->delete();
            // log image is deleted
        } else {
            $exp = new CustomException("Image not deleted: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("Image not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
