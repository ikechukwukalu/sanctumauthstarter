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
    protected $signature = 'sas:setup {--s|sample}';

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
        if ($this->option('sample')) {
            $this->callSilent('sas:controllers', ['sample']);
            $this->callSilent('sas:routes', ['sample']);
        } else {
            $this->callSilent('sas:controllers');
            $this->callSilent('sas:routes');
        }

        $this->components->info('Controllers, routes, requests and services scaffolding generated successfully.');
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['sample', 's', InputOption::VALUE_NONE, 'Scaffold samples for controllers and routes'],
        ];
    }
}
