<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = cache()->remember(
            'posts.index.page.' . request('page', 1) . '.q.' . request('q', ''),
            now()->addMinutes(5),
            fn() => Post::with(['author', 'comments'])
                ->when(request('q'), fn($q) => $q->where('title', 'like', '%' . request('q') . '%'))
                ->latest()->paginate(10)
        );
        return view('posts.index', compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePostRequest $request)
    {
        $post = Post::create(['title' => $request->title, 'content' => $request->content, 'user_id' => auth()->id()]);
        cache()->forget('posts.index.page.*');
        return redirect()->route('posts.show', $post)->with('success', 'Post created');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['author', 'comments.author']); // eager loads
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePostRequest $request, Post $post)
    {
        $post->update($request->validated());
        return back()->with('success', 'Post updated');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $this->authorize('delete', $post); // via policy or manual check
        $post->delete(); // soft delete
        return redirect()->route('posts.index')->with('success', 'Post deleted');
    }
}
