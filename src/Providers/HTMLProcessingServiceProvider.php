<?php

namespace Birdmin\Providers;

use Birdmin\Support\HTMLProcessor;
use Illuminate\Support\ServiceProvider;
use Birdmin\Contracts\HTMLComponent;

class HTMLProcessingServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        /**
         * <component name="App\HTMLComponentClass">Content</component>
         */
        HTMLProcessor::register('component', function($node, $processor)
        {
            $class = $node->name;

            if (! class_exists($class)) {
                throw new \Exception("Component class '$class' does not exist");
            }
            if (! has_contract($class, HTMLComponent::class)) {
                throw new \Exception("Component class '$class' does not implement the HTMLComponent contract");
            }

            $component = $class::create($processor->getModel(),$node);

            // Replace the contents of the node with the component view.
            $node->innertext = $component->render();
        });
    }


    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        // Void
    }

}
