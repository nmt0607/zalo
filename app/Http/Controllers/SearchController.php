<?php

namespace App\Http\Controllers;

use App\Http\Requests\GetSavedSearchRequest;
use App\Models\User;
use App\Models\Message;
use App\Models\Search;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        Search::create([
            'user_id' => auth()->id(),
            'keyword' => $request->keyword,
        ]);

        $users = User::where('name', 'like', '%' . $request->search . '%')->orderBy('name')->get();
        foreach ($users as $user) {
            $user->avatar = $user->avatar;
        }
        $messages = Message::where('message', 'like', '%' . $request->search . '%')
            ->where(function ($query) {
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

    public function get_saved_search(GetSavedSearchRequest $request)
    {
        $data = [];
        foreach (auth()->user()->searches->skip($request->index)->take($request->count) as $x) {
            array_push($data, [
                'id' => $x['id'],
                'keyword' => $x['keyword'],
                'created' => $x['created_at']
            ]);
        }

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok'),
            'data' => $data
        ]);
    }
}
