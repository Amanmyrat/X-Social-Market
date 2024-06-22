<?php

namespace App\Console\Commands;

use DB;
use Illuminate\Console\Command;

class RefreshMaterializedView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'materialized-view:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh the materialized view post_scores';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        DB::statement('REFRESH MATERIALIZED VIEW post_scores');
        $this->info('Materialized view refreshed successfully.');
    }
}
