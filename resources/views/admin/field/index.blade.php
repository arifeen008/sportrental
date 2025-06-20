@extends('layouts.app')
@section('content')
    <a href="{{ route('admin.add-field') }}" class="btn btn-primary mb-3">เพิ่มสนามกีฬาใหม่</a>
    <table class="table table-striped text-center align-middle">
        <thead>
            <tr>
                <th>รูป</th>
                <th>ชื่อสนาม</th>
                <th>ที่อยู่</th>
                <th>ขนาด</th>
                <th>สถานะ</th>
                <th>การจัดการ</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($fields as $field)
                <tr>
                    <td>
                        <img src="{{ asset($field->image_url) }}" alt="ภาพสนามฟุตบอล"
                            style="width: 80px; height: auto; border-radius: 4px;">
                    </td>
                    <td>{{ $field->name }}</td>
                    <td>{{ $field->address }}</td>
                    <td>{{ $field->size }}</td>
                    <td>{{ $field->status ? 'เปิดใช้งาน' : 'ปิดใช้งาน' }}</td>
                    <td>
                        <a href="{{ route('admin.edit-field', $field->id) }}" class="btn btn-sm btn-warning">แก้ไข</a>

                        <form action="{{ route('admin.delete-field', $field->id) }}" method="POST" style="display:inline-block;"
                            onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบสนามนี้?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                        </form>

                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
