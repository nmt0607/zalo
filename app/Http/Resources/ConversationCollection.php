<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ConversationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $user = $request->user();
        $conversations = $this->collection->map(function ($conversation) use ($user) {
            $recipient = $conversation
                ->participants
                ->first(function ($participant) use ($user) {
                    return $participant->id !== $user->id;
                });

            $lastMessage = $conversation->lastMessage
                ? new MessageResource($conversation->lastMessage)
                : null;

            return [
                'id' => $conversation->id,
                'partner' => new UserResource($recipient),
                'lastmessage' => $lastMessage,
            ];
        });

        return [
            'data' => $conversations,
        ];
    }
}
