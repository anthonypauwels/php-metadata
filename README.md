# PHP Metadata

An helper package to build metadata tags on PHP pages.

## Installation

Require this package with composer.
```shell
composer require anthonypauwels/metadata
```

### Laravel without auto-discovery:

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`:
```php
Anthonypauwels\Metadata\Laravel\ServiceProvider::class,
```

Then add this line to your facades in `config/app.php`:
```php
'Metadata' => Anthonypauwels\Metadata\Laravel\Metadata::class,
```

## Usage

### Helper function

Helper function returns an instance of `Metadata`.
```php
$metadata = metadata();
```

```php
metadata()->title( 'Website title' );
metadata()->description( 'Website description' );
metadata()->url( url()->full() );
metadata()->author( 'Anthony Pauwels' );
metadata()->opengraphImage( asset('/img/facebook_meta.jpg'), ['width' => 1200, 'height' => 600, 'type' => 'JPEG'] );
metadata()->twitterImage( asset('/img/twitter_meta.jpg'), ['width' => 600, 'height' => 400, 'type' => 'JPEG'] );
metadata()->twitterCard( 'app' );
metadata()->type( 'website' );
```

### With Laravel

The package provides by default a Facade for Laravel application. You can call methods directly using the Facade or use the alias instead.
```php
use Anthonypauwels\Metadata\Laravel\Metadata;

Metadata::title( 'Website title' );
```

### API documentation

#### Metadata
```php
/**
 * Return the Metadata instance
 */
function getInstance():Metadata; 

/**
 * Set an array of tags
 */
function setTags(array $tags): Metadata;

/**
 * Set the page title in meta tags
 */
function title(string $title, int $flags = Metadata::ALL): Metadata;

/**
 * Set the page description in meta tags
 */
function description(string $description, int $flags = Metadata::ALL): Metadata;

/**
 * Set the image for the page used for cards inside app; Can be used to set image options like size or mimetype
 */
function image(string $url, array $options = [], int $flags = Metadata::TWITTER | Metadata::OPENGRAPH): Metadata;

/**
* Shortcut to image() method for Twitter Card
 */
function twitterImage(string $url, array $options = []): Metadata

/**
* Shortcut to image() method for Opengraph
 */
function opengraphImage(string $url, array $options = []): Metadata

/**
 * Set the page URL
 */
function url(string $url, int $flags = Metadata::TWITTER | Metadata::OPENGRAPH): Metadata;

/**
 * Set the content og type
 */
function type(string $type = 'website', array $options = []): Metadata;

/**
 * Set the author's name
 */
function author(string $author): Metadata;

/**
 * Set the Twitter card format
 */
function twitterCard(string $card_type = 'summary'): Metadata;

/**
 * Set the Twitter website profile
 */
function twitterSite(string $twitter_site): Metadata;

/**
 * Set the twitter author profile
 */
function twitterCreator(string $twitter_creator): Metadata;

/**
 * Set the facebook app_id
 */
function fbAppId(string $app_id): Metadata;

/**
 * Set the facebook admins tag
 */
function fbAdmins(string $admins): Metadata;

/**
 * Set the og:site_name tag
 */
function siteName(string $site_name): Metadata;

/**
 * Disable (or enable if $value is true) the pinterest-rich-pin
 */
function disablePinterestRichPin(bool $value = true): Metadata;

/**
 * Set the robot meta tag
 */
function robots(...$values): Metadata;

/**
 * Set a meta tag with given key and given value
 */
function meta(string $name, string $value): Metadata;

/**
 * Set a meta tag for Twitter
 */
function twitter(string $name, string $value, string $prefix = Metadata::TWITTER_PREFIX): Metadata;

/**
 * Set a meta tag for Opengraph
 */
function opengraph(string $name, string $value, string $prefix = Metadata::OPENGRAPH_PREFIX): Metadata;

/**
 * Generate the HTML code of meta tags
 */
function toHtml(int $flags = Metadata::ALL):string;

/**
 * Print the generated HTML code
 */
function print(int $flags = Metadata::ALL):void;

/**
 * Return the HTML code of meta tags with default parameter
 */
function __toString():string;
```

### Requirement

PHP 8.0 or above