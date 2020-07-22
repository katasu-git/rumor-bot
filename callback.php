<?php
require 'vendor/autoload.php';
require './dialogFlow.php';
require './repFunctions.php';
require_once './backMessageToUser.php';
require_once './createStickerMessages.php';
require_once './rumor-background/RestAPI/writeLog.php';

ini_set('display_errors', "On"); //エラー表示

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

if($type != "text"){
    #テキスト以外のときは適当なスタンプでごまかす
    $messages = [];
    array_push($messages, createStickerMessages());
    backMessageToUser($replyToken, $messages);
    writeLog("user has sended $type", 0, $userId, 0); //ユーザのメッセージ
    exit;
}

############ テキストメッセージの返却処理 ##########################################

$json = analyzeText($userText); // インテントの抽出
$array = json_decode( $json , true );
$action = $array['queryResult']['action']; // どのアクションを実行するか

// テキストから「デマ」や「本当？」などのワードを取り除く処理
if($array['queryResult']['parameters']['dema']) {
    $userText = str_replace($array['queryResult']['parameters']['dema'], '', $userText);
}
if($array['queryResult']['parameters']['doubt']) {
    $userText = str_replace($array['queryResult']['parameters']['doubt'], '', $userText);
}

// インテントごとに返すメッセージを設定
$messages = [];
if ($action == 'share-twitter') {
    // ツイッターリンクが共有された場合
    $messages = getRumorsFromTweet($userText);
} else if ($action == 'handle-user-doubt' || $action == 'handle-keyword-rumor') {
    // 〜って本当？と聞かれた場合
    $messages = getRumorsFromFreeWord($userText);
} else if($action == 'input.welcome') {
    // あいさつ
    $messages = [];
    $text = $array['queryResult']['fulfillmentText'];
    array_push($messages, createStickerMessages());
    array_push($messages,
        [
            "type"=>"text",
            "text"=>$text
        ]
    );
    writeLog($userText, 0, $userId, 0); // ユーザのメッセージ
    writeLog($text, 1, $userId, 0); //ボットのメッセージ

} else if ($action == 'input.unknown' || $action == 'handle-help') {
    // ヘルプを表示，もしくは意図がわからなかった場合
    $text = "使い方はわかったかな？気になることがあったら、「〇〇ってホント？」って話しかけてみてね！";
    //$messages = simpleReply([$text]);
    $messages = [
        [
            "type"=> "image",
            "originalContentUrl"=> "https://www2.yoslab.net/~nishimura//rumor-bot/images/howtouse1.png",
            "previewImageUrl"=> "https://www2.yoslab.net/~nishimura//rumor-bot/images/howtouse1.png"
        ],
        [
            "type"=> "image",
            "originalContentUrl"=> "https://www2.yoslab.net/~nishimura//rumor-bot/images/howtouse2.png",
            "previewImageUrl"=> "https://www2.yoslab.net/~nishimura//rumor-bot/images/howtouse2.png"
        ],
        [
            "type"=> "image",
            "originalContentUrl"=> "https://www2.yoslab.net/~nishimura//rumor-bot/images/howtouse3.png",
            "previewImageUrl"=> "https://www2.yoslab.net/~nishimura//rumor-bot/images/howtouse3.png"
        ],
        [
            "type"=> "text",
            "text"=> $text
        ]
    ];
    writeLog($userText, 0, $userId, 0); // ユーザのメッセージ
    writeLog($text, 1, $userId, 0); //ボットのメッセージ

} else if ($action == 'handle-latest-rumor') {
    // 最新の流言を上から5つ取ってくる処理
    $messages = getFiveRatestRumor();

} else {
    //例外処理
    $text = '「〇〇の流言を教えて！」や「〇〇って本当？」と話しかけてみてください！';
    $messages = simpleReply([$text]);
    writeLog($userText, 0, $userId, 0); // ユーザのメッセージ
    writeLog("例外処理発生", 1, $userId, 0); //ボットのメッセージ
}

backMessageToUser($replyToken, $messages);

############ テキストメッセージの返却処理 ##########################################