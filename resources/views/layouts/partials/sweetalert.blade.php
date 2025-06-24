{{-- resources/views/layouts/partials/sweetalert.blade.php --}}
<script>
    // ใช้ Event Listener เพื่อให้แน่ใจว่า SweetAlert จะทำงานหลังจากหน้าเว็บโหลดเสร็จ
    document.addEventListener('DOMContentLoaded', function() {

        // ตรวจสอบว่ามี session 'success' ถูกส่งกลับมาหรือไม่
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '{{ session('success') }}',
                timer: 3000, // แสดงผล 3 วินาทีแล้วหายไป
                timerProgressBar: true,
                showConfirmButton: false
            });
        @endif

        // ตรวจสอบว่ามี session 'error' ถูกส่งกลับมาหรือไม่
        @if (session('error'))
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}'
                // สำหรับ error เราจะไม่ตั้งเวลาให้หายไป ให้ผู้ใช้กดยืนยันเอง
            });
        @endif

        // ตรวจสอบว่ามี session 'warning' ถูกส่งกลับมาหรือไม่
        @if (session('warning'))
            Swal.fire({
                icon: 'warning',
                title: 'คำเตือน',
                text: '{{ session('warning') }}'
            });
        @endif

    });
</script>
