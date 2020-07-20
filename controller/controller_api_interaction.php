<?php

require_once __DIR__ . '/../vendor/autoload.php';


use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;

define('consumer_key', '83561944886fdab16690e78a1f5f5559846dd86d');
define('consumer_secret', 'f0561bf4d666c1cfe942390bc2761a9ce02cedb8');


function get_threads_by_merchant($merchant_id)
{
	$api_url = "https://www.mydealz.de/rest_api/v2/thread?order_by=new&include_deleted=false&include_moderated=false&include_scheduled=false&expired=true&local=true&type_id=1&merchant_id=3&not_after=1594904753&limit=25";
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

	$oauth = array('oauth_consumer_key' => consumer_key,
		'oauth_nonce' => time(),
		'oauth_signature_method' => 'HMAC-SHA1',
		'oauth_timestamp' => time(),
		'oauth_version' => '1.0');

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

