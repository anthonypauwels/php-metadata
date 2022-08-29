<?php
namespace Anthonypauwels\Metadata\Laravel;

use Illuminate\Support\Facades\Facade;
use Anthonypauwels\Metadata\MetadataProtocol;
use Anthonypauwels\Metadata\MetadataGenerator;

/**
 * Facade.
 * Provide quick access methods to the MetadataGenerator class
 *
 * @method static MetadataGenerator init()
 * @method static MetadataGenerator getInstance()
 * @method static MetadataGenerator __callStatic($name, $arguments)
 * @method static MetadataGenerator setPrefixUrl(string $prefix_url)
 * @method static MetadataGenerator setTags(array $tags)
 * @method static MetadataGenerator title(string $title, int $flags = MetadataProtocol::ALL)
 * @method static MetadataGenerator description(string $description, int $flags = MetadataProtocol::ALL)
 * @method static MetadataGenerator image(string $url, array $options = [], int $flags = MetadataProtocol::TWITTER | MetadataProtocol::OPENGRAPH)
 * @method static MetadataGenerator url(?string $url = null, int $flags = MetadataProtocol::TWITTER | MetadataProtocol::OPENGRAPH)
 * @method static MetadataGenerator type(string $type = 'website', array $options = [])
 * @method static MetadataGenerator author(string $author)
 * @method static MetadataGenerator twitterCard(string $card_type = 'summary')
 * @method static MetadataGenerator twitterSite(string $twitter_site)
 * @method static MetadataGenerator twitterCreator(string $twitter_creator)
 * @method static MetadataGenerator fbAppId(string $app_id)
 * @method static MetadataGenerator fbAdmins(string $admins)
 * @method static MetadataGenerator siteName(string $site_name)
 * @method static MetadataGenerator disablePinterestRichPin(bool $value = true)
 * @method static MetadataGenerator robots(...$values)
 * @method static MetadataGenerator meta(string $name, $value)
 * @method static MetadataGenerator twitter(string $name, $value, string $prefix = MetadataGenerator::TWITTER_PREFIX)
 * @method static MetadataGenerator opengraph(string $name, $value, string $prefix = MetadataGenerator::OPENGRAPH_PREFIX)
 * @method static MetadataGenerator toHtml(int $flags = MetadataProtocol::ALL)
 * @method static MetadataGenerator print(int $flags = MetadataProtocol::ALL)
 * @method static MetadataGenerator __toString()
 *
 * @package Anthonypauwels\Metadata
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class Metadata extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'metadata';
    }
}