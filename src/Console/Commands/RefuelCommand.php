<?php

namespace Birdmin\Console\Commands;

use Illuminate\Console\Command;
use Birdmin\Media;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        // Delete media from the database and from the uploads directory.
        if (Schema::hasTable('media')) {
            $media = Media::all();
            $media->each(function($item) {
                $item->delete();
            });
        }

        $this->call('migrate:reset');
        $this->call('birdmin:launch');
    }
}
