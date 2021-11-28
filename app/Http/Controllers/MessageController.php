<?php

namespace App\Http\Controllers;

use App\Exceptions\MessageNotExistedException;
use App\Http\Requests\DeleteMessageRequest;
use App\Models\Message;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function delete_message(DeleteMessageRequest $request)
    {
        try {
            $message = Message::findOrFail($request->message_id);
        } catch (ModelNotFoundException $e) {
            throw new MessageNotExistedException();
        }

        if ($message->from_id != auth()->id()) {
            throw new MessageNotExistedException();
        }
        
        $message->delete();
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
        ]);
    }
}
