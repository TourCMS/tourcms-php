<?php

// Include our API settings and wrapper
include '../../config.php';

// Tour info
$tour_id = isset($_GET['t']) ? (int)$_GET['t'] : 0;

// Use the channel ID from the config, if not set grab the channel from the URL
if($channel_id == 0) {
	$channel_id = isset($_GET['c']) ? (int)$_GET['c'] : 0;
}

// If Tour ID or Channel ID are zero redirect to the list page
if($tour_id == 0 || $channel_id == 0)
	header('location: index.php');

// Call the TourCMS API method to search for Tours/Hotels
$result = $tc->show_tour($tour_id, $channel_id);

// Helper function
function asterisk2Ul( $text ) {
		$text = preg_replace("/^\*+(.*)?/im","<ul><li>$1</li></ul>",$text);
		$text = preg_replace("/(\<\/ul\>\n(.*)\<ul\>*)+/","",$text);
		return nl2br($text);
}

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title><?php echo $result->tour->tour_name_long; ?></title>
		<link rel="stylesheet" href="../../css/normalize.css" />
		<link rel="stylesheet" href="../../css/examples.css" />
	</head>
	<body>


		<?php
		// Check if the result is ok
		if($result->error=="OK")
		{
		?>
				<h1>
					<?php echo $result->tour->tour_name_long; ?>
				</h1>
				<p style="text-align: center;"><em><?php echo $result->tour->summary; ?></em></p>
				<div class="tour tour-details">

					<!-- Images -->
					<div class="basic-image-wrapper-wrapper">
						<div class="basic-image-wrapper">
						<?php
						foreach ($result->tour->images->image as $image) {
							?>
							<img src="<?php echo $image->url; ?>"
							alt="<?php echo $image->image_desc; ?>">
							<?php
						}

						?>
						</div>
					</div>
					<!-- End Images -->

					<!-- Long / Short description -->
					<div style="padding-bottom: 20px;">
						<?php
						if(!empty($result->tour->longdesc)) {
							echo asterisk2Ul($result->tour->longdesc);
						} else {
							echo asterisk2Ul($result->tour->shortdesc);
						}
						?>
					</div>
					<!-- End Long / Short description -->

					<!-- Includes -->
					<?php if(!empty($result->tour->inc)): ?>

					<div>
						<h4>Includes</h4>
						<?php echo asterisk2Ul($result->tour->inc); ?>
					</div>
					<?php endif; ?>
					<!-- End Includes -->

					<!-- Excludes -->

					<?php if(!empty($result->tour->ex)): ?>
					<div>
						<h4>Excludes</h4>
						<?php echo asterisk2Ul($result->tour->ex); ?>
					</div>
					<?php endif; ?>
					<!-- End Excludes -->

					<p class="buttons">
						<?php
							$book_url =  empty($result->tour->book_url) ? $result->tour->tour_url : $result->tour->book_url;
						?>
						<a href="<?php print $book_url; ?>">Book online</a>
					</p>

				</div>
		<?php
		} else {
			// If not output the error
			print "There has been an error: ";
			print $result->error;
		}
		?>

		<p>The documentation on <a href="http://www.tourcms.com/support/api/mp/tour_show.php" target="_blank">Show Tour</a>  contains a  full list of fields available to you on this page.</p>
		<p>See <a href="http://toursift.com" target="_blank">TourSift</a> for a more fully featured example.</p>
		<p><a href="index.php">↩ Back to list of tours</a></p>
		<p><a href="../../">↩ Back to API examples</a></p>
	</body>
</html>
