<?php
include('src/tourcms.php');

use TourCMS\Utils\TourCMS as TourCMS;

$foo = new TourCMS(126, "5aed2d3d69ea", "simplexml");

$foo->test_environment();

 ?>
