<?php
header('Content-type: text/plain');
header('Content-Disposition: attachment; filename="testeIPs.txt"');

$txtInicial = $_POST["txtLinhas"];

$txtSaida = "";

$linhas = explode(";", $txtInicial);

foreach ($linhas as $linha){
    $colunas = explode(",", $linha);

    if(count($colunas) > 1){
        $txtSaida .= $colunas[1] . " => " . strip_tags($colunas[2]) . "\r\n";
    }
}

print $txtSaida;