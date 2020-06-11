<?php
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', "On"); //エラー表示

function getSimTweet($text) {

    $text = preg_replace("/( |　)/", "", $text ); #文中に含まれる空白を削除
    $text = preg_replace('/(?:\n|\r|\r\n)/', '', $text );
    $command="/usr/local/bin/python3 /home/nishimura/public_html/rumor-bot/rumor-background/RestAPI/getSimTweet.py $text 2>&1";
    exec($command,$output); //pythonを呼び出して形態素解析
    $simRumors = [];
    foreach($output as $o){
        array_push($simRumors, $o);
        //echo "<p>" . $o . "</p>";
    }
    $json = json_encode($simRumors);
    $array = json_decode($json, true);
    return $array;

}
?>