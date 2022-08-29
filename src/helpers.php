<?php
use Anthonypauwels\Metadata\MetadataGenerator;

/**
 * Return the instance of the MetadataGenerator
 *
 * @return MetadataGenerator
 */
if ( !function_exists( 'metadata' ) ) {
    function metadata():MetadataGenerator
    {
        return MetadataGenerator::getInstance();
    }
}