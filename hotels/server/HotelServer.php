<?php
	namespace hotels\server;
	use hotels\service\HotelService;
  require_once '../../global.php';

  $options = [
    "uri" => 'http://' . $_ENV['URL'] . "/hotels/server/HotelServer.php",
	];

	$server = new \SoapServer(null, $options);
	$server->setClass(HotelService::class);
	$server->handle();
