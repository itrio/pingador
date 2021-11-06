<?php

$ip = $_GET["ip"];

switch(PHP_OS){
    case 'WINNT':
        exec("ping ".$ip." -n 1", $output, $result);
        break;
    
    case 'Linux':
        exec("ping ".$ip." -c 1", $output, $result);
        break;
}

$saida = implode(" ", $output);

preg_match('/(?:tempo|time)((=|<)\w*)\b/', $saida, $matches);

if($matches){
    $resultado["status"] = "on";
    $resultado["tempo"] = $matches[1];
}
else{
    $resultado["status"] = "off";
}

header('Content-Type: application/json');
print json_encode($resultado);