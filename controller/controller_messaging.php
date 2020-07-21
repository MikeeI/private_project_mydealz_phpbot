<?php

require_once __DIR__ . '/../vendor/autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$bot_chat_id = '564580760';

function send_Telegram_Message($chat_id, $message){

	$telegram = new Telegram(bot_token);

	var_dump($telegram);
	$text = $telegram->Text();
	$chat_id = 564580760;
	$firstname = $telegram->FirstName();

	$text = create_Telegram_Deal_Message("iPad Pro 12.9", "1000", "30", "https://graph.keepa.com/pricehistory.png?domain=de&asin=B076PKD3WG", 1624338);
	$content = array(
		'chat_id' => $chat_id,
		'text' => $text,
		'parse_mode' => 'HTML',
		'disable_web_page_preview' => 'false'
	);

	$telegram->sendMessage($content);
}

function create_Telegram_Deal_Message($title, $price, $price_discount, $graph_url, $tread_id)
{
	$mydealz_link_redirect = 'https://www.mydealz.de/visit/thread/' . $tread_id;

	$graph_url = 'https://menschen-in-hanau.de/de/wp-content/uploads/daiga-ellaby-uooMllXe6gE-unsplash-quadrat.jpg';
	$line_1 = '<b>DEAL: ' . $title . '</b>' . PHP_EOL;
	$line_2 = 'Preis: ' . $price . PHP_EOL;
	$line_3 = 'Discount: ' . $price_discount . PHP_EOL;
	$line_4 = $graph_url . PHP_EOL;
	$line_5 = PHP_EOL;
	$line_6 = PHP_EOL;
	$line_7 = '<a href="' . $mydealz_link_redirect . '">MY DEALZ LINK</a>' . PHP_EOL;

	$message_string = '
	DEAL: <b>' . $title . '</b>' . PHP_EOL.
		'Preis: ' . $price . PHP_EOL.
		'Discount: ' . $price_discount . PHP_EOL.
		$graph_url . PHP_EOL.
		PHP_EOL.
		PHP_EOL.
		'<a href="' . $mydealz_link_redirect . '">MY DEALZ LINK</a>' . PHP_EOL;


	//$message_string = $line_1 . $line_2 . $line_3 . $line_4 . $line_5 . $line_6 . $line_7;

	echo $message_string;
	return $message_string;
}
