<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

$txtInicial = isset($_POST["txtLinhas"]) ? $_POST["txtLinhas"] : "";
$filename = isset($_POST["filename"]) ? $_POST["filename"] : "testeIPs";

switch ($_POST["filetype"]){
    case ".txt":
        exportTXT($txtInicial, $filename);
        break;

    case ".xlsx":
        exportXLSX($txtInicial, $filename);
        break;

    default:
        exportTXT($txtInicial, $filename);
}

function exportTXT($txt, $filename){
    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="'.$filename.'.txt"');

    $txtSaida = "";

    $linhas = explode(";", $txt);

    foreach ($linhas as $linha){
        $colunas = explode(",", $linha);

        if(count($colunas) > 1){
            $txtSaida .= $colunas[1] . " => " . strip_tags($colunas[2]) . "\r\n";
        }
    }

    print $txtSaida;
}

function exportXLSX($txt, $filename){
    $spreadsheet = new Spreadsheet();

    //Define as propriedades do arquivo
    $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
        ->setLastModifiedBy('Pingador ICMP')
        ->setTitle('Arquivo de Exportação do Pingador')
        ->setSubject('Arquivo de Exportação do Pingador')
        ->setDescription('Arquivo de Exportação do Pingador.');

    //Cria as células
    $linhas = explode(";", $txt);

    foreach ($linhas as $key => $linha){
        $colunas = explode(",", $linha);

        if(count($colunas) > 1 && $colunas[1] != "" && $colunas[1] != " " && !empty($colunas[1])){
            //$txtSaida .= $colunas[1] . " => " . strip_tags($colunas[2]) . "\r\n";
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('A'.($key+1), $colunas[1]);
            $spreadsheet->setActiveSheetIndex(0)->setCellValue('B'.($key+1), strip_tags($colunas[2]));
            //$spreadsheet->setActiveSheetIndex(0)->setCellValue('C'.($key+1), $colunas[3]);
        }

    }
    $spreadsheet->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
    $spreadsheet->getActiveSheet()->getColumnDimension("B")->setAutoSize(true);

    $spreadsheet->getActiveSheet()->setTitle('Disponibilidade dos Hosts');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit;

}