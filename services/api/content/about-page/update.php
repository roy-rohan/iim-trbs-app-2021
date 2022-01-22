<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: PUT');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Content-Type,Access-Control-Allow-Methods, Authorization, X-Requested-With');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/AboutPage.php';

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate about_page object
    $about_page = new AboutPage($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $about_page = copyObject(
        $data,
        $about_page,
        []
    );

    // Update about_page
    if ($about_page->update()) {
        echo json_encode(
            array('message' => 'About Page Updated')
        );
    } else {
        $exp = new CustomException('About Page not updated.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
