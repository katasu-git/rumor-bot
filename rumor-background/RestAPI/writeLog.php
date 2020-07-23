<?php
ini_set('display_errors',1);
require_once dirname(__FILE__) . "/connect_mysql.php";

function writeLog($lineMessage, $isBot, $userId, $noMatch) {
    
    $pdo = connect_mysql();
    $stmt = $pdo -> prepare("INSERT INTO 
    line_log (lineMessage, isBot, userId, noMatch) 
    VALUES (:lineMessage, :isBot, :userId, :noMatch)");
    $stmt->bindValue(':lineMessage', $lineMessage, PDO::PARAM_STR);
    $stmt->bindValue(':isBot', $isBot, PDO::PARAM_INT);
    $stmt->bindValue(':userId', $userId, PDO::PARAM_STR);
    $stmt->bindValue(':noMatch', $noMatch, PDO::PARAM_INT);

    $stmt->execute();

}
?>