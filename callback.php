<?php
require 'vendor/autoload.php';
require './dialogFlow.php';
require './repFunctions.php';
ini_set('display_errors', "On"); //エラー表示
$accessToken = file_get_contents('./conf/lineAccessToken.txt');

//ユーザーからのメッセージ取得
$json_string = file_get_contents('php://input');
$jsonObj = json_decode($json_string);

$type = $jsonObj->{"events"}[0]->{"message"}->{"type"};
//メッセージ取得
$userText = $jsonObj->{"events"}[0]->{"message"}->{"text"};
//userIDしゅとく 
$userId = $jsonObj->{"events"}[0]->{"source"}->{"userId"};
//ReplyToken取得
$replyToken = $jsonObj->{"events"}[0]->{"replyToken"};

//メッセージ以外のときは何も返さず終了
if($type != "text"){
    exit;
}

$json = analyzeText($userText); // インテントの抽出
$array = json_decode( $json , true );
$action = $array['queryResult']['action']; // どのアクションを実行するか

$messages = [];
if ($action == 'share-twitter') {
    // ツイッターリンクが共有された場合
    $messages = getRumorsFromTweet($userText);
} else if ($action == 'handle-user-doubt') {
    // 〜って本当？と聞かれた場合
    $messages = getRumorsFromFreeWord($userText);
} else if($action == 'input.welcome') {
    // あいさつ
    $text = $array['queryResult']['fulfillmentText'];
    $messages = simpleReply([$text]);
} else if ($action == 'input.unknown' || $action == 'handle-help') {
    // ヘルプを表示，もしくは意図がわからなかった場合
    $messages = simpleReply(['「〇〇の流言を教えて！」や「〇〇って本当？」と話しかけてみてください！']);
} else if ($action == 'handle-latest-rumor') {
    // 最新の流言を上から5つ取ってくる処理
    $messages = getFiveRatestRumor();
} else if ($action == 'handle-keyword-rumor') {
    // キーワードに関連する流言を取ってくる処理
    $messages = simpleReply(['この機能は作成中です']);
}

///ログの書き込み部分
$noMatch = 0;
if ($action == 'input.unknown') {
    $noMatch = 1;
}
require_once("./rumor-background/RestAPI/writeLog.php");
foreach($messages as $m) {
    writeLog($userText, 0, $userId, $noMatch); //ユーザのメッセージ
    if ($noMatch !== 1) {
        writeLog($m["text"], 1, $userId, $noMatch); //ボットのメッセージ
    }
}

// 返信部分
$post_data = [
    "replyToken" => $replyToken,
    "messages" => $messages
];

$ch = curl_init("https://api.line.me/v2/bot/message/reply");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json; charser=UTF-8',
    'Authorization: Bearer ' . $accessToken
    ));
$result = curl_exec($ch);
curl_close($ch);

