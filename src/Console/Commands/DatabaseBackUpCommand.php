<?php

namespace Ikechukwukalu\Sanctumauthstarter\Console\Commands;

use Illuminate\Console\Command;

class DatabaseBackUpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $command = config('sanctumauthstarter.console.remote_access') ?
        config('sanctumauthstarter.console.remote_backup_command') :
        config('sanctumauthstarter.console.local_backup_command');

        $returnVar = NULL;
        $output  = NULL;

        exec($command, $output, $returnVar);
    }
}
