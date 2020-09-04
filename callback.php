<?php
ini_set('display_errors', "On"); //エラー表示
require 'vendor/autoload.php';
require_once dirname(__FILE__) . '/functions/dialogFlow.php';
require_once dirname(__FILE__) . '/functions/backMessageToUser.php';
require_once dirname(__FILE__) . '/functions/createStickerMessages.php';
require_once dirname(__FILE__) . '/functions/writeConversations.php';
require_once dirname(__FILE__) . '/functions/replyCards.php';
require_once dirname(__FILE__) . '/rumor-background/RestAPI/getRatestRumor.php';
require_once dirname(__FILE__) . '/rumor-background/RestAPI/getSuddenRiseRumor.php';
require_once dirname(__FILE__) . '/rumor-background/RestAPI/getRankingRumors.php';
require_once dirname(__FILE__) . '/rumor-background/RestAPI/getSimRumors.php';
require_once dirname(__FILE__) . '/rumor-background/RestAPI/getTweet.php';

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
    writeConversations($userId, $type, $type, $type, $type);
    exit;
}

############ テキストメッセージの返却処理 ##########################################

$json = analyzeText($userText); // インテントの抽出
$array = json_decode( $json , true );
$action = $array['queryResult']['action']; // どのアクションを実行するか

// テキストから「デマ」や「本当？」などのワードを取り除く処理
/*
if($array['queryResult']['parameters']['dema']) {
    $userText = str_replace($array['queryResult']['parameters']['dema'], '', $userText);
}
if($array['queryResult']['parameters']['doubt']) {
    $userText = str_replace($array['queryResult']['parameters']['doubt'], '', $userText);
}
*/

// インテントごとに返すメッセージを設定
$messages = [];
if ($action == 'share-twitter') {
    // ツイッターリンクが共有された場合
    $tweetJson = getTweet($userText); // ツイートのリンクからツイートを取得
    $tweet = $tweetJson['full_text'];
    if ($tweet) {
        $tweet = cleanText($tweet);
        $rumors = getSimRumors($tweet);
        if($rumors) {
            $messages = replyCards($rumors, "share-twitter");
            array_push(
                $messages, 
                [
                    "type"=>"text",
                    "text"=>"【もとのツイート】\n" . deleteURL($tweetJson['full_text'])
                ],
                [
                    "type"=>"text",
                    "text"=>"👆のツイートに関係しそうな" . count($rumors) . "件の怪しい情報が見つかったよ！"
                ]
            );
            $reply_rumor = createRumorsForLog($rumors);
        } else {
            $emoji = json_decode('"\uDBC0\uDC29"');
            $reply_rumor = "関係しそうなデマは見つからなかったよ$emoji 他に気になる情報はないかな？";
            $messages = simpleReply([$reply_rumor]);
        }
    } else {
        $emoji = json_decode('"\uDBC0\uDC7C"');
        $reply_rumor = "ツイートが見つからなかったよ$emoji";
        $messages = simpleReply([$reply_rumor]);
    }

} else if ($action == 'handle-user-doubt' || $action == 'handle-keyword-rumor') {
    // 〜って本当？と聞かれた場合
    $userText = cleanText($userText);
    $rumors = getSimRumors($userText);

    if($rumors) {
        $messages = replyCards($rumors, "handle-user-doubt");
        array_push(
            $messages, 
            [
                "type"=>"text",
                "text"=>count($rumors) . "件の怪しい情報が見つかったよ！"
            ]
        );
        $reply_rumor = createRumorsForLog($rumors);
    } else {
        $emoji = json_decode('"\uDBC0\uDC29"');
        $reply_rumor = "関係しそうなデマは見つからなかったよ$emoji 他に気になる情報はないかな？";
        $messages = simpleReply([$reply_rumor]);
    }

} else if($action == 'input.welcome') {
    // あいさつ
    $messages = [];
    $reply_rumor = $array['queryResult']['fulfillmentText'];
    array_push($messages, createStickerMessages());
    array_push($messages,
        [
            "type"=>"text",
            "text"=>$reply_rumor
        ]
    );

} else if ($action == 'input.unknown' || $action == 'handle-help') {
    // ヘルプを表示，もしくは意図がわからなかった場合
    $reply_rumor = "使い方はわかったかな？気になることがあったら、「〇〇ってホント？」って話しかけてみてね！";
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
            "text"=> $reply_rumor
        ]
    ];

} else if ($action == 'handle-latest-rumor') {
    // 最新の流言を上から5つ取ってくる処理
    $rumors = getRatestRumor();
    $rumorsRand = [];
    foreach( array_rand( $rumors, 5 ) as $key ) {
		array_push($rumorsRand, $rumors[$key]);
	}
    $messages = replyCards($rumorsRand, "handle-latest-rumor");
    array_push($messages,
        [
            "type"=>"text",
            "text"=>"今日新しく見つかった流言だよ！"
        ]
    );
    $reply_rumor = createRumorsForLog($rumorsRand);

} else if ($action == 'handle-sudden-rise') {
    $rumors = getSuddenRiseRumor();
    
    $messages = replyCards($rumors, "handle-sudden-rise");
    array_push($messages,
        [
            "type"=>"text",
            "text"=>"みんなの関心が高まってる流言だよ！"
        ]
    );
    $reply_rumor = createRumorsForLog($rumors);

} else if ($action == 'handle-ranking') {
    $rumors = getRankingRumors();
    
    $messages = replyCards($rumors, "handle-ranking");
    array_push($messages,
        [
            "type"=>"text",
            "text"=>"続きはここで見られるよ！\nhttps://liff.line.me/1654776413-dpYy83Wb/?id=&path=ranking"
        ]
    );
    $reply_rumor = createRumorsForLog($rumors);

} else {
    //例外処理
    $reply_rumor = '「〇〇の流言を教えて！」や「〇〇って本当？」と話しかけてみてください！';
    $messages = simpleReply([$reply_rumor]);
}

// 何かしらの例外でメッセージが空の場合
if(!$messages) {
    $reply_rumor = "ごめんなさい、他の言葉でためしてみてね$emoji";
    $messages = simpleReply([$reply_rumor]);
}

backMessageToUser($replyToken, $messages);
writeConversations($userId, $action, $type, $userText, $reply_rumor); //ログ書き込み

function createRumorsForLog($rumors) {
    $reply_rumor = "";
    for($i=0; $i<5; $i++) {
        $reply_rumor = $reply_rumor . $rumors[$i]['id'] . "/";
    }
    $reply_rumor = substr($reply_rumor, 0, -1); // 最後のスラッシュ削除
    return $reply_rumor;
}

function cleanText($text) {
    // URLを削除
    $text = deleteURL($text);
    $text = preg_replace("/[^ぁ-んァ-ンーa-zA-Z0-9一-龠０-９\-\r]+/u",'' ,$text);
    $text = preg_replace("/( |　)/", "", $text ); #文中に含まれる空白を削除
    $text = preg_replace('/(?:\n|\r|\r\n)/', '', $text );
    return $text;
}

function deleteURL($text) {
    if(preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $text, $result) !== false){
        $text = str_replace($result[0], '', $text);
    }
    return $text;
}

function simpleReply($texts) {
    // メッセージオブジェクトの作成 http://urx.blue/vFSo
    $messages = [];
    foreach($texts as $t) {
        array_push(
            $messages,
            [
                "type" => "text",
                "text" => $t
            ]
        );
    }
    return $messages;
}