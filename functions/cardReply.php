<?php

function cardReply($rumors) {
    $cardMessages = [];
    $file = dirname(__FILE__) . "/flex.json";
    $json = file_get_contents($file);
    $array = json_decode($json, true);

    if($rumors[0]['slashKeywords']) {
        for($i=0; $i<count($rumors); $i++) {
            array_push(
                $array["contents"]["contents"],
                [
                    "type"=> "bubble",
                    "body"=> [
                      "type"=> "box",
                      "layout"=> "horizontal",
                      "spacing"=> "md",
                      "contents"=> [
                        [
                          "type"=> "box",
                          "layout"=> "vertical",
                          "contents"=> [
                            [
                                "type"=> "text",
                                "text"=> $rumors[$i]['contents'],
                                "margin"=> "xxl",
                                "size"=> "lg",
                                "align"=> "start",
                                "gravity"=> "top",
                                "weight"=> "bold",
                                "color"=> "#4B4C4B",
                                "wrap"=> true
                            ],
                            [
                                "type"=> "separator",
                                "margin"=> "xxl"
                            ],
                            [
                              "type"=> "text",
                              "text"=> "【キーワード】",
                              "margin"=> "xxl",
                              "size"=> "sm",
                              "align"=> "center",
                              "weight"=> "regular",
                              "color"=> "#00B900",
                              "wrap"=> true
                            ],
                            [
                              "type"=> "text",
                              "text"=> $rumors[$i]['slashKeywords'],
                              "margin"=> "sm",
                              "size"=> "sm",
                              "align"=> "center",
                              "weight"=> "regular",
                              "color"=> "#00B900"
                            ],
                            [
                              "type"=> "text",
                              "text"=> "この情報を" . $rumors[$i]['fix'] . "人が疑っています",
                              "margin"=> "xl",
                              "size"=> "sm",
                              "align"=> "center",
                              "color"=> "#4B4C4B"
                            ],
                            [
                                "type"=> "separator",
                                "margin"=> "xxl"
                            ],
                          ]
                        ]
                      ]
                    ],
                    "footer"=> [
                      "type"=> "box",
                      "layout"=> "horizontal",
                      "contents"=> [
                        [
                          "type"=> "button",
                          "action"=> [
                            "type"=> "uri",
                            "label"=> "もっと詳しく！",
                            "uri"=> $rumors[$i]['url']
                          ],
                          "color"=> "#009FB9",
                          "style"=> "link",
                          "height"=> "sm",
                          "gravity"=> "center"
                        ]
                      ]
                    ]
                ]
            );
        }
    } else {
        for($i=0; $i<count($rumors); $i++) {
            array_push(
                $array["contents"]["contents"],
                [
                    "type"=> "bubble",
                    "body"=> [
                      "type"=> "box",
                      "layout"=> "horizontal",
                      "spacing"=> "md",
                      "contents"=> [
                        [
                          "type"=> "box",
                          "layout"=> "vertical",
                          "contents"=> [
                            [
                                "type"=> "text",
                                "text"=> $rumors[$i]['contents'],
                                "margin"=> "xxl",
                                "size"=> "lg",
                                "align"=> "start",
                                "gravity"=> "top",
                                "weight"=> "bold",
                                "color"=> "#4B4C4B",
                                "wrap"=> true
                            ],
                            [
                                "type"=> "separator",
                                "margin"=> "xxl"
                            ],
                            [
                              "type"=> "text",
                              "text"=> "この情報を" . $rumors[$i]['fix'] . "人が疑っています",
                              "margin"=> "xl",
                              "size"=> "sm",
                              "align"=> "center",
                              "color"=> "#4B4C4B"
                            ],
                            [
                                "type"=> "separator",
                                "margin"=> "xxl"
                            ],
                          ]
                        ]
                      ]
                    ],
                    "footer"=> [
                      "type"=> "box",
                      "layout"=> "horizontal",
                      "contents"=> [
                        [
                          "type"=> "button",
                          "action"=> [
                            "type"=> "uri",
                            "label"=> "もっと詳しく！",
                            "uri"=> $rumors[$i]['url']
                          ],
                          "color"=> "#009FB9",
                          "style"=> "link",
                          "height"=> "sm",
                          "gravity"=> "center"
                        ]
                      ]
                    ]
                ]
            );
        }
    }
    array_push(
        $cardMessages, $array
    );
    return $cardMessages;
}

function cutText($text) {
    //文字数の上限
    $limit = 30;
    if(mb_strlen($text) > $limit) { 
        $cutText = mb_substr($text,0,$limit, "UTF-8");
        return $cutText . "...";
    } else {
        return $text;
    }
}