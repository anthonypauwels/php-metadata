<?php
namespace anthonypauwels\Metadata\Laravel;

use Illuminate\Support\ServiceProvider;
use anthonypauwels\Metadata\MetadataGenerator;

/**
 * ServiceProvider.
 * Register the Metadata helper class as a singleton into Laravel
 *
 * @package anthonypauwels\Metadata
 * @author Anthony Pauwels <hello@anthonypauwels.be>
 */
class MetadataServiceProvider extends ServiceProvider
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
