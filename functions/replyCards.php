<?php

function returnUpDown($updown) {
    if($updown == 0) {
        return "±$updown";
    } else if($updown > 0) {
        return "+$updown";
    } else {
        return "-$updown";
    }
}

function replyCards($rumors) {
    $cardMessages = [];
    $file = dirname(__FILE__) . "/flex.json";
    $json = file_get_contents($file);
    $array = json_decode($json, true);

    for($i=0; $i<5; $i++) {
        $updown = returnUpDown($rumors[$i]['updown']);
        $fix = (string) $rumors[$i]['fix'];
        $id = (string) $rumors[$i]['id'];

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
                    "text"=> $rumors[$i]['content'],
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
                        "text"=> $fix . "人が疑っています($updown" . "人)",
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
                      "uri"=> "https://liff.line.me/1654776413-dpYy83Wb" . "/?id=$id&path=detail"
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

    array_push(
        $cardMessages, $array
    );
    return $cardMessages;
}