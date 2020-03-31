# PHP Wrapper for the TourCMS API

* [Installation](#installation)
  * [Installing via Composer](#installing-via-composer-recommended)
  * [Installing Manually](#installing-manually)
  * [Upgrading from v1.x](#upgrading-from-version-1x)
* [Usage](#usage)
* [Caching (PSR-16)](#caching-psr-16)
* [Further examples](#further-examples)
* [Environment test](#environment-test)

## Installation

### Installing via Composer (Recommended)

1. Install [Composer](https://getcomposer.org/), add `"tourcms/tourcms-php": "3.5.*",` to the `requires` section of your `composer.json`:
2. Ensure you are including composer's `autoload.php`, alternatively include `TourCMS.php` directly.

### Installing Manually

1. Download the source zip, extract to your web server
2. Include `TourCMS.php` in your source

### Upgrading from version 1.x

If you are upgrading from version 1.x of the library the latest `TourCMS.php` should be more or less a straight swap. The major change being that to adhere to PHP [PSR-4](http://www.php-fig.org/psr/psr-4/) standards, the class is now namespaced. Broadly speaking there are two different ways to update existing code to account for this:

#### Aliasing the namespace

If you already have a global include file that includes `TourCMS.php` you could add the following line immediately after `TourCMS.php` is included:

```php
use TourCMS\Utils\TourCMS as TourCMS;
```

Your existing code should then work as-is, for example when you create a new instance of the TourCMS class you would have:

```php
$tourcms = new TourCMS(0, 'YOUR_PASSWORD', 'simplexml');
```

#### Using the fully qualified name

Alternatively use the fully qualified name when you create a new instance of the class:

```php
$tourcms = new TourCMS\Utils\TourCMS(0, 'YOUR_PASSWORD', 'simplexml');
```

## Usage

```php
// Common configuration parameters

  // Marketplace ID will be 0 for Tour Operators, non-zero for Marketplace Agents
  // Agents can find their Marketplace ID in the API page in TourCMS settings
    $marketplace_id = 0;

  // API key will be a string, find it in the API page in TourCMS settings
    $api_key = "YOUR_KEY_HERE";

  // Timeout will set the maximum execution time, in seconds. If set to zero, no time limit is imposed.
    $timeout = 0;

  // Channel ID represents the Tour Operator channel to call the API against
  // Tour Operators may have multiple channels, so enter the correct one here
  // Agents can make some calls (e.g. tour_search()) across multiple channels
  // by entering a Channel ID of 0 or omitting it, or they can restrict to a
  // specific channel by providing the Channel ID
    $channel_id = 0;


// Create a new TourCMS instance
  // Optionally alias the namespace
  use TourCMS\Utils\TourCMS as TourCMS;
  $tourcms = new TourCMS($marketplace_id, $api_key, 'simplexml', $timeout);
  // 'simplexml' returns as a SimpleXMLObject
  // 'raw' returns the XML as as String

// Call the API
  // Here as a quick example we search for some tours
  $result = $tourcms->search_tours('', $channel_id);

// Display the output
  print_r($result);
```

## Caching (PSR-16)
The TourCMS object can receive a PSR-16 SimpleCache object in its constructor as the fourth argument that is treated as the cache driver when set.
### PSR-16
[PSR-16](https://www.php-fig.org/psr/psr-16/) is a commonly used Interface for cache drivers. Most established cache libraries implement the interface or offer adapters.

- [PhpFastCache](https://www.phpfastcache.com)
- [Scrapbook](https://www.scrapbook.cash)

### Caching Example
In order to enable API caching add a cache driver that implements the interface `Psr\SimpleCache\CacheInterface` to the
constructor of the TourCMS object:

Example using Scrapbook's Simple-Cache adapter
```php
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use MatthiasMullie\Scrapbook\Adapters\Flysystem;
use MatthiasMullie\Scrapbook\Psr16\SimpleCache;
use TourCMS\Utils\TourCMS;



$tmpdir = dirname(__DIR__) . "/.tmp";
$adapter = new Local($tmpdir);
$filesystem = new Filesystem($adapter);
$flysystem = new Flysystem($filesystem);
$cacheDriver = new SimpleCache($flysystem);

$tourcms = new TourCMS(1, "YOUR_KEY", "simplexml", $cacheDriver);

$params = "lat=56.82127&long=-6.09139&k=walking";
$channel = 3;
$tours = $tourcms->search_tours($params, $channel);
```

Then in another request or just later

```php
//the value of $tours came from the cache and not from the live TourCMS server
$tours = $tourcms->search_tours();
```

### Cache Timeouts
TourCMS uses a configuration array to determine how long the response for a specific request should be cached.
The default configuration is stored in a static property `TourCMS::$default_cache_timeouts`.

This array contains the API methods with their timeout values that are recommended by TourCMS.

If you wish to make changes to these default values simply take the above mentioned default array,
adjust the values and provide the newly formed configuration array as a fifth argument to the constructor
of `TourCMS`

```php

use TourCMS\Utils\TourCMS;

// setup cache driver as seen above
...
$cacheDriver = new SimpleCache($flysystem);

$config = TourCMS::$default_cache_timeouts;
unset($config['show_supplier']);
$config["show_tour"] = ["time" => 60 * 30];

$tourcms = new TourCMS(1, "YOUR_KEY", "simplexml", $cacheDriver, $config);

```  


## Further Examples

### API documentation on tourcms.com

Each API method in the [TourCMS API documentation](http://www.tourcms.com/support/api/mp/) includes full PHP sample code.

### Examples in this repository

Additionally there are some examples included in this repository, to run them:

1. Copy the `src/examples` directory to your web root
2. Rename `examples/config-example.php` to `examples/config.php`
3. Load your API credentials in the config file and ensure the path to `TourCMS.php` is correct
4. Point your web browser at the examples folder

# Environment test

The library can attempt to check your local environment and API credentials, useful if you are having trouble connecting. First off make sure you are including `TourCMS.php`, either via autoload or explicitly.

```php
// Common configuration parameters

  // Marketplace ID will be 0 for Tour Operators, non-zero for Marketplace Agents
  // Agents can find their Marketplace ID in the API page in TourCMS settings
    $marketplace_id = 0;

  // API key will be a string, find it in the API page in TourCMS settings
    $api_key = "YOUR_KEY_HERE";

  // Channel ID represents the Tour Operator channel to call the API against
  // Tour Operators may have multiple channels, so enter the correct one here
  // Agents can leave this as 0 for the test
    $channel_id = 0;

// Create a new TourCMS instance
  // Optionally alias the namespace
  use TourCMS\Utils\TourCMS as TourCMS;
  $tourcms = new TourCMS($marketplace_id, $api_key, "simplexml");

// Call the environment test, the results will be displayed on the screen
$tourcms->test_environment($channel_id);
```

# Response headers

TourCMS responds with some useful information in the response headers. There is a method to retrieve the last set of response headers via the PHP wrapper.

```php
$headers = $tourcms->get_last_response_headers();
$remaining_limit = $headers["x-ratelimit-remaining"];
```

# Override base URL

By default the base URL will point to TourCMS main production environment. There is a method to override this to point at another base URL, intended for testing purposes.

```php
$tourcms->set_base_url("https://api.example.com");
```
