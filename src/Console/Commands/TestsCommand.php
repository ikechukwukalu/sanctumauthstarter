<?php

namespace Ikechukwukalu\Sanctumauthstarter\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class TestsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sas:tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the tests';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($directory = base_path('tests/Feature'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/Tests'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('tests/Feature/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Tests scaffolding generated successfully.');
    }
}
