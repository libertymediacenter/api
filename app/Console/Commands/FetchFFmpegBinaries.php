<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FetchFFmpegBinaries extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ffmpeg:download';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download ffmpeg & ffprobe binaries';

    private $ffmpegArchiveUrl = 'https://github.com/vot/ffbinaries-prebuilt/releases/download/v4.1/ffmpeg-4.1-linux-64.zip';
    private $ffProbeArchiveUrl = 'https://github.com/vot/ffbinaries-prebuilt/releases/download/v4.1/ffprobe-4.1-linux-64.zip';

    /** @var \Illuminate\Contracts\Filesystem\Filesystem */
    private $disk;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->disk = Storage::disk('local');
        $this->disk->makeDirectory('bin');
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

    }

    private function downloadArchive(string $url)
    {
        $guzzle = new Client();
        $guzzle->get($url);
    }
}
