<?php

$start_time = microtime(true);

$query_string = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "";
$params = explode("/", $query_string);

$device     = isset($params[0]) ? $params[0] : "";
$version    = isset($params[1]) ? $params[1] : "";
$debug      = isset($params[2]) ? $params[2] : "";

//$device = isset($_GET['device']) ? $_GET['device'] : "";
//$version = isset($_GET['version']) ? $_GET['version'] : "";
//$debug = isset($_GET['debug']) ? $_GET['debug'] : "0";

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'password';
$db_name = 'Billionaire_prod';
$tbl_event = 'event';

$json['current_time'] = date('Y-m-d H:i:s');
$json['device'] = $device;
$json['version'] = $version;
$json['exception'] = "";

if ($debug == "1") {
    $tbl_event = str_replace("_prod", "_dev", $db_name) . "." . $tbl_event;
}

try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $query = "select start_date, end_date from " . $tbl_event . " "
            . "where `device` = ? and `version` = ? and `status`='active' ";

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->bind_param("ss", $device, $version);
        $stmt->execute();
        $stmt->bind_result($start_date, $end_date);

        if ($stmt->fetch()) {
            $json['event_time']['start'] = $start_date;
            $json['event_time']['end'] = $end_date;
        }

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


