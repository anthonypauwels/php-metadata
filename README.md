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

Helper function returns an instance of `MetadataGenerator`.
```php
$metadataGenerator = metadata();
```

```php
metadata()->title( 'Website title' );
metadata()->description( 'Website description' );
metadata()->setPrefixUrl( 'https://website.com/' );
metadata()->url( '/' );
metadata()->author( 'Anthony Pauwels' );
metadata()->image( '/img/facebook_meta.jpg', ['width' => 1200, 'height' => 600, 'type' => 'JPEG'], MetaProtocol::OPENGRAPH );
metadata()->image( '/img/twitter_meta.jpg', ['width' => 600, 'height' => 400, 'type' => 'JPEG'], MetaProtocol::TWITTER );
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

#### MetadataGenerator
```php
/**
 * Return the MetadataGenerator instance
 */
function getInstance():MetadataGenerator; 

/**
 * Set the prefix URL used by image and url tags
 */
function setPrefixUrl(string $prefix_url): MetadataGenerator;

/**
 * Set an array of tags
 */
function setTags(array $tags): MetadataGenerator;

/**
 * Set the page title in meta tags
 */
function title(string $title, int $flags = MetadataProtocol::ALL): MetadataGenerator;

/**
 * Set the page description in meta tags
 */
function description(string $description, int $flags = MetadataProtocol::ALL): MetadataGenerator;

/**
 * Set the image for the page used for cards inside app; Can be used to set image options like size or mimetype
 */
function image(string $url, array $options = [], int $flags = MetadataProtocol::TWITTER | MetadataProtocol::OPENGRAPH): MetadataGenerator;

/**
 * Set the page URL; by default, it's the current URL
 */
function url(?string $url = null, int $flags = MetadataProtocol::TWITTER | MetadataProtocol::OPENGRAPH): MetadataGenerator;

/**
 * Set the content og type
 */
function type(string $type = 'website', array $options = []): MetadataGenerator;

/**
 * Set the author's name
 */
function author(string $author): MetadataGenerator;

/**
 * Set the Twitter card format
 */
function twitterCard(string $card_type = 'summary'): MetadataGenerator;

/**
 * Set the Twitter website profile
 */
function twitterSite(string $twitter_site): MetadataGenerator;

/**
 * Set the twitter author profile
 */
function twitterCreator(string $twitter_creator): MetadataGenerator;

/**
 * Set the facebook app_id
 */
function fbAppId(string $app_id): MetadataGenerator;

/**
 * Set the facebook admins tag
 */
function fbAdmins(string $admins): MetadataGenerator;

/**
 * Set the og:site_name tag
 */
function siteName(string $site_name): MetadataGenerator;

/**
 * Disable (or enable if $value is true) the pinterest-rich-pin
 */
function disablePinterestRichPin(bool $value = true): MetadataGenerator;

/**
 * Set the robot meta tag
 */
function robots(...$values): MetadataGenerator;

/**
 * Set a meta tag with given key and given value
 */
function meta(string $name, string $value): MetadataGenerator;

/**
 * Set a meta tag for Twitter
 */
function twitter(string $name, string $value, string $prefix = self::TWITTER_PREFIX): MetadataGenerator;

/**
 * Set a meta tag for Opengraph
 */
function opengraph(string $name, string $value, string $prefix = self::OPENGRAPH_PREFIX): MetadataGenerator;

/**
 * Generate the HTML code of meta tags
 */
function toHtml(int $flags = MetadataProtocol::ALL):string;

/**
 * Print the generated HTML code
 */
function print(int $flags = MetadataProtocol::ALL):void;

/**
 * Return the HTML code of meta tags with default parameter
 */
function __toString():string;
```

### Requirement

PHP 8.0 or above