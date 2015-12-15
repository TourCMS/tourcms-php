<?php

// Include our API settings and wrapper
include '../../config.php';

// Call the TourCMS API method to check the API rate limit status
$result = $tc->api_rate_limit_status($channel_id);

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>API rate limit status</title>
		<link rel="stylesheet" href="../../css/normalize.css" />
		<link rel="stylesheet" href="../../css/examples.css" />
	</head>
	<body>
		<h1>API Rate limit status</h1>
		<p>
		<?php
		// Check if the result is ok
		if($result->error=="OK") {
			// If so output current hits and limits
			print $result->remaining_hits . " of " . $result->hourly_limit . " hits remaining this hour.";
		} else {
			// If not output the error
			print "There has been an error: ";
			print $result->error;
		}
		?>
		</p>
		<p><a href="../../">↩ Back to API examples</a></p>
	</body>
</html>
