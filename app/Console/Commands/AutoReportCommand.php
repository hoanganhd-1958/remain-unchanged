<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp;
use Illuminate\Support\Carbon;

/**
 * Class RemainUnchangedCommand
 * @package App\Console\Commands
 */
class AutoReportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "auto_report";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Auto report project Digmee";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $token = 'eyJhbGciOiJIUzI1NiJ9.eyJleHAiOjE2MTM2OTc0NTQsImlzcyI6InN1bi1hc3RlcmlzayIsImF1ZCI6ImlwZHctZmUiLCJ1c2VyX2lkIjoxMDJ9.GdRm9X-olomhJHIoYHOXTCgOlzHShbzO4Vw3826yZ_M';

        $uri = 'https://ipdw-admin.sun-asterisk.vn/api/v1/projects/16/daily_reports';

        $options = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            ],
            'body' => json_encode([
                'daily_report' => [
                    'create_new_message' => false,
                    'project_id' => '16',
                    'rating' => 'good',
                    'report_date' => Carbon::now()->format('d/m/Y'),
                    'reports_attributes' => [
                        [
                            'function_name' => 'Điều tra',
                            'kind' => 'task',
                            'url' => '',
                            'priority' => 'normal',
                            'estimated_time' => '8',
                            'actual_time' => '8',
                            'percent_completed' => '100',
                            'note' => '',
                            '_destroy' => false,
                        ],
                    ],
                ],
            ]),
        ];

        (new GuzzleHttp\Client())->request(
            'POST',
            $uri,
            $options
        );
    }
}
