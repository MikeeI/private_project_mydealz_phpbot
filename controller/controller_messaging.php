<?php

require_once __DIR__ . '/../vendor/autoload.php';


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// import the Intervention Image Manager Class
use Intervention\Image\ImageManagerStatic as Image;

// configure with favored image driver (gd by default)
Image::configure(array('driver' => 'gd'));

function send_Telegram_Message($chat_id, $message)
{
	$telegram = new Telegram(bot_token);

	$content = array(
		'chat_id' => $chat_id,
		'text' => $message,
		'parse_mode' => 'HTML',
		'disable_web_page_preview' => 'false'
	);

	$telegram->sendMessage($content);
}

function create_Telegram_Deal_Message($thread, $graph_url = "")
{
	$title = $thread->title;
	$tread_id = $thread->thread_id;
	$price = $thread->price;
	$price_discount = $thread->price_discount;
	$temperature_rating = $thread->temperature_rating;

	$mydealz_link_redirect = 'https://www.mydealz.de/visit/thread/' . $tread_id;

	$message_string = '• DEAL: ' . "<b>" . $title . '</b>' . PHP_EOL;
	$message_string = $message_string . '• Preis: ' . "<b>" . $price . "€</b>" . PHP_EOL;
	$message_string = $message_string . '• Discount: ' . "<b>" . $price_discount . "%</b>" . PHP_EOL;
	$message_string = $message_string . '• Temperatur: ' . "<b>" . $temperature_rating . "C°</b>" . PHP_EOL;
	$message_string = $message_string . PHP_EOL;
	$message_string = $message_string . '• ' . $graph_url . PHP_EOL;
	$message_string = $message_string . PHP_EOL;
	$message_string = $message_string . PHP_EOL;
	//$message_string = $message_string . '<a href="' . $mydealz_link_redirect . '">Mydealz LINK</a>' . PHP_EOL;
	$message_string = $message_string . '• ' . $mydealz_link_redirect . PHP_EOL;

	return $message_string;
}

function create_Notification_element($thread, $graph_url = null)
{
	$filename = "data/notifications/notification_array_debug.file";
	$thread_id = "de_" . $thread->thread_id;

	$array_notification_new = array($thread_id => '1');
	$array_notifications_read = [];

	if (file_exists($filename)) {
		$string_data = file_get_contents($filename);
		$array_notifications_read = unserialize($string_data);

		highlight_string("" . var_export($array_notifications_read, true));

		if (is_bool($array_notifications_read) == true) {
			$send_notification = true;

		} else {
			if (array_key_exists($thread_id, $array_notifications_read) && ($array_notifications_read[$thread_id] == "1")) {
				$send_notification = false;
			} else {
				$send_notification = true;
			}
		}

	} else {
		$send_notification = true;
	}

	if (is_bool($array_notifications_read) == true) {
		$array_notifications = $array_notification_new;
	} else {
		$array_notifications = $array_notifications_read + $array_notification_new;

	}
	$string_data = serialize($array_notifications);
	file_put_contents($filename, $string_data);

	return $send_notification;
}

function check_Notification_element($thread_id)
{

}

function create_Notification_image($thread = null, $graph_url = null)
{
	$img_canvas = Image::canvas(500, 440, '#ffffff');

	if ($graph_url != null) {
		$img_canvas->insert($graph_url);
	}
	$img_canvas_mydealz = Image::make(preg_replace('/\\\\/', '', $thread->icon_detail_url));
	$img_canvas_mydealz->resize(null, 200, function ($constraint) {
		$constraint->aspectRatio();
	});

// draw a red line with 5 pixel width
	$img_canvas->line(0, 220, 500, 220, function ($draw) {
		$draw->color('#0088CC');
	});

	$path = folder_notification_images . 'de_' . $thread->thread_id . '.png';
	$url_base = 'http://data.geschmeidig.es/projects/mydealz_phpparserbot/';
	$img_canvas->insert($img_canvas_mydealz, 'bottom-center'); // add offset
	$img_canvas->save($path, 100);


	//echo '<img alt="" src="' . $url_base . $path . '">_</img>';

	return $url_base . $path;
}

