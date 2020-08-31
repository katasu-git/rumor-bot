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

function replyCards($rumors, $handle) {
    $cardMessages = [];
    $file = dirname(__FILE__) . "/flex.json";
    $json = file_get_contents($file);
    $array = json_decode($json, true);

    for($i=0; $i<5; $i++) {
        $updown = returnUpDown($rumors[$i]['updown']);
        $fix = (string) $rumors[$i]['fix'];
        $id = (string) $rumors[$i]['id'];
        $rank = $i + 1;

        if($handle == "handle-latest-rumor") {
          $topColor = "#6AC0C8";
          $label = "【今日の新着】";
          $subText = "疑っている人：$fix" . "人";
    
        } else if($handle == "handle-sudden-rise") {
          $topColor = "#EF7943";
          $subText = "疑っている人：$updown" . "人（昨日より）";
          $label = "【注目・急上昇】";

        } else if($handle == "handle-ranking") {
          $topColor = "#74BE89";
          $subText = "疑っている人：$fix" . "人";
          $label = "【ランキング $rank" . "位】";

        } else {
          $topColor = "#C6CF63";
          $subText = "疑っている人：$fix" . "人";
          $label = "【類似】";

        }
        $content = $rumors[$i]['content'];
        $detailURL = "https://liff.line.me/1654776413-dpYy83Wb/?id=$id&path=detail";

        array_push(
          $array["contents"]["contents"],
            [
              "type"=> "bubble",
              "direction"=> "ltr",
              "header"=> [
                "type"=> "box",
                "layout"=> "vertical",
                "contents"=> [
                  [
                    "type"=> "text",
                    "text"=> "$label",
                    "size"=> "xs",
                    "align"=> "center",
                    "color"=> "#FFFFFF"
                  ],
                  [
                    "type"=> "text",
                    "text"=> "$subText",
                    "size"=> "sm",
                    "align"=> "center",
                    "margin"=> "sm",
                    "weight"=> "bold",
                    "color"=> "#FFFFFF",
                    "wrap"=> false
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
                        "text"=> "【怪しい情報】",
                        "size"=> "xs",
                        "align"=> "center",
                        "weight"=> "regular",
                        "color"=> "#797979"
                      ],
                      [
                        "type"=> "text",
                        "text"=> "$content",
                        "margin"=> "lg",
                        "size"=> "lg",
                        "align"=> "center",
                        "gravity"=> "center",
                        "weight"=> "bold",
                        "color"=> "#4B4B4B",
                        "wrap"=> true
                      ],
                      [
                        "type"=> "spacer"
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
                      "uri"=> "$detailURL"
                    ],
                    "color"=> "#009FB9",
                    "style"=> "link",
                    "gravity"=> "center"
                  ]
                ]
              ],
              "styles"=> [
                "header"=> [
                  "backgroundColor"=> "$topColor"
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