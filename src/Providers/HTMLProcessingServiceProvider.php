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
            $class = null;
            $name = $node->name;

            $possibilities = [
                $name,
                "App\\Components\\$name",
                "Birdmin\\Components\\$name"
            ];

            foreach ($possibilities as $className) {
                if (class_exists($className)) {
                    $class = $className;
                    break;
                }
            }
            if (! $class) {
                throw new \Exception("Component class '$name' does not exist");
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
