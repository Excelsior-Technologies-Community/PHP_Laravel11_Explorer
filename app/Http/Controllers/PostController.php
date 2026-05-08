<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;

class PostController extends Controller
{

 public function index(Request $request)
{
    $search = $request->search;

    $posts = Post::query()

        ->when($search, function ($query) use ($search) {

            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");

        })

        ->orderBy('id', 'asc')

        ->paginate(3)

        ->withQueryString();

    return view('posts.index', compact('posts'));
}

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|min:3',
            'content' => 'required|min:10'
        ]);

        Post::create([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect('/')
            ->with('success','Post Created Successfully');
    }

    public function search(Request $request)
    {
        $search = $request->search;

        if($search)
        {
            $posts = Post::search($search)->paginate(5);
        }
        else
        {
            $posts = Post::latest()->paginate(5);
        }

        return view('posts.index', compact('posts'));
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title' => 'required|min:3',
            'content' => 'required|min:10'
        ]);

        $post->update([
            'title' => $request->title,
            'content' => $request->content
        ]);

        return redirect('/')
            ->with('success','Post Updated Successfully');
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect('/')
            ->with('success','Post Deleted Successfully');
    }

}