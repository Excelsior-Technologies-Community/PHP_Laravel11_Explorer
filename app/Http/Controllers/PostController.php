<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $search   = $request->search;
        $category = $request->category;

        $posts = Post::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->when($category, function ($query) use ($category) {
                $query->where('category', $category);
            })
            ->when($search, function ($query) use ($search) {
                $query->orderByRaw("
                    CASE 
                        WHEN title LIKE ? THEN 1
                        WHEN content LIKE ? THEN 2
                        ELSE 3
                    END ASC
                ", ["%{$search}%", "%{$search}%"]);
            })
            ->orderBy('id', 'desc')
            ->paginate(3)
            ->withQueryString();

        $facets = Post::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                      ->orWhere('content', 'like', "%{$search}%");
                });
            })
            ->select('category', DB::raw('COUNT(*) as count'))
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        return view('posts.index', compact('posts', 'facets', 'search', 'category'));
    }

    public function autocomplete(Request $request)
    {
        $term = $request->search;

        if (!$term || strlen($term) < 2) {
            return response()->json([]);
        }

        $suggestions = Post::query()
            ->where('title', 'like', "%{$term}%")
            ->orderByRaw("
                CASE 
                    WHEN title LIKE ? THEN 1
                    ELSE 2
                END
            ", ["{$term}%"])
            ->limit(6)
            ->select('id', 'title')
            ->get();

        return response()->json($suggestions);
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'    => 'required|min:3',
            'content'  => 'required|min:10',
            'category' => 'required|string|max:100',
        ]);

        Post::create([
            'title'    => $request->title,
            'content'  => $request->content,
            'category' => $request->category,
        ]);

        return redirect('/')->with('success', 'Post Created Successfully');
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $request->validate([
            'title'    => 'required|min:3',
            'content'  => 'required|min:10',
            'category' => 'required|string|max:100',
        ]);

        $post->update([
            'title'    => $request->title,
            'content'  => $request->content,
            'category' => $request->category,
        ]);

        return redirect('/')->with('success', 'Post Updated Successfully');
    }

    public function destroy(Post $post)
    {
        $post->delete();
        return redirect('/')->with('success', 'Post Deleted Successfully');
    }
}