<?php

namespace App\Console\Commands;

use App\Models\Library;
use Illuminate\Console\Command;

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

        $this->output->text('Created Movie Library');
    }
}
