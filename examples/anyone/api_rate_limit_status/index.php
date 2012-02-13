<?php
// Include the TourCMS library
include '../../../tourcms.php';

// Include our API settings
include '../../../config.php';

// We can now run whichever methods we want
// Here we'll check our API rate limit
$result = $tc->api_rate_limit_status($channel_id); 
?>
<pre>
	<?php print_r($result); ?>
</pre>