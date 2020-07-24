
<?php
ini_set('display_errors',1);
require_once dirname(__FILE__) . "/connect_mysql.php";

function writeConversations($user_id, $action, $type, $user_message, $reply_rumor) {
    
    $pdo = connectMysql();
    $stmt = $pdo -> prepare("INSERT INTO 
    line_conversations (line_user_id, reply_action, user_message_type, user_message, reply_rumor) 
    VALUES (:line_user_id, :reply_action, :user_message_type, :user_message, :reply_rumor)");
    $stmt->bindValue(':line_user_id', $user_id, PDO::PARAM_STR);
    $stmt->bindValue(':reply_action', $action, PDO::PARAM_STR);
    $stmt->bindValue(':user_message_type', $type, PDO::PARAM_STR);
    $stmt->bindValue(':user_message', $user_message, PDO::PARAM_STR);
    $stmt->bindValue(':reply_rumor', $reply_rumor, PDO::PARAM_STR);
    #$stmt->bindValue(':no_match', $no_match, PDO::PARAM_INT);

    $stmt->execute();

}
?>