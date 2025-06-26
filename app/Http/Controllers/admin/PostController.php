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
                $coverImagePath = $request->file('cover_image')->store('posts/covers', 'public');
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
                    $path = $file->store('posts/galleries', 'public');
                    // สร้างข้อมูลในตาราง post_images โดยผูกกับ post ที่เพิ่งสร้าง
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

        $imagePath = $post->image_path;
        if ($request->hasFile('image')) {
            // ลบรูปเก่า (ถ้ามี)
            if ($post->image_path) {
                Storage::disk('public')->delete($post->image_path);
            }
            // --- ส่วนที่แก้ไข: สร้างชื่อไฟล์ใหม่ ---
            $file        = $request->file('image');
            $extension   = $file->getClientOriginalExtension();
            $newFilename = now()->format('YmdHis') . '.' . $extension;
            // อัปโหลดรูปใหม่ด้วยชื่อใหม่
            $imagePath = $file->storeAs('posts', $newFilename, 'public');
            // --- สิ้นสุดส่วนที่แก้ไข ---
        }

        $post->update([
            'title'        => $validated['title'],
            'content'      => $validated['content'],
            'status'       => $validated['status'],
            'image_path'   => $imagePath, // <-- ใช้ path ที่มีชื่อไฟล์ใหม่
            'published_at' => ($validated['status'] == 'published' && ! $post->published_at) ? now() : $post->published_at,
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

        // ส่งข้อมูลไปยัง View
        return view('posts.show', compact('post'));
    }
}
