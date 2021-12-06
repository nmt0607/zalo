<?php

namespace App\Listeners;

use App\Events\MessageDeleting;
use App\Services\MessageService;

class DeleteMessage
{
    /** @var MessageService */
    protected $messageService;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->messageService = app(MessageService::class);
    }

    /**
     * Handle the event.
     *
     * @param  MessageDeleting  $event
     * @return void
     */
    public function handle(MessageDeleting $event)
    {
        $messageId = $event->getMessageId();
        $this->messageService->destroy($messageId);
    }
}
