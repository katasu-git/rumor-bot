<?php
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', "On"); //エラー表示

function getRatestRumor() {

    // $command="python getRatestRumor.py";
    $command="/usr/local/bin/python3 /home/nishimura/public_html/rumor-bot/rumor-background/RestAPI/getRatestRumor.py 2>&1";
    exec($command,$output); //pythonを呼び出して形態素解析
    #$output = shell_exec("export LANG=ja_JP.UTF-8; python getSimTweet.py $text");
    $json = json_encode($output);
    $array = json_decode($json, true);
    return $array;

}
?>