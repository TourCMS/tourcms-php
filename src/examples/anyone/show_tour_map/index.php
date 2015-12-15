<?php

// Include our API settings and wrapper
include '../../config.php';

// Tour info
$tour_id = isset($_GET['t']) ? (int)$_GET['t'] : '';

// Use the channel ID from the URL, fall back to the one in config
$channel_id = isset($_GET['c']) ? (int)$_GET['c'] : $channel_id;

if($tour_id != '' && $channel_id > 0) {
	// Call the TourCMS API method to Show the tour
	$result = $tc->show_tour($tour_id, $channel_id);
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

		<!-- Driver form, used to enter Tour ID and Channel ID -->
		<p>Enter a Tour ID and Channel ID then hit "Submit" to generate a map</p>
		<form method="get" class="driver">
			<label>Tour ID <input type="text" name="t" value="<?php echo $tour_id; ?>" /></label>
			<label>Channel ID<input type="text" name="c" value="<?php echo $channel_id; ?>" /></label>
			<input type="submit">
		</form>

		<?php

			// If we have called the API and got a response
			if(!empty($result->error)):

				// Response "error" will be OK if tour returned
				if($result->error=="OK"):
					?>

					<h1>
						<?php echo $result->tour->tour_name_long; ?>
					</h1>




<!-- Start map code -->

	<!-- Map container -->
	<div id="map-wrap" style="width: auto; height: 500px;">
		<div id="map-canvas" style="width: 100%; height: 100%;">

		</div>
	</div>

	<!-- Map JavaScript -->
	<script>

				// Function to generate our map
				// Called by the Google Maps script once it has loaded (see "loadMapScript" below)
				function initializeMap() {

					// Tour start location
					var tourLatLng = new google.maps.LatLng(<?php echo $result->tour->geocode_start; ?>);

					// Set default zoom level and center on tour start location
					var tourMapOptions = {
						zoom: 12,
						center: tourLatLng
					};

					// Generate map
					var tourMap = new google.maps.Map(document.getElementById('map-canvas'), tourMapOptions);

					// Drop a marker at the start location
					var tourMarker = new google.maps.Marker({
							position: tourLatLng,
							map: tourMap,
							title:"Start location"
					});
				}

				// Function to load the Google Maps script
				// Pass our "InitializeMap" function anme as callback so it gets called once loaded
				function loadMapScript() {

				  var script = document.createElement('script');
				  script.type = 'text/javascript';
				  script.src = 'https://maps.googleapis.com/maps/api/js?v=3.exp&' +
				      'callback=initializeMap';

				  document.body.appendChild(script);

				}


				// Event lister, calls our script to load Google Maps once the rest of the page is loaded
				if (window.addEventListener) {
				  window.addEventListener('load', loadMapScript, false);
				} else if (el.attachEvent)  {
				  window.attachEvent('onload', loadMapScript);
				}
	</script>

<!-- End Map Code -->






					<?php

				else:

					// If not output the error
					print "<p>There has been an error: $result->error</p>";

				endif;

			endif;
		?>


		<p><a href="../../">↩ Back to API examples</a></p>
	</body>
</html>
