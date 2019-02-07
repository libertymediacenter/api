<?php

namespace App\Console\Commands;

use App\Models\Library;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class SetupDevCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:dev';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resets & migrates database - then runs the importer';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->cleanAssets();

        $this->call('migrate:fresh');
        $this->createLibraries();

        return 0;
    }

    private function createLibraries()
    {
        $movieLibrary = Library::create([
            'name'          => 'Movies',
            'type'          => Library::MOVIE,
            'metadata_lang' => 'en',
            'path'          => 'movies',
        ]);

        $tvLibrary = Library::create([
            'name'          => 'TV',
            'type'          => Library::TV,
            'metadata_lang' => 'en',
            'path'          => 'tv',
        ]);

        $this->output->note('Created libraries');

        $this->output->table(['Name', 'type', 'Lang', 'path'], [
            [$movieLibrary->name, $movieLibrary->type, $movieLibrary->metadata_lang, $movieLibrary->path],
            [$tvLibrary->name, $tvLibrary->type, $tvLibrary->metadata_lang, $tvLibrary->path],
        ]);
    }

    private function cleanAssets()
    {
        $this->output->text('Cleaning assets');

        $localDisk = Storage::disk('local');
        $directories = collect($localDisk->listContents('public/assets/images'));

        $directories->each(function (array $item) use (&$localDisk) {
            if ($item['type'] !== 'dir') {
                return;
            }

            $files = collect($localDisk->listContents($item['path']));
            $files = $files->map(function (array $item) {
                return $item['path'];
            });

            $localDisk->delete($files);
        });

    }
}
