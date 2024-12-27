<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SampleCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sample:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        \Log::info('Cron command executed at: ' . date('H:i:s'));
    }
}
