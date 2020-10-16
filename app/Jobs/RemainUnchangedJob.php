<?php

namespace App\Jobs;

use Illuminate\Http\Request;
use wataridori\ChatworkSDK\ChatworkRoom;
use wataridori\ChatworkSDK\ChatworkSDK;
use GuzzleHttp;

class RemainUnchangedJob extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Execute the job.
     *
     * @param Request $request
     * @return void
     */
    public function handle(Request $request)
    {

    }
}
