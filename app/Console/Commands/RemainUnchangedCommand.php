<?php

namespace App\Console\Commands;

use App\Jobs\RemainUnchangedJob;
use Illuminate\Console\Command;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        $client = new GuzzleHttp\Client();
        $NUM_OF_ATTEMPTS = 5;
        $attempts = 0;
        do {
            try {
                $res = $client->request('GET', 'https://script.google.com/macros/s/AKfycbzyjngLeB0DGcnEllhcygUWgOu6LuPwRAuSfvaw_DlUg3LRbZTp/exec');
                $data = json_decode($res->getBody()->getContents());
                $token = $data->content->token[0];
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
                        $this->sendMessageToChatwork('Có lỗi xảy ra');
                        throw new \ErrorException('Error');
                    }
                }
            } catch (\Exception $ex) {
                $attempts++;
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
