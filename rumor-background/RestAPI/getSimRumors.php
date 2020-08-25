<?php
ini_set('display_errors', "On"); //エラー表示
require_once dirname(__FILE__) . "/../../functions/connect_mysql.php";
require_once dirname(__FILE__) . "/../RestAPI/sortDesk.php";

function getMorpheme($text) {
    $path = dirname(__FILE__) . "/getMorpheme.py";
    $command="/usr/local/bin/python3 $path $text 2>&1";
    exec($command,$output); //pythonを呼び出して形態素解析
    return $output;
}

function getRumors() {
    # $today = date('Y-m-d');

    $pdo = connectMysql();  //mysqlに接続
    # $sql = "SELECT * FROM rumors WHERE created_at = '$today'"; // シングルコート必要
    $sql = "SELECT * FROM rumors"; // シングルコート必要
    $stmt = $pdo -> query($sql);
    $result = array();
    foreach($stmt as $row) {
        $rumor = array('id' => $row['id'], 'content' => $row['content'], 'fix' => $row['fix'], 'fix_tweets' => $row['fix_tweets'], 'morpheme' => $row['morpheme'], 'updown' => $row['updown'], 'created_at' => $row['created_at']);
        array_push($result, $rumor);
    }
    # $result = sortDesk($result);
    return $result;
}

function searchSimRurmor($rumors, $inputMorpheme) {
    $result = [];
    for($i=0; $i<count($rumors); $i++) {
        $rumor = $rumors[$i];
        $rumorMorpheme = $rumor['morpheme'];
        $rumorMorpheme = explode("/", $rumorMorpheme);
        # var_dump($rumorMorpheme);
        if(array_intersect($rumorMorpheme, $inputMorpheme)) {
            $keywords = array_intersect($rumorMorpheme, $inputMorpheme);
            $hitNum = count($keywords);

            $keyword = "";
            foreach($keywords as $k) {
                $keyword .= "$k/"; 
            }
            $keyword = substr($keyword, 0, -1);

            $rumor += array('hitNum'=>$hitNum); # いくつキーワードが一致したか
            $rumor += array('keyword'=>$keyword); # 一致したキーワード（スラッシュ区切り）
            array_push($result, $rumor);
        }
    }
    $result = sortDesk($result, "hitNum"); # 一致度が高い順に入れ替え
    return $result;
}

function getSimRumors($text) {
    $rumors = getRumors();
    $morpheme = getMorpheme($text);
    $morpheme = explode("/", $morpheme[0]);
    $simRumors = searchSimRurmor($rumors, $morpheme);
    # print_r($simRumors);
    return $simRumors;
}

?>