<?php

use Birdmin\Input;
use Birdmin\Core\Model;
use Illuminate\Database\Seeder;
use Birdmin\Support\ModelBlueprint;

class InputSeeder extends Seeder
{
    /**
     * Input objects.
     * @var array
     */
    protected $created = [];
    /**
     * Install inputs for each model.
     * Encapsulate values if need be by the ` character.
     *
     * @return void
     */
    public function run()
    {
        $config = Model::$config;


        foreach ($config as $class=>$data)
        {
            // Use the blueprint first.
            if ($blueprint = ModelBlueprint::get($class)) {
                $arr = $blueprint->getInputsArray();

                foreach ($arr as $input) {
                    $this->createIfNotExisting($input);
                }
                continue;
            }


            // Otherwise, use deprecated stuff.
            foreach ($data['fields'] as $input) {
                $this->createIfNotExisting($input);
            }
        }


        echo sizeof($this->created)." Input objects created.\n";
    }


    /**
     * Create an input if it doesn't exist.
     * @param $input array
     */
    protected function createIfNotExisting($input)
    {
        if ( Input::where('name', $input['name'] )
            ->where('object', $input['object'])
            ->exists() ) {
            return;
        }

        $this->created[] = Input::create($input);
    }

}
