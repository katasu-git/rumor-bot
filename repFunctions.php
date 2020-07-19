<?php

function getRumorsFromTweet($twitterURL) {
    require './rumor-background/RestAPI/getTweet.php';
    $res = getTweet($twitterURL); // ツイートのリンクからツイートを取得
    if ($res['full_text']) {
        $twitterText = cleanText($res['full_text']);
        require './rumor-background/RestAPI/getSimTweet.php';
        $res = getSimTweet($twitterText);
        if($res) {
            $twitterText = cutTweet($twitterText);
            $firstCardTexts = [
                "title"=> "ツイートに関係しそうなデマ→",
                "text"=> $twitterText,
                "actions"=> [
                  [
                    "type"=> "uri",
                    "label"=> "もとのツイートを見る",
                    "uri"=> $twitterURL
                  ]
                ]
            ];
            $messages = cardReply($res, $firstCardTexts);
        } else {
            $message = '関連する流言はありませんでした';
            $messages = simpleReply([$message]);
        }
    } else {
        // 配列がからの場合
        $message = 'ツイートが取得できませんでした';
        $messages = simpleReply([$message]);
    }
    return $messages;
}

function cutTweet($text) {
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

function getRumorsFromFreeWord($userText) {
    require './rumor-background/RestAPI/getSimTweet.php';
    $userText = cleanText($userText);
    $res = getSimTweet($userText);
    $messages = [];
    if($res) {
        $message = '関係ありそうな怪しい情報はこれだよ' . "\n\n";
        foreach ($res as $r) {
            $message = $message . '・' . $r;
            if ($r !== end($res)) {
                // 最後
                $message = $message . "\n\n";
            }
        }
    } else {
        $message = '関連する流言はありませんでした';
    }
    $messages = simpleReply([$message]);
    return $messages;
}

function getFiveRatestRumor() {
    require './rumor-background/RestAPI/getRatestRumor.php';
    $res = getRatestRumor();
    $firstCardTexts = [
        "title"=> "最新の流言→",
        "text"=> "こんな怪しい情報があるよ！注意してね！",
        "actions"=> [
          [
            "type"=> "uri",
            "label"=> "ランキングを見る",
            "uri"=> "http://mednlp.jp/~miyabe/rumorCloud/rumorlist.cgi"
          ]
        ]
    ];
    return cardReply($res, $firstCardTexts);
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

function cardReply($rumors, $firstCardTexts) {
    $cardMessages = [];
    $file = "./carousel.json";
    $json = file_get_contents($file);
    $array = json_decode($json, true);
    array_push(
        $array["template"]["columns"], $firstCardTexts
    );
    for($i=0; $i<count($rumors); $i++) {
        array_push(
            $array["template"]["columns"],
            [
                "title"=> $rumors[$i]['fix'] . "人 が疑ってます",
                "text"=> $rumors[$i]['contents'],
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
    return $cardMessages;
}