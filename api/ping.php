<?php

$ip = $_GET["ip"];

exec("ping ".$ip." -n 1", $output, $result);

if(isset($output[2])){
    $resultado["linha"] = utf8_encode($output[2]);
    if(strpos($resultado["linha"], "TTL") === FALSE || strpos($resultado["linha"], "tempo") === FALSE){
        $resultado["status"] = "off";
    }
    else{
        $resultado["status"] = "on";
        $tempo = explode("tempo", $resultado["linha"]);
        $tempo = explode(" ", $tempo[1]);
        $resultado["tempo"] = $tempo[0];
    }

}
else{
    $resultado["status"] = "off";
}


header('Content-Type: application/json');
print json_encode($resultado);