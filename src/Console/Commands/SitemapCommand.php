<?php

namespace Birdmin\Console\Commands;

use Illuminate\Console\Command;
use Birdmin\Support\SitemapGenerator;

class SitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:sitemap';

    /**
     * The console command description.
     * @var string
     */
    protected $description = 'Creates an XML sitemap and robots.txt';


    /**
     * Execute the console command.
     * @return mixed
     */
    public function handle()
    {
        $sitemap = new SitemapGenerator();

        $sitemap->crawl(true);
        $sitemap->save();
    }
}
