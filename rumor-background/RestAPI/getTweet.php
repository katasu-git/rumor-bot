<?php
/**************************************************

	[GET statuses/show/:id]のお試しプログラム

	認証方式: アクセストークン

	配布: SYNCER
	公式ドキュメント: https://dev.twitter.com/rest/reference/get/statuses/show/id
	日本語解説ページ: https://syncer.jp/Web/API/Twitter/REST_API/GET/statuses/show/id/

**************************************************/
//twitterURL=https://twitter.com/hatanohiroshi/status/1261752685545439232?s=12

function getTweet($twitterURL) {

	// $twitterURL = $_GET['twitterURL'];
	$URLs = explode("/", $twitterURL);
	//var_dump( $URLs );
	$tweetId =$URLs[5];
	//$tweetId = "1261752685545439232?s=12";
	
		// 設定
		$api_key = 'PPHBl3cB7OyQjzcdgKFBgD9hU' ;		// APIキー
		$api_secret = '7WGpQ6PzsRKk36ZarGxVh7384axDckv84O3MD7RpiP7rVfA2A7' ;		// APIシークレット
		$access_token = '429673952-OCZIAlbGhLn6s0sGjunEi32Mi8t7vF8LIkF2eKYA' ;		// アクセストークン
		$access_token_secret = 'hnbcNs2ysZxzv60oaG0xPgDR36d2JgSdapR8UjuHbPVO4' ;		// アクセストークンシークレット
		$request_url = 'https://api.twitter.com/1.1/statuses/show.json' ;		// エンドポイント
		$request_method = 'GET' ;
	
		// パラメータA (オプション)
		$params_a = array(
			"id" => $tweetId,
			"tweet_mode" => "extended" #ここ必要
	//		"trim_user" => "true",
	//		"include_my_retweet" => "true",
	//		"include_entities" => "true",
		) ;
	
		// キーを作成する (URLエンコードする)
		$signature_key = rawurlencode( $api_secret ) . '&' . rawurlencode( $access_token_secret ) ;
	
		// パラメータB (署名の材料用)
		$params_b = array(
			'oauth_token' => $access_token ,
			'oauth_consumer_key' => $api_key ,
			'oauth_signature_method' => 'HMAC-SHA1' ,
			'oauth_timestamp' => time() ,
			'oauth_nonce' => microtime() ,
			'oauth_version' => '1.0' ,
		) ;
	
		// パラメータAとパラメータBを合成してパラメータCを作る
		$params_c = array_merge( $params_a , $params_b ) ;
	
		// 連想配列をアルファベット順に並び替える
		ksort( $params_c ) ;
	
		// パラメータの連想配列を[キー=値&キー=値...]の文字列に変換する
		$request_params = http_build_query( $params_c , '' , '&' ) ;
	
		// 一部の文字列をフォロー
		$request_params = str_replace( array( '+' , '%7E' ) , array( '%20' , '~' ) , $request_params ) ;
	
		// 変換した文字列をURLエンコードする
		$request_params = rawurlencode( $request_params ) ;
	
		// リクエストメソッドをURLエンコードする
		// ここでは、URL末尾の[?]以下は付けないこと
		$encoded_request_method = rawurlencode( $request_method ) ;
	 
		// リクエストURLをURLエンコードする
		$encoded_request_url = rawurlencode( $request_url ) ;
	 
		// リクエストメソッド、リクエストURL、パラメータを[&]で繋ぐ
		$signature_data = $encoded_request_method . '&' . $encoded_request_url . '&' . $request_params ;
	
		// キー[$signature_key]とデータ[$signature_data]を利用して、HMAC-SHA1方式のハッシュ値に変換する
		$hash = hash_hmac( 'sha1' , $signature_data , $signature_key , TRUE ) ;
	
		// base64エンコードして、署名[$signature]が完成する
		$signature = base64_encode( $hash ) ;
	
		// パラメータの連想配列、[$params]に、作成した署名を加える
		$params_c['oauth_signature'] = $signature ;
	
		// パラメータの連想配列を[キー=値,キー=値,...]の文字列に変換する
		$header_params = http_build_query( $params_c , '' , ',' ) ;
	
		// リクエスト用のコンテキスト
		$context = array(
			'http' => array(
				'method' => $request_method , // リクエストメソッド
				'header' => array(			  // ヘッダー
					'Authorization: OAuth ' . $header_params ,
				) ,
			) ,
		) ;
	
		// パラメータがある場合、URLの末尾に追加
		if( $params_a ) {
			$request_url .= '?' . http_build_query( $params_a ) ;
		}
	
		// オプションがある場合、コンテキストにPOSTフィールドを作成する (GETの場合は不要)
	//	if( $params_a ) {
	//		$context['http']['content'] = http_build_query( $params_a ) ;
	//	}
	
		// cURLを使ってリクエスト
		$curl = curl_init() ;
		curl_setopt( $curl, CURLOPT_URL , $request_url ) ;
		curl_setopt( $curl, CURLOPT_HEADER, 1 ) ; 
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST , $context['http']['method'] ) ;	// メソッド
		curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER , false ) ;	// 証明書の検証を行わない
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER , true ) ;	// curl_execの結果を文字列で返す
		curl_setopt( $curl, CURLOPT_HTTPHEADER , $context['http']['header'] ) ;	// ヘッダー
	//	if( isset( $context['http']['content'] ) && !empty( $context['http']['content'] ) ) {		// GETの場合は不要
	//		curl_setopt( $curl , CURLOPT_POSTFIELDS , $context['http']['content'] ) ;	// リクエストボディ
	//	}
		curl_setopt( $curl , CURLOPT_TIMEOUT , 5 ) ;	// タイムアウトの秒数
		$res1 = curl_exec( $curl ) ;
		$res2 = curl_getinfo( $curl ) ;
		curl_close( $curl ) ;
	
		// 取得したデータ
		$json = substr( $res1, $res2['header_size'] ) ;		// 取得したデータ(JSONなど)
		$header = substr( $res1, 0, $res2['header_size'] ) ;	// レスポンスヘッダー (検証に利用したい場合にどうぞ)
	
		// JSONをオブジェクトに変換
		$obj = json_decode( $json, true ) ;
		print_r($obj);
		return $obj;

}