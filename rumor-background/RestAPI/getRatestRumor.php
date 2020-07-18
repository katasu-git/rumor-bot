<?php
header('Content-Type: text/html; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
ini_set('display_errors', "On"); //エラー表示

function getRatestRumor() {

    // $command="python getRatestRumor.py";
    $command="/usr/local/bin/python3 /home/nishimura/public_html/rumor-bot/rumor-background/RestAPI/getRatestRumor.py 2>&1";
    exec($command,$output); //pythonを呼び出して形態素解析
    $json = json_encode($output);
    $array = json_decode($json, true);

    $result = [];
    foreach($array as $a){
        $array = explode(',', $a);
        $associative['id'] = $array[0];
        $associative['contents'] = $array[1];
        $associative['fix'] = $array[2];
        $associative['wakachi'] = $array[3];
        array_push($result, trimStr($associative));
    }
    return $result;

}

function trimStr($array) {
    $array = str_replace("[", '', $array);
    $array = str_replace("]", '', $array);
    $array = str_replace("'", '', $array);
    $array = str_replace(" ", '', $array);
    return $array;
}

#getRatestRumor() #削除する
?>