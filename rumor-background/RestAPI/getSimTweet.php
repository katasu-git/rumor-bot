<?php
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', "On"); //エラー表示
require dirname(__FILE__) . '/strToJson.php';

function getSimTweet($text) {

    $path = dirname(__FILE__) . "/getSimTweet.py";
    #$command="/usr/local/bin/python3 /home/nishimura/public_html/rumor-bot/rumor-background/RestAPI/getSimTweet.py $text 2>&1";
    $command="/usr/local/bin/python3 $path $text 2>&1";
    exec($command,$output); //pythonを呼び出して形態素解析
    $result = strToJSON($output);
    return $result;

}
?>