<?php
// Include the TourCMS library
include 'tourcms.php';

// Marketplace account ID
// Leave this as zero if you are a supplier (i.e. not a Marketplace partner)
$marketplace_account_id = 0;

// Marketplace account ID
// Leave this as zero if you are a Marketplace partner (i.e. not a supplier)
$channel_id = 0;

// API Private Key (log in to TourCMS to get yours)
$api_private_key = "";

// Result type required
// 'raw' or 'simplexml', if not supplied defaults to 'raw'
$result_type = 'simplexml';

// Create a new Instance of the TourCMS API class
$tc = new TourCMS($marketplace_account_id, $api_private_key, $result_type);

// We can now run whichever methods we want
// Here we'll check our API rate limit
$result = $tc->api_rate_limit_status($channel_id); 
?>
<pre>
	<?php print_r($result); ?>
</pre>