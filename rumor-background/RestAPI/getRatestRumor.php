<?php
ini_set('display_errors', "On"); //エラー表示
require dirname(__FILE__) . '/strToJson.php';

function getRatestRumor() {

    // $command="python getRatestRumor.py";
    $path = dirname(__FILE__) . "/getRatestRumor.py";
    $command="/usr/local/bin/python3 $path 2>&1";
    exec($command,$output); //pythonを呼び出して形態素解析
    $result = strToJSON($output);
    return $result;

}
#getRatestRumor() #削除する