<?php
ini_set('display_errors', "On"); //エラー表示

function strToJSON($output) {
    $json = json_encode($output);
    $array = json_decode($json, true);

    $result = [];
    foreach($array as $a){
        $array = explode(',', $a);
        $associative['id'] = $array[0];
        $associative['contents'] = $array[1];
        $associative['fix'] = $array[2];
        $associative['wakachi'] = $array[3];
        $associative['url'] = $array[4];
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