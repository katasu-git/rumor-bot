<?php

function cardReply($rumors) {
    $cardMessages = [];
    $file = dirname(__FILE__) . "/carousel.json";
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