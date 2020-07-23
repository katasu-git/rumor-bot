<?php
require_once dirname(__FILE__) . '/rumor-background/RestAPI/writeLog.php';
require_once dirname(__FILE__) . '/functions/cardReply.php';
# writeLog($userText, 0, $userId, $noMatch); //ユーザのメッセージ
# テキストの内容，ボット=>1，ユーザID，インテント失敗=>1

function getRumorsFromTweet($twitterURL) {
    require_once dirname(__FILE__) . '/rumor-background/RestAPI/getSimTweet.php';
    require_once dirname(__FILE__) . '/rumor-background/RestAPI/getTweet.php';
    $res = getTweet($twitterURL); // ツイートのリンクからツイートを取得
    global $userId;
    
    if (!$res['full_text']) {
        $emoji = json_decode('"\uDBC0\uDC7C"');
        $text = "ツイートが見つからなかったよ$emoji";
        $messages = simpleReply([$text]);
        writeLog($twitterURL, 0, $userId, 0); // ユーザのメッセージ
        writeLog($text, 1, $userId, 0); //ボットのメッセージ
        return $messages;
    }

    $twitterText = cleanText($res['full_text']);
    $res = getSimTweet($twitterText);
    writeLog($twitterURL . "\n" . $twitterText, 0, $userId, 0); // ユーザのメッセージ

    if(!$res) {
        $emoji = json_decode('"\uDBC0\uDC29"');
        $text = "関係しそうなデマは見つからなかったよ$emoji 他に気になる情報はないかな？";
        $messages = simpleReply([$text]);
        writeLog($text, 1, $userId, 0); //ボットのメッセージ
        return $messages;
    }

    writeRumorsLog($res); //ボットのメッセージ

    $messages = cardReply($res);
    array_push(
        $messages, 
        [
            "type"=>"text",
            "text"=>count($res) . "件の怪しい情報見つかったよ！"
        ]
    );
    return $messages;
}

function getRumorsFromFreeWord($userText) {
    require_once dirname(__FILE__) . '/rumor-background/RestAPI/getSimTweet.php';
    $userText = cleanText($userText);
    $res = getSimTweet($userText);
    $messages = cardReply($res);
    array_push(
        $messages, 
        [
            "type"=>"text",
            "text"=>count($res) . "件の怪しい情報見つかったよ！"
        ]
    );
    return $messages;
}

function getFiveRatestRumor() {
    require_once dirname(__FILE__) . '/rumor-background/RestAPI/getRatestRumor.php';
    $res = getRatestRumor();
    $messages = cardReply($res);
    return $messages;
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

function writeRumorsLog($rumors) {
    global $userId;
    $rumorsForLog = "";

    foreach($rumors as $r) {
        $rumorsForLog = $rumorsForLog . $r['contents'] . "\n";
    }
    writeLog($rumorsForLog, 1, $userId, 0); //ボットのメッセージ
}