<?php

namespace Yab\Quarx\Console;

use Artisan;
use Illuminate\Support\Str;
use Illuminate\Support\Schema;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Illuminate\Filesystem\Filesystem;

class Theme extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'quarx:theme {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a base Quarx theme';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        $fileSystem = new Filesystem;

        $files = $fileSystem->allFiles(__DIR__.'/../PublishedAssets/Theme');
        $this->line("\n");
        foreach ($files as $file) {
            $this->line(str_replace(__DIR__.'/../PublishedAssets/Theme/', '', str_replace('template', strtolower($name), $file)));
        }

        $this->info("\n\nThese files will be generated\n");

        $result = $this->confirm("Are you sure you want to generate this theme?");

        if ($result) {
            foreach ($files as $file) {
                $newFileName = str_replace(__DIR__.'/../PublishedAssets/Theme', '', $file);
                $newFileName = str_replace('template', strtolower($name), $newFileName);
                $this->line("Copying ".$newFileName."...");
                if (is_dir($file)) {
                    $fileSystem->copyDirectory($file, base_path($newFileName));
                } else {
                    @mkdir(base_path(str_replace(basename($newFileName), '', $newFileName)), 0755, true);
                    $fileSystem->copy($file, base_path($newFileName));
                }
            }

            $sass = file_get_contents(base_path('resources/views/quarx/themes/'.strtolower($name).'/assets/sass/_theme.scss'));
            $repairs = str_replace('template', strtolower($name), $sass);
            file_put_contents(base_path('resources/views/quarx/themes/'.strtolower($name).'/assets/sass/_theme.scss'), $repairs);

            $this->info("Finished generating your theme");
            $this->line("\n");
            $this->info("Please add this to your gulpfile.js in the scripts elixir:");
            $this->comment('../../views/quarx/themes/'.strtolower($name).'/assets/js/theme.js');
            $this->line("\n");
            $this->info("Please add this to your app.scss:");
            $this->comment('@import "resources/views/quarx/themes/'.strtolower($name).'/assets/sass/_theme.scss"');
        } else {
            $this->info("Nothing has been changed or added");
        }
    }
}
