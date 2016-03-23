<?php

$start_time = microtime(true);

$query_string = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "";
$params = explode("/", $query_string);

$facebook_id    = isset($params[0]) ? $params[0] : "";
$country        = isset($params[1]) ? $params[1] : "";
$debug          = isset($params[2]) ? $params[2] : "";

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'password';
$db_name = 'Billionaire_prod';
$tbl_user = 'user';

$json['current_time'] = date('Y-m-d H:i:s');
$json['exception'] = "";

$json['facebook_id'] = $facebook_id;
$json['country'] = $country;

if ($debug == "1") {
    $tbl_user = str_replace("_prod", "_dev", $db_name) . "." . $tbl_user;
}

try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $query = "update " . $tbl_user . " set country = ? where facebook_id = ?";

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->bind_param("ss", $country, $facebook_id);
        $stmt->execute();
        $stmt->close();
    }
} catch (Exception $ex) {
    $json['exception'] = $ex->getMessage();
} finally {
    $mysqli->close();
}

$end_time = microtime(true);

$json['execute_time'] = number_format($end_time - $start_time, 5);
$json['memory_usage'] = memory_get_usage(true);

echo json_encode($json);


