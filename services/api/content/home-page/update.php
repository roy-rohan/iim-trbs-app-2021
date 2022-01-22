<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/HomePage.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate home_page object
    $home_page = new HomePage($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $home_page = copyObject(
        $data,
        $home_page,
        []
    );

    // Update home_page
    if ($home_page->update()) {
        echo json_encode(
            array('message' => 'Home Page Updated')
        );
    } else {
        $exp = new CustomException('Home Page not updated.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
