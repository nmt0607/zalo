<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $users = User::where('name', 'like', '%'.$request->search.'%')->orderBy('name')->get();
        foreach ($users as $user) {
            $user->avatar = $user->avatar;
        }
        $messages = Message::where('message', 'like', '%'.$request->search.'%')
            ->where(function($query){
                $query->where('from_id', auth()->id());
                $query->orWhere('to_id', auth()->id());
            })->get();
        foreach ($messages as $message) {
            $message->sender = $message->sender;
            $message->sender->avatar = $message->sender->avatar;
        }
        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => [
                'user' => $users,
                'message' => $messages,
            ],
        ]);
    }
}
