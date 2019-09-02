<?php

$ips = [
    0 => '187.60.42.254',
    1 => '189.84.120.26',
    2 => '192.168.229.250',
    3 => '192.168.0.11',
];

foreach ($ips as $key => $value){
    exec("ping ".$value." -n 1", $output, $result);

    $resultado[$key]["linha"] = $output[2];
    $resultado[$key]["status"] = strpos($resultado[$key]["linha"], "TTL") === FALSE ? "off" : "on";
    $output = "";
}

print "<pre>";
print_r($resultado);
print "</pre>";
print "<br>";