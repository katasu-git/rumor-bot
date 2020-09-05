<?php
ini_set('display_errors', "On"); //エラー表示
require_once dirname(__FILE__) . "/../../functions/connect_mysql.php";
require_once dirname(__FILE__) . "/../RestAPI/sortDesk.php";

function getRankingRumors() {
    $today = date("Y-m-d");
    $weekday = date("Y-m-d",strtotime("-7 day"));

    $pdo = connectMysql();  //mysqlに接続
    $sql = "SELECT * FROM rumors WHERE created_at BETWEEN '$weekday' AND '$today' ORDER BY fix DESC LIMIT 5"; // シングルコート必要
    $stmt = $pdo -> query($sql) -> fetchAll();
    return $stmt;
}

var_dump(getRankingRumors()); #削除する