<?php
// Headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once __DIR__ . '/../common/APIUtil.php';
include_once __DIR__ . '/../../models/College.php';

// Instantiate DB & connect
$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $college_model = new College($db);

    // Get raw posted data
    $data = json_decode(file_get_contents("php://input"));

    $totalRowCount = $college_model->getCount($data);

    if ($totalRowCount > 0) {

        $college_arr = array();
        $offset = 0;

        while ($totalRowCount > 0) {

            $result = $college_model->read($data, $offset);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {

                extract($row);

                $college = array(
                    'college_id' => $college_id,
                    'name' => $name,
                    'is_active' => $is_active,
                    'created_at' => $created_at,
                    'updated_at' => $updated_at
                );

                // Push to "data"
                array_push($college_arr, $college);
            }
            $totalRowCount -= $result->rowCount();
            $offset += 300;
        }
        // Turn to JSON & output
        // echo json_encode($college_arr);

        $prefix = '';
        $output = '[';
        foreach ($college_arr as $row) {
            if (json_encode($row)) {
                $output .= $prefix . json_encode($row);
                $prefix = ',';
            } else {
                foreach ($row as $key => $val) {
                    echo "[" . $key . "] : " . $val;
                }
            }
        }
        $output .= ']';
        echo $output;
    } else {
        // No Events
        echo json_encode(
            array()
        );
    }
} else {
    $exp = new CustomException($_SERVER['REQUEST_METHOD'] . " method not allowed");
    $exp->sendMethodNotAllowedRequest();
}
