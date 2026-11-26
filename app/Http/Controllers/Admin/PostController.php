<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Post;
use App\Support\Html;
use App\Support\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PostController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.posts.index', ['posts' => Post::when($request->q, fn ($q, $s) => $q->where('title', 'like', "%$s%"))->latest()->paginate(20)->withQueryString()]);
    }

    public function create()
    {
        return view('admin.posts.form', ['post' => new Post(['published_at' => now()])]);
    }

    public function store(Request $request)
    {
        $p = Post::create($this->validated($request) + ['user_id' => auth()->id()]);
        ActivityLog::record('post.create', $p, "Created post {$p->title}");

        return redirect()->route('admin.posts.edit', $p)->with('success', 'Article created.');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.form', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $post->update($this->validated($request, $post));
        ActivityLog::record('post.update', $post, "Updated post {$post->title}");

        return back()->with('success', 'Article saved.');
    }

    public function destroy(Post $post)
    {
        Media::delete($post->image);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Article deleted.');
    }

    private function validated(Request $request, ?Post $post = null): array
    {
        $request->merge(['slug' => Str::slug($request->input('slug') ?: $request->input('title', ''))]);
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'slug' => ['required', 'max:200', Rule::unique('posts')->ignore($post?->id)],
            'excerpt' => 'nullable|string|max:500',
            'body' => 'required|string|max:100000',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'published_at' => 'nullable|date',
            'image' => 'nullable|image|max:4096',
        ]);
        $data['body'] = Html::clean($data['body']);
        if (! $request->boolean('publish')) {
            $data['published_at'] = null;
        } else {
            $data['published_at'] = $data['published_at'] ?? now();
        }
        if ($request->hasFile('image')) {
            Media::delete($post?->image);
            $data['image'] = Media::store($request->file('image'), 'blog');
        } elseif ($request->boolean('remove_image')) {
            Media::delete($post?->image);
            $data['image'] = null;
        } else {
            unset($data['image']);
        }

        return $data;
    }
}
