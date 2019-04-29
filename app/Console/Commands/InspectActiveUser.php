<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InspectActiveUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspect:active';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inspect Active Twitter User. And add to UnfollowTargetList';

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
        //
    }
}
