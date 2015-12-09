# PHP Wrapper for the TourCMS API

Currently updating this file, additional documentation available at: http://www.tourcms.com/support/api/mp/code/library_php.php

## Installation

### Installing via Composer (Recommended)

1. Install [Composer](https://getcomposer.org/), add `"tourcms/tourcms-php": "2.0.*",` to the `requires` section of your `composer.json`:
2. Ensure you are including composer's `autoload.php` in your source, alternatively include `tourcms.php` directly.

### Installing Manually

1. Download the source zip, extract to your web server
2. Include `tourcms.php` in your source

## Usage

```php
// Optionally alias the namespace
use \TourCMS\Utils\TourCMS as TourCMS;

// Common configuration parameters

// Marketplace ID will be 0 for Tour Operators, non-zero for Marketplace Agents
// Agents can find their Marketplace ID in the API page in TourCMS settings
$marketplace_id = 0;

// API key will be a string, find it in the API page in TourCMS settings
$api_key = "YOUR_KEY_HERE";

// Channel ID represents the Tour Operator channel to call the API against
// Tour Operators may have multiple channels, so enter the correct one here
// Agents can make some calls (e.g. tour_search()) across multiple channels
// by entering a Channel ID of 0 or omitting it, or they can restrict to a
// specific channel by providing the Channel ID
$channel_id = 0;

// Create a new TourCMS instance
$tourcms = new TourCMS($marketplace_id, $api_key, 'simplexml');
// 'simplexml' returns as a SimpleXMLObject
// 'raw' returns the XML as as String

// Call the API
// Here as a quick example we search for some tours
$result = $tourcms->search_tours('', $channel_id);

print_r($result);
```

Copyright (c) 2011-2015 Travel UCD
