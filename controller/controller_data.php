<?php

require_once __DIR__ . '/../vendor/autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$bot_token = '1352490642:AAGyWQrL1rfeHxuIVkO5UgyvjTHhKMtQk3U';
$bot_chat_id = '564580760';

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


$telegram = new Telegram($bot_token);

var_dump($telegram);
$text = $telegram->Text();
$chat_id = 564580760;
$firstname = $telegram->FirstName();


/*
$text = '
<b>bold</b>, <strong>bold</strong>
<i>italic</i>, <em>italic</em>
<u>underline</u>, <ins>underline</ins>
<s>strikethrough</s>, <strike>strikethrough</strike>, <del>strikethrough</del>
<b>bold <i>italic bold <s>italic bold strikethrough</s> <u>underline italic bold</u></i> bold</b>
<a href="https://static.mydealz.de/live/threads/thread_large/default/1624338_1.jpg">inline URL</a>
<a href="tg://user?id=123456789">inline mention of a user</a>
https://static.mydealz.de/live/threads/thread_large/default/1624338_1.jpg
<code>inline fixed-width code</code>
<pre>pre-formatted fixed-width code block</pre>
<pre><code class="language-python">pre-formatted fixed-width code block written in the Python programming language</code></pre>
';
*/
$text = create_Telegram_Deal_Message("iPad Pro 12.9", "1000", "30", "https://graph.keepa.com/pricehistory.png?domain=de&asin=B076PKD3WG", 1624338);
$content = array(
	'chat_id' => $chat_id,
	'text' => $text,
	'parse_mode' => 'HTML',
	'disable_web_page_preview' => 'false'
);

$telegram->sendMessage($content);
/*
var_dump($telegram->getMe());
var_dump($telegram);

$chat_id = $telegram->ChatID();

var_dump($chat_id);

$content = array('chat_id' => "1352490642", 'text' => 'Test');
$telegram->sendMessage($content);
*/
echo "Done";