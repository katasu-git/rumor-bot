<?php
require_once './rumor-background/RestAPI/writeLog.php';
# writeLog($userText, 0, $userId, $noMatch); //ユーザのメッセージ
# テキストの内容，ボット=>1，ユーザID，インテント失敗=>1

function getRumorsFromTweet($twitterURL) {
    require_once './rumor-background/RestAPI/getSimTweet.php';
    require_once './rumor-background/RestAPI/getTweet.php';
    $res = getTweet($twitterURL); // ツイートのリンクからツイートを取得
    global $userId;
    
    if (!$res['full_text']) {
        $messages = simpleReply(['ツイートが取得できませんでした']);
        writeLog($twitterURL, 0, $userId, 0); // ユーザのメッセージ
        writeLog('ツイートが取得できませんでした', 1, $userId, 0); //ボットのメッセージ
        return $messages;
    }

    $twitterText = cleanText($res['full_text']);
    $res = getSimTweet($twitterText);
    writeLog($twitterURL . "\n" . $twitterText, 0, $userId, 0); // ユーザのメッセージ

    if(!$res) {
        $messages = simpleReply(["関連する流言はありませんでした"]);
        writeLog('関連する流言はありませんでした', 1, $userId, 0); //ボットのメッセージ
        return $messages;
    }

    writeRumorsLog($res); //ボットのメッセージ

    $messages = cardReply($res);
    return $messages;
}

function getRumorsFromFreeWord($userText) {
    require_once './rumor-background/RestAPI/getSimTweet.php';
    $userText = cleanText($userText);
    $res = getSimTweet($userText);
    return cardReply($res);
}

function getFiveRatestRumor() {
    require_once './rumor-background/RestAPI/getRatestRumor.php';
    $res = getRatestRumor();
    return cardReply($res);
}

function cutText($text) {
    //文字数の上限
    $limit = 45;
    if(mb_strlen($text) > $limit) { 
        $cutText = mb_substr($text,0,$limit, "UTF-8");
        return $cutText . "...";
    } else {
        return $text;
    }
}

function cleanText($text) {
    // URLを削除
    if(preg_match_all('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@&=+$,%#]+)', $text, $result) !== false){
        $text = str_replace($result[0], '', $text);
    }
    $text = preg_replace("/( |　)/", "", $text ); #文中に含まれる空白を削除
    $text = preg_replace('/(?:\n|\r|\r\n)/', '', $text );
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

function cardReply($rumors) {
    $cardMessages = [];
    $file = "./carousel.json";
    $json = file_get_contents($file);
    $array = json_decode($json, true);
    for($i=0; $i<count($rumors); $i++) {
        array_push(
            $array["template"]["columns"],
            [
                "title"=> $rumors[$i]['fix'] . "人 が疑ってます",
                "text"=> cutText($rumors[$i]['contents']),
                "actions"=> [
                  [
                    "type"=> "uri",
                    "label"=> "もっと詳しく！",
                    "uri"=> $rumors[$i]['url']
                  ]
                ]
            ]
        );
    }
    array_push(
        $cardMessages, $array
    );
    array_push(
        $cardMessages, 
        [
            "type"=>"text",
            "text"=>count($rumors) . "件の怪しい情報見つかったよ！\n" . "もっと詳しく！を押すと、訂正情報が見られるよ。"
        ]
    );
    return $cardMessages;
}

function writeRumorsLog($rumors) {
    global $userId;
    $rumorsForLog = "";

    foreach($rumors as $r) {
        $rumorsForLog = $rumorsForLog . $r['contents'] . "\n";
    }
    writeLog($rumorsForLog, 1, $userId, 0); //ボットのメッセージ
}