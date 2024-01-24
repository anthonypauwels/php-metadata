<?php
use Anthonypauwels\Metadata\Metadata;

/**
 * Return the instance of the MetadataGenerator
 *
 * @return Metadata
 */
if ( !function_exists( 'metadata' ) ) {
    function metadata():Metadata
    {
        return Metadata::getInstance();
    }
}
