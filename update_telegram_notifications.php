<?php

require_once 'controller/controller_config.php';
require_once 'controller/controller_api_interaction.php';
require_once 'controller/controller_data.php';
require_once 'controller/controller_messaging.php';

require_once 'library/lib_timetracker.php';
require_once __DIR__ . '/vendor/autoload.php';

$json = get_threads_by_merchant(3, 50);

foreach ($json->data as $thread) {
	if ($thread->group_display_summary === "Lebensmittel & Haushalt") {
		continue;
	}

	if (isset($thread->price_discount) && isset($thread->price)) {
		echo $thread->thread_id . " - " . $thread->price_discount . " - " . $thread->price."€<br>";

		if (($thread->price_discount > 90) && ($thread->price > 0.1)) {
			send_notification($thread);
			echo "SEND<br><br>";
			continue;
		}

		if (($thread->price_discount > 50) && ($thread->price > 3)) {
			send_notification($thread);
			echo "SEND<br><br>";

			continue;
		}
	}


}

function send_notification($thread)
{
	$url_deal = 'https://www.mydealz.de/visit/thread/' . $thread->thread_id;
	$asin = get_ASIN_from_URL($url_deal);
	$url_graph = get_image_price_graph($asin);


	if (create_Notification_element($thread)) {
		$url_image = create_Notification_image($thread, $url_graph);
		$message = create_Telegram_Deal_Message($thread, $url_image);

		send_Telegram_Message(bot_chat_id, $message);

	}
}