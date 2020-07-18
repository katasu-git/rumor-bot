<?php

function getRumorsFromTweet($twitterURL) {
    require './rumor-background/RestAPI/getTweet.php';
    $res = getTweet($twitterURL); // ツイートのリンクからツイートを取得
    if ($res['full_text']) {
        $twitterText = cleanText($res['full_text']);
        require './rumor-background/RestAPI/getSimTweet.php';
        $res = getSimTweet($twitterText);
        if($res) {
            $message = $twitterText . "\n\nに関係ありそうな怪しい情報はこれだよ\n\n";
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
    } else {
        // 配列がからの場合
        $message = 'ツイートが取得できませんでした';
    }
    $messages = simpleReply([$message]);
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
    $message = 'こんな怪しい情報が出回っているよ！' . "\n\n";
    foreach($res as $r) {
        $message = $message . '・' . $r['contents'] . "\n" . $r['url'];

        if ($r !== end($res)) {
            // 最後
            $message = $message . "\n\n";
        }
    }
    $messages = simpleReply([$message]);
    return $messages;
}

function simpleReply($texts) {
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