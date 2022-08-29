<?php
namespace Anthonypauwels\Metadata\Laravel;

use Anthonypauwels\Metadata\MetadataGenerator;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * ServiceProvider.
 * Register the Metadata helper class as a singleton into Laravel
 *
 * @package Anthonypauwels\Metadata
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Register the MetadataGenerator
     */
    public function register()
    {
        $this->app->singleton('metadata', function () {
            return new MetadataGenerator();
        } );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides():array
    {
        return [ MetadataGenerator::class ];
    }
}
