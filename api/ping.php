<?php

$ip = $_GET["ip"];

exec("ping ".$ip." -n 1", $output, $result);

if(isset($output[2])){
    $resultado["linha"] = utf8_encode($output[2]);
    $resultado["status"] = strpos($resultado["linha"], "TTL") === FALSE ? "off" : "on";
}
else{
    $resultado["status"] = "off";
}


header('Content-Type: application/json');
print json_encode($resultado);