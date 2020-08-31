<?php
ini_set('display_errors', "On"); //エラー表示
require_once dirname(__FILE__) . "/../../functions/connect_mysql.php";
require_once dirname(__FILE__) . "/../RestAPI/sortDesk.php";

function getRankingRumors() {
    $pdo = connectMysql();  //mysqlに接続
    $sql = "SELECT * FROM rumors ORDER BY fix DESC LIMIT 5"; // シングルコート必要
    $stmt = $pdo -> query($sql) -> fetchAll();
    return $stmt;
}

var_dump(getRankingRumors()); #削除する