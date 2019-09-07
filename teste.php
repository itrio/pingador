<?php
$file = "test.csv";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename='.$file);
$content = "Col1,Col2,Col3\n";
$content .= "test1,test1,test3\n";
$content .= "testtest,ttesttest2,testtest3\n";
echo $content;