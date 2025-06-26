@extends('layouts.admin')
@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">จัดการข่าวสารและกิจกรรม</h1>
        <a href="{{ route('admin.posts.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>เขียนข่าวใหม่</a>
    </div>
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>หัวข้อ</th>
                            <th>ผู้เขียน</th>
                            <th class="text-center">สถานะ</th>
                            <th>วันที่เผยแพร่</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($posts as $post)
                            <tr>
                                <td>{{ Str::limit($post->title, 50) }}</td>
                                <td>{{ $post->user->name }}</td>
                                <td class="text-center">
                                    @if ($post->status == 'published')
                                        <span class="badge bg-success">เผยแพร่แล้ว</span>
                                    @else
                                        <span class="badge bg-secondary">ฉบับร่าง</span>
                                    @endif
                                </td>
                                <td>{{ $post->published_at ? thaidate('j M Y', $post->published_at) : '-' }}</td>
                                <td class="text-center">
                                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-warning"
                                        title="แก้ไข"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('admin.posts.destroy', $post) }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข่าวนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="ลบ"><i
                                                class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4">ยังไม่มีข่าวสาร</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($posts->hasPages())
            <div class="card-footer">
                {{ $posts->links() }}
            </div>
        @endif
    </div>
@endsection
