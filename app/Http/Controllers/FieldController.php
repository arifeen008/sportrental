<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Field;
use Illuminate\Http\Request;

// สำหรับการจัดการไฟล์
class FieldController extends Controller
{
    public function index()
    {
        $fields = Field::all();                              // ดึงข้อมูลสนามทั้งหมด
        return view('admin.field.index', compact('fields')); // ส่งข้อมูลไปยัง view
    }

    public function create()
    {
        return view('admin.field.create');
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'size'        => 'nullable|string|max:50',
            'status'      => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image_url = null;

        if ($request->hasFile('image')) {
            $image           = $request->file('image');
            $fileName        = time() . '_' . $image->getClientOriginalName();
            $destinationPath = public_path('fields'); // public/fields

            // สร้างโฟลเดอร์ถ้าไม่มี
            if (! file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }

            // ย้ายไฟล์ไป public/fields
            $image->move($destinationPath, $fileName);

            // เก็บพาธแบบ relative สำหรับแสดงในเว็บ
            $image_url = 'fields/' . $fileName;
        }

        Field::create([
            'name'        => $validatedData['name'],
            'address'     => $validatedData['address'],
            'description' => $validatedData['description'],
            'size'        => $validatedData['size'],
            'status'      => $validatedData['status'] ?? true,
            'image_url'   => $image_url,
        ]);

        return redirect()->route('admin.field.index')->with('success', 'สนามกีฬาถูกเพิ่มเรียบร้อยแล้ว!');
    }

    public function edit($id)
    {
        $field = Field::findOrFail($id);
        return view('admin.field.edit', compact('field'));
    }

    public function update(Request $request)
    {
        $id    = $request->input('id'); // ✅ รับ ID จาก form input
        $field = Field::findOrFail($id);

        $validatedData = $request->validate([
            'name'        => 'required|string|max:255',
            'address'     => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'size'        => 'nullable|string|max:50',
            'status'      => 'boolean',
            'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $image_url = $field->image_url;

        if ($request->hasFile('image')) {
            if ($image_url && file_exists(public_path($image_url))) {
                unlink(public_path($image_url)); // ✅ ลบไฟล์จาก public
            }

            $image    = $request->file('image');
            $fileName = time() . '_' . $image->getClientOriginalName();
            $destPath = public_path('fields');

            if (! file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }

            $image->move($destPath, $fileName);
            $image_url = 'fields/' . $fileName;
        }

        $field->update([
            'name'        => $validatedData['name'],
            'address'     => $validatedData['address'],
            'description' => $validatedData['description'],
            'size'        => $validatedData['size'],
            'status'      => $validatedData['status'] ?? true,
            'image_url'   => $image_url,
        ]);

        return redirect()->route('admin.field.index')->with('success', 'สนามกีฬาถูกอัพเดตเรียบร้อยแล้ว!');
    }

    public function destroy($id)
    {
        $field = Field::findOrFail($id); 
        // ลบไฟล์ภาพถ้าไฟล์มีอยู่จริง
        if ($field->image_url && file_exists(public_path($field->image_url))) {
            unlink(public_path($field->image_url));
        }

        $field->delete(); 

        return redirect()->route('admin.field.index')->with('success', 'สนามกีฬาถูกลบเรียบร้อยแล้ว!');
    }

}
