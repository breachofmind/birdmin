<?php

use Birdmin\Input;
use Birdmin\Core\Model;
use Illuminate\Database\Seeder;

class InputSeeder extends Seeder
{
    /**
     * Install inputs for each model.
     * Encapsulate values if need be by the ` character.
     *
     * @return void
     */
    public function run()
    {
        $created = [];

        $config = Model::$config;
        foreach ($config as $class=>$data) {
            foreach ($data['fields'] as $input) {
                if ( Input::where('name', $input['name'])
                    ->where('object', $input['object'])
                    ->exists() ) {
                    continue;
                }
                $created[] = Input::create($input);
            }
        }


        echo sizeof($created)." Input objects created.\n";
    }


}
