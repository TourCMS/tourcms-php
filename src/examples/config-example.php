<?php
	// Marketplace account ID
	// Leave this as zero if you are a supplier (i.e. not a Marketplace partner)
	$marketplace_account_id = 0;

	// Channel ID
	// Leave this as zero if you are a Marketplace partner (i.e. not a supplier)
	$channel_id = 0;

	// API Private Key (log in to TourCMS to get yours)
	$api_private_key = "";

	// Result type required
	// 'raw' or 'simplexml', if not supplied defaults to 'raw'
	$result_type = 'simplexml';

        // Timeout will set the maximum execution time, in seconds. If set to zero, no time limit is imposed.
        $timeout = 0;

	// Include the TourCMS library
	// Update this to point to the tourcms.php on your server
	include $_SERVER['DOCUMENT_ROOT'] . '/vendor/tourcms/tourcms-php/src/TourCMS.php';

	Use TourCMS\Utils\TourCMS as TourCMS;

	$tc = new TourCMS($marketplace_account_id, $api_private_key, "simplexml", $timeout);
?>
