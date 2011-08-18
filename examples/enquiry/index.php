<?php
	// Include the TourCMS API class
	include '../../tourcms.php';
	
	// Include our API settings
	include '../config.php';
	
	// Partners override the Channel ID here if you want
	//$channel_id = 0;
	
	// Create a new Instance of the TourCMS API class
	$tc = new TourCMS($marketplace_account_id, $api_private_key, $result_type);
	
	$enquiry = new SimpleXMLElement('<enquiry />');
	$enquiry->addChild('title', 'Mr');
	$enquiry->addChild('firstname', 'Test');
	$enquiry->addChild('surname', 'McTest');
	$enquiry->addChild('enquiry_type', 'Brochure request');
	$enquiry->addChild('enquiry_detail', 'Testing testing');
	//$enquiry->addChild('enquiry_followup_date', '2012-04-01');
	
	$result = $tc->create_enquiry($enquiry, $channel_id);
?><pre><?php print_r($result); ?></pre>