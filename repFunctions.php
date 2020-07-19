<?php
function getRumorsFromTweet($twitterURL) {
    require_once './rumor-background/RestAPI/getSimTweet.php';
    require_once './rumor-background/RestAPI/getTweet.php';
    $res = getTweet($twitterURL); // ツイートのリンクからツイートを取得
    
    if (!$res['full_text']) {
        $messages = simpleReply(['ツイートが取得できませんでした']);
        return $messages;
    }

    $twitterText = cleanText($res['full_text']);
    $res = getSimTweet($twitterText);

    if(!$res) {
        $messages = simpleReply(['関連する流言はありませんでした']);
        return $messages;
    }

    $twitterText = cutText($twitterText);
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
    return $messages;
}

function getRumorsFromFreeWord($userText) {
    require_once './rumor-background/RestAPI/getSimTweet.php';
    $userText = cleanText($userText);
    $res = getSimTweet($userText);
    $firstCardTexts = [
        "title"=> "関係ありそうなデマ→",
        "text"=> $userText,
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

function getFiveRatestRumor() {
    require_once './rumor-background/RestAPI/getRatestRumor.php';
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
    return $cardMessages;
}