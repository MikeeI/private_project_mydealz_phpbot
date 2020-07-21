<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;


function get_ASIN_from_URL($url)
{
	global $timer;
	$timer->add("CURLINFO_EFFECTIVE_URL_begin");

	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, '');
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_TCP_FASTOPEN, 1);
	curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
	curl_setopt($ch, CURLOPT_ENCODING, "");

	$response = curl_exec($ch);
	$url_amazon = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);

	$timer->add("CURLINFO_EFFECTIVE_URL_end");

	preg_match('/(?:dp|o|gp|-|dp\/product|gp\/product)\/(B[0-9]{2}[0-9A-Z]{7}|[0-9]{9}(?:X|[0-9]))/', $url_amazon, $asin_arr);

	if (isset($asin_arr[1])) {
		return $asin_arr[1];
	} else {
		return null;
	}
}

function get_image_price_graph($ASIN, $domain = "de")
{
	if ($ASIN != null) {
		$url = "https://graph.keepa.com/pricehistory.png?domain=" . $domain . "&asin=" . $ASIN;
	} else {
		$url = "data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==";
	}

	return $url;
}

function get_threads_by_merchant($merchant_id, $filter = null)
{
	$base_url = "https://www.mydealz.de/rest_api/v2/thread?order_by=new";
	$api_url = $base_url . "&include_deleted=true&include_moderated=true&include_scheduled=true&expired=true&local=false&type_id=1&merchant_id=" . $merchant_id . "&limit=20";
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

