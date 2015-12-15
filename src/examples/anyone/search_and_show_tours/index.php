<?php

// Include our API settings and wrapper
include '../../config.php';

// Pagination
$per_page = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Set a querystring for the search
$parameters = array(
					"per_page" => $per_page,
					"page" => $page,
					);

$querystring = http_build_query($parameters);

// Call the TourCMS API method to search for Tours/Hotels
$result = $tc->search_tours($querystring, $channel_id);

?><!DOCTYPE html>
<html lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Search and Show Tours</title>
		<link rel="stylesheet" href="../../css/normalize.css" />
		<link rel="stylesheet" href="../../css/examples.css" />
	</head>
	<body>
		<h1>Search and Show Tours</h1>
		<p>Display a list of tours, click through to a separate page showing further details.</p>
		<p>
		<?php
		// Check if the result is ok
		if($result->error=="OK")
		{
			// Calculate how many pages we have
			$pages = ceil($result->total_tour_count / $per_page);

			?>
			<p>Page <strong><?php print $page; ?></strong> of <strong><?php print $pages; ?></strong></p>
			<?php

			// If so loop through and display our Tours/Hotels
			foreach ($result->tour as $tour) :

				// We are going to keep the customer on our site to show them the tour details
				// rather than redirect them to the 'tour_url' configured on the tour
				// so, we build our own link
				//
				// In reality this would likely use a search engine friendly URL
				$tour_url = "show.php?t=" . $tour->tour_id;

				// If we have no channel ID configured in our config
				// (i.e. we are an agent) we also need to pass the Channel ID
				if($channel_id == 0)
					$tour_url .= "&c=" . $tour->channel_id;


				?>
				<div class="tour">
					<h4>
						<a href="<?php print $tour_url; ?>">
							<?php print $tour->tour_name; ?>
						</a>
					</h4>

					<p class="summary"><?php print $tour->summary; ?></p>

					<p><?php print $tour->shortdesc; ?></p>

					<p class="buttons">
						<a href="<?php print $tour_url; ?>">View full details</a>
					</p>
				</div>
				<p class="pagination">
				<?php
			endforeach;

			// Basic pagination
			if($page > 1)
			{
				// First
				print '<a href="?page=1">&lt;&lt; First page</a>';
				// Previous
				print '<a href="?page=' . ($page - 1) . '">&lt; Previous page</a>';
			}
			if($page < $pages)
			{
				// Next
				print '<a href="?page=' . ($page + 1) . '">Next page &gt;</a>';
				// Last
				print '<a href="?page=' . $pages . '">Last page &gt;&gt;</a>';
			}


			?>
				</p>
			<?php

//			print "<pre>";
//			print_r($result);
//			print "</pre>";
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
