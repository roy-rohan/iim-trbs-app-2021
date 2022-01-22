<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/AboutPage.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $aboutPageModel = new AboutPage($db);

    $aboutPageModel->read_single();
    if ($aboutPageModel->about_page_id) {
        $aboutPageResponse = array(
            'about_page_id' => $aboutPageModel->about_page_id,
            'about' => $aboutPageModel->about,
            'event_desc' => $aboutPageModel->event_desc,
            'workshop_desc' => $aboutPageModel->workshop_desc,
            'speaker_desc' => $aboutPageModel->speaker_desc,
            'video_link' => $aboutPageModel->video_link
        );

        echo json_encode($aboutPageResponse);
    } else {
        $exp = new CustomException('About Page Information not found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
