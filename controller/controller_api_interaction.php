<?php

require_once __DIR__ . '/../vendor/autoload.php';


use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

define('consumer_key', '83561944886fdab16690e78a1f5f5559846dd86d');
define('consumer_secret', 'f0561bf4d666c1cfe942390bc2761a9ce02cedb8');

function get_ASIN_from_URL($url)
{
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

	$output = curl_exec($ch);
	$url_amazon =  curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	//echo $output;
	curl_close($ch);

	$amazon_us_product_url = $url_amazon;
	$asin_arr = array();
	preg_match('/(?:dp|o|gp|-|dp\/product|gp\/product)\/(B[0-9]{2}[0-9A-Z]{7}|[0-9]{9}(?:X|[0-9]))/', $amazon_us_product_url, $asin_arr);
	if(isset($asin_arr[0]) && isset($asin_arr[1]))
	{
		$asin = $asin_arr[1];
		var_dump($asin);
		return $asin;

	}
	else
	{
		return null;
	}





}

function get_image_price_graph($ASIN, $domain = "de")
{
	if($ASIN != null){
		$url = "https://graph.keepa.com/pricehistory.png?domain=".$domain."&asin=".$ASIN;
	}
	else{
		$url = "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
	}

	return $url;
}

function get_threads_by_merchant($merchant_id, $filter = null)
{
	$base_url = "https://www.mydealz.de/rest_api/v2/thread?order_by=new";
	$api_url = $base_url . "&include_deleted=false&include_moderated=false&include_scheduled=true&expired=true&local=false&type_id=1&merchant_id=" . $merchant_id . "&limit=5";
	$response_json = send_mydealz_api_request($api_url);
	return $response_json;
}

function send_mydealz_api_request($api_url)
{
	$stack = HandlerStack::create();

	$oauth_guzzle_config = array(
		'consumer_key' => consumer_key,
		'consumer_secret' => consumer_secret,
		'signature_method' => Oauth1::SIGNATURE_METHOD_HMAC,
		'token_secret' => ''
	);

	$middleware = new Oauth1($oauth_guzzle_config);
	$stack->push($middleware);

	$client = new Client(array(
		'handler' => $stack,
		'verify' => false
	));

	try {
		$response = $client->get($api_url, array('auth' => 'oauth'));
		$json_string = $response->getBody()->getContents();
		$json = json_decode($json_string);
		return $json;

	} catch (\GuzzleHttp\Exception\GuzzleException $e) {
		echo $e->getMessage();
		return null;
	}

}

