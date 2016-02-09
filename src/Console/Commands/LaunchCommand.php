<?php

namespace Birdmin\Console\Commands;

use Illuminate\Console\Command;

class LaunchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birdmin:launch';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Installs and seeds the core Birdmin tables';

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $this->call('migrate', [
            "--path" => 'cms/db/migrations'
        ]);
        $this->call('migrate', [
            "--path" => 'app/db/migrations'
        ]);
        $this->call('db:seed', [
            "--class" => 'Birdmin\Core\Seeder'
        ]);
    }
}
