<?php

namespace App\Console\Commands;

use App\Jobs\RemainUnchangedJob;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use wataridori\ChatworkSDK\ChatworkRoom;
use wataridori\ChatworkSDK\ChatworkSDK;
use GuzzleHttp;

/**
 * Class RemainUnchangedCommand
 * @package App\Console\Commands
 */
class RemainUnchangedCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = "remain_unchanged";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Remain unchanged all key results";


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $refresh_token = Storage::get('token.txt');
        $client = new GuzzleHttp\Client();

        $get_token = $client->request('POST', 'https://goal.sun-asterisk.vn/api/v1/refresh_token',
            [
                'headers' => [],
                'form_params' => [
                    'refresh_token' => $refresh_token,
                ],
            ]
        );
        $get_token = json_decode($get_token->getBody()->getContents());

        Storage::put('token.txt', $get_token->data->refresh_token);

        $token = $get_token->data->access_token;

        $NUM_OF_ATTEMPTS = 5;
        $attempts = 0;
        do {
            try {
                $res = $client->request('GET', 'https://script.google.com/macros/s/AKfycbzyjngLeB0DGcnEllhcygUWgOu6LuPwRAuSfvaw_DlUg3LRbZTp/exec');
                $data = json_decode($res->getBody()->getContents());
                $kr = $data->content->ids;

                foreach ($kr as $value) {
                    $value = (int) $value;
                    $response = $client->request('POST',
                        'https://goal.sun-asterisk.vn/api/v1/objectives/999999/remain_unchanged',
                        [
                            'headers' => [
                                'Authorization' => $token
                            ],
                            'form_params' => [
                                'keyResultId' => $value,
                            ]
                        ]
                    );

                    if ($response->getHeader('Content-Type')[0] != 'application/json') {
                        $this->sendMessageToChatwork('Token hết hạn, vui lòng vào link sau và sửa token https://docs.google.com/spreadsheets/d/1g3iaU_yyOE4z3Wp7KP1G-TQqmQq1Yy2MXGc5jxvej1c/edit?usp=sharing, và hệ thống sẽ chạy update lại vào ' . date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i:s")." +30 minutes")));
                        throw new \ErrorException('Error');
                    }
                }

                $this->sendMessageToChatwork('Remain unchange thành công');
                break;
            } catch (\Exception $ex) {
                $attempts++;
                if ($attempts == 5) {
                    $this->sendMessageToChatwork('Remain unchange tuần này thất bại');
                }
                sleep(1800);
                continue;
            }
        } while($attempts < $NUM_OF_ATTEMPTS);
    }

    /**
     * @param string $message
     * @return void
     */
    private function sendMessageToChatwork(string $message): void
    {
        $token = config('services.chatwork.token');
        $room = config('services.chatwork.room');

        if ($token && $room) {
            ChatworkSDK::setApiKey($token);
            $chatworkRoom = new ChatworkRoom($room);

            $chatworkRoom->sendMessageToAll($message, true);
        }
    }
}
