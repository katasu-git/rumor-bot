<?php

function backMessageToUser($replyToken, $messages) {

    // 返信部分
    $accessToken = file_get_contents('/home/nishimura/public_html/rumor-bot/conf/lineAccessToken.txt');
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

}