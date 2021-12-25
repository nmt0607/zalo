<?php

namespace App\Http\Controllers;

use App\Exceptions\SearchIdNotFoundException;
use App\Http\Requests\DelSavedSearchRequest;
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

        $users = User::where('name', 'like', '%' . $request->keyword . '%')->orderBy('name')->get();
        foreach ($users as $user) {
            $user->avatar = $user->avatar;
        }
        $messages = Message::where('message', 'like', '%' . $request->keyword . '%')
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
                'total_user' => $users->count(),
                'total_mess' => $messages->count(),
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

    public function del_saved_search(DelSavedSearchRequest $request)
    {
        if ($request->all == 1) {
            Search::destroy(auth()->user()->searches->pluck('id'));
            return response()->json([
                'code' => config('response_code.ok'),
                'message' => __('messages.ok')
            ]);
        }

        $search = Search::find($request->search_id);

        if (!$search) {
            throw new SearchIdNotFoundException();
        }
        
        if ($search->user_id != auth()->id()) {
            throw new SearchIdNotFoundException();
        }

        $search->delete();

        return response()->json([
            'code' => config('response_code.ok'),
            'message' => __('messages.ok')
        ]);
    }
}
