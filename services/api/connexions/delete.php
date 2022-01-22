<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: DELETE');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/Connextion.php';
include_once __DIR__ . '/../../models/Image.php';


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate connexion object
    $connexion = new Connextion($db);
    $image = new Image($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    // Set ID to delete
    $connexion->connexion_id = $data->id;
    $connexion->read_single();
    if ($connexion->slug) {
        $image->entity_id = $connexion->connexion_id;
        $image->entity_type = "connexion";
        $image->deleteByEntity();
        if ($connexion->delete()) {
            echo json_encode(
                array('message' => 'Connextion Deleted')
            );
        } else {
            $exp = new CustomException("Connextion not found with id: " . $data->id);
            $exp->sendBadRequest();
        }
    } else {
        $exp = new CustomException("Connextion not found with id: " . $data->id);
        $exp->sendBadRequest();
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
