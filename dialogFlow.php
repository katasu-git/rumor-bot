<?php

ini_set('display_errors', "On"); //エラー表示
require 'vendor/autoload.php';
use Google\Cloud\Dialogflow\V2\AgentsClient;
use Google\Cloud\Dialogflow\V2\EntityTypesClient;
use Google\Cloud\Dialogflow\V2\IntentsClient;
use Google\Cloud\Dialogflow\V2\SessionEntityTypesClient;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Google\Cloud\Dialogflow\V2\QueryParameters;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryResult;
use Google\Protobuf\Internal\RepeatedField;
use Google\Rpc\Status;
use Google\Protobuf\Struct;
use Google\Protobuf\Internal\MapField;
use Google\Protobuf\Value;
use Google\Cloud\Dialogflow\V2\DetectIntentResponse;
use Google\Cloud\Dialogflow\V2\Context;
use Google\Cloud\Dialogflow\V2\Intent;
use Google\ApiCore\PagedListResponse;

function analyzeText($speachText) {
    // サービスアカウントパスとプロジェクトIDを設定
    $serviceAccountPath = "./conf/linerumor-tdljtl-744073035215.json";
    $projectId = "linerumor-tdljtl";
    $config = [
        'credentialsConfig' => [
            'keyFile' => $serviceAccountPath,
        ],
        'projectId' => $projectId,
    ];
    // TODO:ここに処理を記載

    // 利用する言語コードを設定
    $languageCode = 'ja';
    // セッションIDを生成
    $sessionId = uniqid("", true);
    // 解析するテキストを設定
    // $speachText = 'こんにちは！';
    $sessionsClient = new SessionsClient($config);
    try {
        // セッションクライアントを設定
        $formattedSession = $sessionsClient->sessionName($projectId, $sessionId);
        // テキストを設定
        $textInput = new TextInput();
        $textInput->setText($speachText);
        $textInput->setLanguageCode($languageCode);
        // パラメータを設定
        $queryParameters = new QueryParameters();
        $queryParameters->setTimeZone('Asia/Tokyo');
        // クエリインプットを作成
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);
        $optionalArgs = [
            'queryParams' => $queryParameters,
        ];
        // テキストを検証
        $response = $sessionsClient->detectIntent($formattedSession, $queryInput, $optionalArgs);
        $queryResult = $response->getQueryResult();
        // レスポンス結果を取得
        $responseJsonString = $response->serializeToJsonString();
        echo $responseJsonString;
        return $responseJsonString;
    } catch(Exception $e) {
        echo "エラー";
        header('HTTP', true, 400);
    } finally {
        $sessionsClient->close();
    }

}

// analyzeText("aaaaa"); どうもNo Mattch インテントの処理にbccompとかいうphpパッケージが必要らしい