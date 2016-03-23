<?php

$start_time = microtime(true);

$query_string = isset($_SERVER["QUERY_STRING"]) ? $_SERVER["QUERY_STRING"] : "";
$params = explode("/", $query_string);

$facebook_id    = isset($params[0]) ? $params[0] : "";
$display_name   = isset($params[1]) ? $params[1] : "";
$networth       = isset($params[2]) ? $params[2] : "0";
$networth_2     = isset($params[3]) ? $params[3] : "0";
$networth_pow   = isset($params[4]) ? $params[4] : "0";
$appVersion     = isset($params[5]) ? $params[5] : "";
$device_type    = isset($params[6]) ? $params[6] : "";
$debug          = isset($params[7]) ? $params[7] : "";

$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'password';
$db_name = 'Billionaire_prod';
$tbl_user = 'user';

$json['current_time'] = date('Y-m-d H:i:s');
$json['exception'] = "";

$json['facebook_id'] = $facebook_id;
$json['display_name'] = $display_name;
$json['networth'] = $networth;
$json['networth_2'] = $networth_2;
$json['networth_pow'] = $networth_pow;
$json['appVersion'] = $appVersion;
$json['device_type'] = $device_type;

if ($debug == "1") {
    $tbl_user = str_replace("_prod", "_dev", $db_name) . "." . $tbl_user;
}

try {
    $mysqli = new mysqli($db_host, $db_user, $db_pass, $db_name);

    $query = "insert into " . $tbl_user . " (facebook_id, display_name, networth, networth_2, networth_pow, appVersion, device_type) "
            . " values (?, ?, ?, ?, ?, ?, ?) "
            . " on duplicate key update "
            . "         display_name=values(display_name), networth=values(networth), networth_2=values(networth_2), "
            . "         networth_pow=values(networth_pow), appVersion=values(appVersion), device_type=values(device_type)";

    if ($stmt = $mysqli->prepare($query)) {

        $stmt->bind_param("ssddiss", $facebook_id, $display_name, $networth, $networth_2, $networth_pow, $appVersion, $device_type);
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


