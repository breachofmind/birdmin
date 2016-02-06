<?php
namespace Birdmin\Core;
use Illuminate\Database\Seeder as BaseSeeder;

class Seeder extends BaseSeeder
{
    protected $seed_classes = [];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seed_classes = config('database.seeds');

        Model::unguard();

        // Instantiate the seeds.
        foreach ($this->seed_classes as $class) {
            if (!class_exists($class)) {
                continue;
            }
            $this->call($class);
        }

        Model::reguard();
    }
}
