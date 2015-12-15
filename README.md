# PHP Wrapper for the TourCMS API

* [Installation](#installation)
  * [Installing via Composer](#installing-via-composer-recommended)
  * [Installing Manually](#installing-manually)
  * [Upgrading from v1.x](#upgrading-from-version-1x)
* [Usage](#usage)
* [Further examples](#further-examples)

## Installation

### Installing via Composer (Recommended)

1. Install [Composer](https://getcomposer.org/), add `"tourcms/tourcms-php": "2.0.*",` to the `requires` section of your `composer.json`:
2. Ensure you are including composer's `autoload.php`, alternatively include `tourcms.php` directly.

### Installing Manually

1. Download the source zip, extract to your web server
2. Include `tourcms.php` in your source

### Upgrading from version 1.x

If you are upgrading from version 1.x of the library the latest `tourcms.php` should be more or less a straight swap. The major change being that to adhere to PHP [PSR-4](http://www.php-fig.org/psr/psr-4/) standards, the class is now namespaced. Broadly speaking there are two different ways to update existing code to account for this:

#### Aliasing the namespace

If you already have a global include file that includes `tourcms.php` you could add the following line immediately after `tourcms.php` is included:

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

  // Channel ID represents the Tour Operator channel to call the API against
  // Tour Operators may have multiple channels, so enter the correct one here
  // Agents can make some calls (e.g. tour_search()) across multiple channels
  // by entering a Channel ID of 0 or omitting it, or they can restrict to a
  // specific channel by providing the Channel ID
    $channel_id = 0;


// Create a new TourCMS instance
  // Optionally alias the namespace
  use TourCMS\Utils\TourCMS as TourCMS;
  $tourcms = new TourCMS($marketplace_id, $api_key, 'simplexml');
  // 'simplexml' returns as a SimpleXMLObject
  // 'raw' returns the XML as as String

// Call the API
  // Here as a quick example we search for some tours
  $result = $tourcms->search_tours('', $channel_id);

// Display the output
print_r($result);
```

## Further Examples

### API documentation on tourcms.com

Each API method in the [TourCMS API documentation](http://www.tourcms.com/support/api/mp/) includes full PHP sample code.

### Examples in this repository

Additionally there are some examples included in this repository, to run them:

1. Copy the `src/examples` directory to your web root
2. Rename `examples/config-example.php` to `examples/config.php`
3. Load your API credentials in the config file and ensure the path to `tourcms.php` is correct
4. Point your web browser at the examples folder
