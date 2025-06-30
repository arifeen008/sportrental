<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::latest()->paginate(10);
        return view('admin.posts.index', compact('posts'));
    }

    public function posts()
    {
        $posts = Post::where('status', 'published')
            ->latest('published_at')
            ->paginate(9);
        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'          => 'required|string|max:255',
            'content'        => 'required|string',
            'status'         => 'required|in:draft,published',
            'cover_image'    => 'nullable|image',
            'other_images.*' => 'nullable|image', // ตรวจสอบทุกไฟล์ใน array
        ]);

        // ใช้ Transaction เพื่อความปลอดภัย
        DB::transaction(function () use ($request, $validated) {
            $coverImagePath = null;
            if ($request->hasFile('cover_image')) {
                $file           = $request->file('cover_image');
                $extension      = $file->getClientOriginalExtension();
                $newFilename    = now()->format('YmdHis') . '.' . $extension;
                $coverImagePath = $file->storeAs('posts/covers', $newFilename, 'public');
            }

            // 1. สร้าง Post หลักก่อน
            $post = Post::create([
                'title'            => $validated['title'],
                'content'          => $validated['content'],
                'status'           => $validated['status'],
                'cover_image_path' => $coverImagePath,
                'user_id'          => Auth::id(),
                'published_at'     => ($validated['status'] == 'published') ? now() : null,
            ]);

            // 2. ถ้ามีรูปภาพประกอบอื่นๆ ให้วนลูปบันทึก
            if ($request->hasFile('other_images')) {
                foreach ($request->file('other_images') as $file) {
                    $extension   = $file->getClientOriginalExtension();
                    $newFilename = now()->format('YmdHis') . '_' . uniqid() . '.' . $extension;
                    $path        = $file->storeAs('posts/galleries', $newFilename, 'public');
                    $post->images()->create(['path' => $path]);
                }
            }
        });

        return redirect()->route('admin.posts.index')->with('success', 'สร้างข่าวสารสำเร็จแล้ว');
    }

    public function edit(Post $post)
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'required|in:draft,published',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $coverImagePath = $post->cover_image_path;
        if ($request->hasFile('cover_image')) {
            if ($post->cover_image_path) {
                Storage::disk('public')->delete($post->cover_image_path);
            }
            $file           = $request->file('cover_image');
            $extension      = $file->getClientOriginalExtension();
            $newFilename    = 'cover_' . now()->format('YmdHis') . '.' . $extension;
            $coverImagePath = $file->storeAs('posts/covers', $newFilename, 'public');
        }

        $post->update([
            'title'            => $validated['title'],
            'content'          => $validated['content'],
            'status'           => $validated['status'],
            'cover_image_path' => $coverImagePath,
            'published_at'     => ($validated['status'] == 'published' && ! $post->published_at) ? now() : $post->published_at,
        ]);

        return redirect()->route('admin.posts.index')->with('success', 'อัปเดตข่าวสารสำเร็จแล้ว');
    }

    public function destroy(Post $post)
    {
        if ($post->image_path) {
            Storage::disk('public')->delete($post->image_path);
        }
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'ลบข่าวสารสำเร็จแล้ว');
    }

    /**
     * แสดงหน้ารายละเอียดของข่าวสาร
     */
    public function show(Post $post)
    {
        // โหลดรูปภาพประกอบทั้งหมดมาด้วย (Eager Loading)
        $post->load('images');

        // 2. ดึงข่าวสารอื่นๆ (ไม่รวมโพสต์ปัจจุบัน) มา 5 เรื่องล่าสุดเพื่อแสดงใน Sidebar
        $otherPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id) // ไม่เอาโพสต์ที่กำลังดูอยู่
            ->latest('published_at')
            ->take(5)
            ->get();

        // ส่งข้อมูลไปยัง View
        return view('posts.show', compact('post', 'otherPosts'));
    }
}
