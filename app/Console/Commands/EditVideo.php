<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class EditVideo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'video:edit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Edit the Videos with auto-editor, handles single video and multiple videos by passing a folder path';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        echo "Editing Video\n";
    }
}
