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
                  "direction"=> "ltr",
                  "header"=> [
                    "type"=> "box",
                    "layout"=> "horizontal",
                    "contents"=> [
                      [
                        "type"=> "text",
                        "text"=> $rumors[$i]['contents'],
                        "margin"=> "xxl",
                        "size"=> "md",
                        "align"=> "start",
                        "gravity"=> "top",
                        "weight"=> "bold",
                        "color"=> "#FFFFFF",
                        "wrap"=> true
                      ]
                    ]
                  ],
                  "body"=> [
                    "type"=> "box",
                    "layout"=> "horizontal",
                    "contents"=> [
                      [
                        "type"=> "box",
                        "layout"=> "vertical",
                        "contents"=> [
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
                            "size"=> "sm",
                            "align"=> "center",
                            "weight"=> "regular",
                            "color"=> "#00B900"
                          ],
                          [
                            "type"=> "text",
                            "text"=> "この情報を" . $rumors[$i]['fix'] . "人が疑っています",
                            "margin"=> "md",
                            "size"=> "sm",
                            "align"=> "center",
                            "color"=> "#4B4C4B"
                          ]
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
                        "margin"=> "none",
                        "height"=> "sm",
                        "style"=> "link",
                        "gravity"=> "center"
                      ]
                    ]
                  ],
                  "styles"=> [
                    "header"=> [
                      "backgroundColor"=> "#00B900"
                    ],
                    "body"=> [
                      "backgroundColor"=> "#FFFFFF",
                      "separator"=> true
                    ],
                    "footer"=> [
                      "separator"=> true
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
                  "direction"=> "ltr",
                  "header"=> [
                    "type"=> "box",
                    "layout"=> "horizontal",
                    "contents"=> [
                      [
                        "type"=> "text",
                        "text"=> $rumors[$i]['contents'],
                        "margin"=> "xxl",
                        "size"=> "md",
                        "align"=> "start",
                        "gravity"=> "top",
                        "weight"=> "bold",
                        "color"=> "#FFFFFF",
                        "wrap"=> true
                      ]
                    ]
                  ],
                  "body"=> [
                    "type"=> "box",
                    "layout"=> "horizontal",
                    "contents"=> [
                      [
                        "type"=> "box",
                        "layout"=> "vertical",
                        "contents"=> [
                          [
                            "type"=> "text",
                            "text"=> "この情報を" . $rumors[$i]['fix'] . "人が疑っています",
                            "margin"=> "md",
                            "size"=> "sm",
                            "align"=> "center",
                            "color"=> "#4B4C4B"
                          ]
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
                        "margin"=> "none",
                        "height"=> "sm",
                        "style"=> "link",
                        "gravity"=> "center"
                      ]
                    ]
                  ],
                  "styles"=> [
                    "header"=> [
                      "backgroundColor"=> "#00B900"
                    ],
                    "body"=> [
                      "backgroundColor"=> "#FFFFFF",
                      "separator"=> true
                    ],
                    "footer"=> [
                      "separator"=> true
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