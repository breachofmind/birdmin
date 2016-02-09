<?php

namespace Birdmin\Console\Commands;

use App\Modules\Site;
use Birdmin\Media;
use Illuminate\Console\Command;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birdmin:import';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Clears and imports all media';

    public $importPath;

    /**
     * Create a new command instance.
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->importPath = base_path('import');
        if (! is_dir($this->importPath)) {
            echo "Import path does not exist.\n";
            exit;
        }
        if (! method_exists(Site::class, "handleImport")) {
            echo "Site::handleImport is not declared. Aborting...\n";
            exit;
        }
    }

    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $media = Media::all();
        foreach ($media as $item) {
            $item->delete();
        }
        $status = Site::handleImport(glob($this->importPath.DS."*"));

        echo "Import complete.\n";
        echo $status;
    }
}
