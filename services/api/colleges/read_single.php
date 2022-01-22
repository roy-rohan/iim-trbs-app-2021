<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/College.php';

if ($_SERVER['REQUEST_METHOD'] == "GET") {
    // Instantiate DB & connect
    $database = new Database();
    $db = $database->connect();

    // Instantiate blog college object
    $college = new College($db);

    // Get ID
    $college->college_id = isset($_GET['id']) ? $_GET['id'] : die();

    // Get college
    $college->read_single();
    if ($college->college_id) {
        $college_arr = array(
            'college_id' => $college->college_id,
            'name' => $college->name,
            'is_active' => $college->is_active,
            'created_at' => $college->created_at,
            'updated_at' => $college->updated_at
        );

        echo json_encode($college_arr);
    } else {
        $exp = new CustomException('No matched record found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . ' method not allowed.');
    $exp->sendMethodNotAllowedRequest();
    exit(1);
}
