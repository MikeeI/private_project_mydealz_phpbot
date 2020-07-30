<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Pdp\Domain;
use Pdp\PublicSuffix;
use Pdp\Rules;

function get_final_URL_from_URL($url)
{
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
	$url_final = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
	curl_close($ch);

	return $url_final;
}

function get_ASIN_from_URL($url)
{
	$url_amazon = get_final_URL_from_URL($url);

	preg_match('/(?:dp|o|gp|-|dp\/product|gp\/product)\/(B[0-9]{2}[0-9A-Z]{7}|[0-9]{9}(?:X|[0-9]))/', $url_amazon, $asin_arr);

	if (isset($asin_arr[1])) {
		return $asin_arr[1];
	} else {
		return null;
	}
}

function get_image_price_graph($ASIN, $url)
{
	$url_final = get_final_URL_from_URL($url);
	$tld = get_tld_from_url($url_final);
	if ($ASIN != null) {
		$url = "https://graph.keepa.com/pricehistory.png?domain=" . $tld . "&asin=" . $ASIN;
	} else {
		$url = "";
	}

	return $url;
}

function get_tld_from_url($URL)
{
	$URL = get_final_URL_from_URL($URL);
	$urlMap = array('com', 'co.uk', 'de', 'at','es','it','fr');

	$host = "";

	$urlData = parse_url($URL);
	$hostData = explode('.', $urlData['host']);
	$hostData = array_reverse($hostData);

	if (array_search($hostData[1] . '.' . $hostData[0], $urlMap) !== FALSE) {
		$host = $hostData[2] . '.' . $hostData[1] . '.' . $hostData[0];
	} elseif (array_search($hostData[0], $urlMap) !== FALSE) {
		$host = $hostData[1] . '.' . $hostData[0];
	}

	$host = explode(".", $host);
	echo $host[0]; // amazon
	echo $host[1]; // de

	return $host[1];

}

function get_threads_by_merchant($merchant_id, $limit = 50, $filter = null)
{
	$base_url = "https://www.mydealz.de/rest_api/v2/thread?order_by=new";
	$api_url = $base_url . "&include_deleted=true&include_moderated=true&include_scheduled=true&expired=true&local=false&type_id=1&merchant_id=" . $merchant_id . "&limit=" . $limit;
	$response_json = send_mydealz_api_request($api_url);

	return $response_json;
}


function modify_threads_by_filter($threads)
{

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

