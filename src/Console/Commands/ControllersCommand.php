<?php

namespace Ikechukwukalu\Sanctumauthstarter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class ControllersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sas:controllers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the authentication controllers';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($directory = app_path('Http/Controllers/Auth'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = app_path('Http/Requests/Auth'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = app_path('Services/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/Controllers'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        collect($filesystem->allFiles(__DIR__.'/stubs/Requests'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Requests/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        collect($filesystem->allFiles(__DIR__.'/stubs/Services'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Services/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Controllers, requests and services scaffolding generated successfully.');
    }
}
