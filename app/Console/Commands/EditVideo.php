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

        $label = $type == $singleVideo ? "Enter the path to the single video" : "Enter the path to the folder containing multiple videos";

        $videos = $type == $singleVideo ? collect(Storage::disk('common')->files())->filter(function ($video) {
            return str_ends_with($video, '.mkv') && !str_contains($video, 'ALTERED');
        })->toArray() : [];

        $directories = $type == $folderOfVideos ? collect(Storage::disk('common')->directories())->toArray() : [];

        $input = suggest(
            label: $label,
            required: true,
            options: $type == $singleVideo ? $videos : $directories,
        );

        if ($type == $singleVideo) {
            EditVideoJob::dispatch(config('auto-editor.video_path') . $input);

            return;
        }

        $fullPath = config('auto-editor.video_path') . $input;

        $videos = scandir($fullPath);

        foreach ($videos as $video) {
            if (!str_ends_with($video, '.mkv') || str_contains($video, 'ALTERED')) {
                continue;
            }

            $this->info("Added $video to the queue for Processing");

            EditVideoJob::dispatch($fullPath . '/' . $video);
        }

        return;
    }
}
