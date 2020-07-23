<?php

function createStickerMessages() {
    $packageId = "1153" . (string)mt_rand(7, 9);
    if($packageId == "11537") {
        $stickerId = "520027" . (string)mt_rand(34, 73);
    } elseif($packageId == "11538") {
        $stickerId = "51626" . (string)mt_rand(494, 533);
    } else {
        $stickerId = "521141" . (string)mt_rand(11, 49);
    }
    $messages = [
        "type"=> "sticker",
        "packageId"=> $packageId,
        "stickerId"=> $stickerId
    ];
    return $messages;
}