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

	// Include the TourCMS library
	include '../tourcms.php';

	Use TourCMS\Utils\TourCMS as TourCMS;

	$tc = new TourCMS($marketplace_account_id, $api_private_key, "simplexml");
?>
