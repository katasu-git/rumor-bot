<?php
ini_set('display_errors', "On"); //エラー表示
require_once dirname(__FILE__) . "/../../functions/connect_mysql.php";
require_once dirname(__FILE__) . "/../RestAPI/sortDesk.php";

function getRatestRumor() {
    $today = date('Y-m-d');

    $pdo = connectMysql();  //mysqlに接続
    $sql = "SELECT * FROM rumors WHERE created_at = '$today'"; // シングルコート必要
    $stmt = $pdo -> query($sql);
    $result = array();
    foreach($stmt as $row) {
        $rumor = array('id' => $row['id'], 'content' => $row['content'], 'fix' => $row['fix'], 'fix_tweets' => $row['fix_tweets'], 'morpheme' => $row['morpheme'], 'updown' => $row['updown'], 'created_at' => $row['created_at']);
        array_push($result, $rumor);
    }
    $result = sortDesk($result, 'updown');
    return $result;
}

# var_dump(getRatestRumor()); #削除する