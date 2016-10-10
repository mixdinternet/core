<?php

namespace Mixdinternet\Core\Commands;

use Illuminate\Console\Command;
use File;
use DB;
use Carbon\Carbon;

class SessionClear extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clear {days=30}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpa as sessions com mais de 30 dias';

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
        $days = intval($this->argument('days'));
        $time = strtotime(Carbon::now()->subDays($days)->format('Y-m-d H:i:s'));
        $this->comment('Limpando as sessions com mais de ' . $days);

        if (config('session.driver') == 'file') {
            $files = File::allFiles(config('session.files'));
            foreach ($files as $file)
            {
                $filename = (string)$file;
                if(filemtime($filename) <= $time) {
                    unlink($filename);
                }
            }
        }

        if (config('session.driver') == 'database') {
            DB::table('sessions')->where('last_activity', '<=', $time)->delete();
        }
    }
}
