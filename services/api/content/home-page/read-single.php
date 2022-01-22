<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../../common/APIUtil.php';
include_once __DIR__ . '/../../../models/HomePage.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $homePageModel = new HomePage($db);
	
    $homePageModel->read_single();
    if ($homePageModel->home_page_id) {
        $homePageResponse = array(
            'home_page_id' => $homePageModel->home_page_id,
            'about' => $homePageModel->about,
            'event_count' => $homePageModel->event_count,
            'workshop_count' => $homePageModel->workshop_count,
            'speaker_count' => $homePageModel->speaker_count,
            'about' => $homePageModel->about,
            'panel_disc_count' => $homePageModel->panel_disc_count,
            'mng_symp_count' => $homePageModel->mng_symp_count,
            'video_link' => $homePageModel->video_link
        );
        echo json_encode($homePageResponse);
    } else {
        $exp = new CustomException('Home Page Information not found.');
        $exp->sendBadRequest();
        exit(1);
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
