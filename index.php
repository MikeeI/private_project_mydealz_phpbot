<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once 'controller/controller_api_interaction.php';
require_once 'controller/controller_data.php';

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Add request, authorize, etc to end of URL based on what call you're making
//$url = "http://www.mydealz.de/rest_api/v2/thread/hottest?days=1&page=1&limit=3&local=0";
//$url = "https://www.mydealz.de/rest_api/v2/thread?order_by=new&include_deleted=false&include_moderated=false&include_scheduled=false&expired=true&local=true&type_id=1&merchant_id=3&limit=50";


function secToHR($seconds)
{
	$hours = floor($seconds / 3600);
	$minutes = floor(($seconds / 60) % 60);
	$seconds = $seconds % 60;
	return "$hours:$minutes:$seconds";
}


$date = new DateTime();


$json = get_threads_by_merchant(3);

//var_dump($json->data);


echo '<table>';
echo '<thead><tr><th>title</td><td>price</td><td>number</td></tr></thead>';
foreach ($json->data as $thread) {
	echo '<tr>';
	echo '<td>' . secToHR($date->getTimestamp() - $thread->submitted) . "</td>";
	echo '<td>' . $thread->thread_id . '</td>';
	echo '<td>' . $thread->price . '</td>';
	if (isset($thread->price_discount)) {
		echo '<td>' . $thread->price_discount . '</td>';
	} else {
		echo '<td>' . "" . '</td>';

	}

	echo '<td>' . $thread->title . '</td>';

	echo '</tr>';
}
echo '</table>';