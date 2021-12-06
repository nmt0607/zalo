<?php

namespace App\Console\Commands;

use App\Events\AuthenticatingWithToken;
use App\Events\ConversationJoining;
use App\Events\MessageSending;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class RedisSubscribe extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'redis:subscribe';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Subscribe to Redis channels';

    private $events;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->events = [
            config('define.events.authenticating') => new AuthenticatingWithToken(),
            config('define.events.conversation_joining') => new ConversationJoining(),
            config('define.events.message_sending') => new MessageSending(),
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Redis::connection('subscription')
            ->subscribe(['authentication', 'chat'], function ($message) {
                [
                    'event' => $eventName,
                    'data' =>  $data,
                ] = json_decode($message, true);

                if (array_key_exists($eventName, $this->events)) {
                    event($this->events[$eventName]->setData($data));
                }
            });
    }
}
