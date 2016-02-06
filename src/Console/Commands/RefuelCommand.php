<?php

namespace Birdmin\Console\Commands;

use Illuminate\Console\Command;

class RefuelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birdmin:refuel';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Uninstalls and re-runs birdmin migrations and seeds';

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
        $this->call('migrate:reset');
        $this->call('birdmin:launch');
    }
}
