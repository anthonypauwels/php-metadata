<?php
namespace Anthonypauwels\Metadata;

/**
 * Class MetaProtocol
 *
 * @package Anthonypauwels\Metadata
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
final class MetadataProtocol
{
    /** @var int All meta tags */
    const ALL = 1;

    /** @var int Only common meta tags */
    const META = 2;
    /** @var int Only Twitter meta tags */

    const TWITTER = 4;
    /** @var int Only Opengraph meta tags */

    const OPENGRAPH = 8;

    private function __construct()
    {
        //
    }
}