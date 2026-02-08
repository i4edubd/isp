<?php

namespace App\Console\Commands;

use App\Models\blade_template;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Illuminate\Support\Str;

class FindUnusedViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:unused_views {--search_in_view=1} {--search_in_controllers=1} {--search_in_routes=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'find unused views';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $search_in_view = (bool)$this->option('search_in_view');
        $search_in_controllers = (bool)$this->option('search_in_controllers');
        $search_in_routes = (bool)$this->option('search_in_routes');

        blade_template::truncate();

        // log views
        $dir = resource_path('views');
        $process = new Process(['find', $dir]);
        try {
            $process->mustRun();
            $files = $process->getOutput();
            Storage::put('views_found/views.txt', $files);
            $storage_file = storage_path('app/views_found/views.txt');
            if (file_exists($storage_file)) {
                $lines = file($storage_file);
                while ($line = array_shift($lines)) {
                    if (Str::contains($line, 'php')) {
                        $view_directory = Str::beforeLast($line, '/');
                        $view_file = Str::afterLast($line, '/');
                        $template_name = Str::before($view_file, '.blade.php');
                        $blade_template = new blade_template();
                        $blade_template->directory = $view_directory;
                        $blade_template->template_name = $template_name;
                        $blade_template->save();
                    }
                }
            } else {
                $this->info('File : ' . $storage_file . ' not found');
            }
        } catch (ProcessFailedException $exception) {
            echo $exception->getMessage();
        }

        // log usage
        $view_path = resource_path('views');
        $controllers_path = app_path('Http/Controllers');
        $mail_path = app_path('Mail');
        $routes_path = base_path('routes');

        $templates = blade_template::all();
        foreach ($templates as $template) {
            $search_paths = [$mail_path];
            if ($search_in_view) $search_paths[] = $view_path;
            if ($search_in_controllers) $search_paths[] = $controllers_path;
            if ($search_in_routes) $search_paths[] = $routes_path;
            foreach ($search_paths as  $search_path) {
                $command = "grep -nHr $search_path -e '$template->template_name'";
                $process = Process::fromShellCommandline($command);
                try {
                    $process->mustRun();
                    // echo $process->getOutput();
                    $template->used = 1;
                    $template->save();
                } catch (ProcessFailedException $exception) {
                    // $this->info($command);
                }
            }
        }

        // unused files
        $unused_files = blade_template::where('used', 0)->get();
        $this->info('unused files:');
        foreach ($unused_files as $unused_file) {
            $this->info($unused_file->directory . '/' . $unused_file->template_name . '.blade.php');
        }

        return Command::SUCCESS;
    }
}
