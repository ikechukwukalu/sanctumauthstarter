<?php

namespace Ikechukwukalu\Sanctumauthstarter\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

class SetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sas:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Running all commands';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->callSilently('sas:controllers');
        $this->callSilently('sas:routes');
        $this->callSilently('sas:tests');

        $this->components->info('Controllers, routes, requests, services and tests scaffolding generated successfully.');
    }
}
