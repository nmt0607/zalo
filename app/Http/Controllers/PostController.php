<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        return response()->json([
            'data' => Post::all(),
        ]);
    }

    public function show($id)
    {
        return Post::find($id);
    }

    public function store(Request $request)
    {
        $request->validate([
            'content' => 'required',
        ]);

        return Post::create($request->all());
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required',
        ]);
        return Post::find($id)->update($request->all());
    }

    public function destroy($id){
        return Post::destroy($id);

    }
}
