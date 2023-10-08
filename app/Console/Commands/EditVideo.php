<?php

namespace App\Console\Commands;

use App\Jobs\EditVideoJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use function Laravel\Prompts\{select, suggest};

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
        $singleVideo = "Single Video";
        $folderOfVideos = "Folder of Videos";

        $type = select(
            label: 'Would you like to edit a single video or folder containing multiple videos?',
            options: [$singleVideo, $folderOfVideos],
        );

        $label = "Enter the path to the single video";

        $videos = collect(Storage::disk('common')->files())->filter(function ($video) {
            return str_ends_with($video, '.mkv') && !str_contains($video, 'ALTERED');
        })->toArray();

        $input = suggest(
            label: $label,
            required: true,
            options: $videos,
        );

        EditVideoJob::dispatch(config('auto-editor.video_path') . $input);
    }
}
