<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .booking-card .status-badge {
            font-size: 0.9rem;
        }
    </style>
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <div class="container py-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body p-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center">
                    <div>
                        <h2 class="h4">สวัสดี, <?php echo e(Auth::user()->name); ?>!</h2>
                        <p class="text-muted mb-0">ยินดีต้อนรับสู่แดชบอร์ดของคุณ</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <a href="<?php echo e(route('user.create.hourly')); ?>" class="btn btn-primary"><i
                                class="fas fa-plus-circle me-2"></i>จองสนามรายชั่วโมง</a>
                        <a href="<?php echo e(route('user.create.package')); ?>" class="btn btn-info"><i
                                class="fas fa-calendar-day me-2"></i>จองแบบเหมาวัน</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <?php if($activeMembership): ?>
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="far fa-id-card me-2"></i> บัตรสมาชิกของคุณ</h5>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-2">
                                <dt class="col-sm-4">ประเภทบัตร:</dt>
                                <dd class="col-sm-8"><?php echo e($activeMembership->membershipTier->tier_name); ?></dd>
                                <dt class="col-sm-4">ชั่วโมงคงเหลือ:</dt>
                                <dd class="col-sm-8 fw-bold fs-5 text-primary">
                                    <?php echo e(number_format($activeMembership->remaining_hours, 2)); ?> ชม.</dd>
                                <dt class="col-sm-4">วันหมดอายุ:</dt>
                                <dd class="col-sm-8"><?php echo e(thaidate('j F Y', $activeMembership->expires_at)); ?></dd>
                            </dl>
                            <a href="<?php echo e(route('user.create.membership')); ?>" class="btn btn-success w-100">
                                <i class="fas fa-calendar-plus me-2"></i> จองสนามด้วยบัตรนี้
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัสการจอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายละเอียด</th>
                                        <th class="text-end">ยอดชำระ (บาท)</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $myBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td><strong><?php echo e($booking->booking_code); ?></strong></td>
                                            <td><?php echo e(thaidate('j M Y', $booking->booking_date)); ?></td>
                                            <td>
                                                <?php if($booking->booking_type === 'daily_package'): ?>
                                                    <strong><?php echo e($booking->price_calculation_details['package_name'] ?? 'เหมาวัน'); ?></strong>
                                                    <small
                                                        class="d-block text-muted"><?php echo e($booking->price_calculation_details['rental_type'] ?? ''); ?></small>
                                                <?php else: ?>
                                                    <strong><?php echo e(optional($booking->fieldType)->name ?? 'ไม่ระบุ'); ?></strong>
                                                    <small class="d-block text-muted">
                                                        <?php echo e(\Carbon\Carbon::parse($booking->start_time)->format('H:i')); ?> -
                                                        <?php echo e(\Carbon\Carbon::parse($booking->end_time)->format('H:i')); ?> น.
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-end"><?php echo e(number_format($booking->total_price, 2)); ?></td>
                                            <td class="text-center">
                                                <?php if($booking->payment_status == 'paid'): ?>
                                                    <span class="badge bg-success">ชำระเงินแล้ว</span>
                                                <?php elseif($booking->payment_status == 'unpaid'): ?>
                                                    <span class="badge bg-warning text-dark">รอชำระเงิน</span>
                                                <?php elseif($booking->payment_status == 'verifying'): ?>
                                                    <span class="badge bg-info">รอตรวจสอบ</span>
                                                <?php elseif($booking->payment_status == 'rejected'): ?>
                                                    <span class="badge bg-danger">ถูกปฏิเสธ</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e($booking->payment_status); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if($booking->payment_status == 'unpaid'): ?>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#uploadSlipModal-<?php echo e($booking->id); ?>">
                                                        <i class="fas fa-upload me-1"></i> แจ้งชำระเงิน
                                                    </button>
                                                <?php else: ?>
                                                    <a href="#" class="btn btn-sm btn-outline-secondary disabled"
                                                        aria-disabled="true">รายละเอียด</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-5">
                                                <p class="mb-0">คุณยังไม่มีประวัติการจอง</p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php if($myBookings->hasPages()): ?>
                        <div class="card-footer">
                            <?php echo e($myBookings->links()); ?>

                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">
                            <i class="fas fa-calendar-check text-success me-2"></i>
                            ตารางจองที่ยืนยันแล้ว (7 วันข้างหน้า)
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if($confirmedBookings->isEmpty()): ?>
                            <div class="text-center p-4 text-muted">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <p>ยังไม่มีการจองที่ยืนยันแล้วในช่วงนี้</p>
                            </div>
                        <?php else: ?>
                            <ul class="list-group list-group-flush">
                                <?php $__currentLoopData = $confirmedBookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item px-0 py-3">
                                        <div class="d-flex w-100">
                                            <div class="text-center me-3" style="width: 65px;">
                                                <div class="bg-light rounded p-1 border">
                                                    <span
                                                        class="d-block small text-danger fw-bold"><?php echo e(thaidate('M', $booking->booking_date)); ?></span>
                                                    <span class="d-block h4 mb-0"><?php echo e($booking->booking_date->day); ?></span>
                                                    <span
                                                        class="d-block small text-muted"><?php echo e(thaidate('D', $booking->booking_date)); ?></span>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">
                                                    <?php if($booking->booking_type === 'daily_package'): ?>
                                                        <i class="fas fa-sun text-warning me-1"></i>
                                                        <?php echo e($booking->price_calculation_details['package_name'] ?? 'เหมาวัน'); ?>

                                                    <?php else: ?>
                                                        <i class="fas fa-futbol text-primary me-1"></i>
                                                        <?php echo e(optional($booking->fieldType)->name ?? 'ไม่ระบุ'); ?>

                                                    <?php endif; ?>
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="far fa-clock"></i>
                                                    <?php echo e(\Carbon\Carbon::parse($booking->start_time)->format('H:i')); ?> -
                                                    <?php echo e(\Carbon\Carbon::parse($booking->end_time)->format('H:i')); ?> น.
                                                </small>
                                            </div>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php $__currentLoopData = $myBookings->where('payment_status', 'unpaid'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="uploadSlipModal-<?php echo e($booking->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form action="<?php echo e(route('user.booking.uploadSlip', $booking)); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fas fa-file-invoice-dollar me-2"></i>แจ้งชำระเงินสำหรับ:
                                <?php echo e($booking->booking_code); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <p class="mb-1">ยอดที่ต้องชำระ: <strong
                                        class="fs-5"><?php echo e(number_format($booking->total_price, 2)); ?> บาท</strong></p>
                                <p class="small mb-0">โอนเงินมาที่: ธ.กสิกรไทย 255-1-03447-2 (สหกรณ์อิสลามปะกาสัย)</p>
                            </div>
                            <div class="text-center mb-3">
                                <img id="slip-preview-<?php echo e($booking->id); ?>" src="#" alt="ตัวอย่างสลิป"
                                    class="img-fluid rounded" style="max-height: 200px; display: none;">
                            </div>
                            <div class="mb-3">
                                <label for="slipImage-<?php echo e($booking->id); ?>" class="form-label">เลือกไฟล์รูปภาพสลิป</label>
                                <input class="form-control" type="file" name="slip_image"
                                    id="slipImage-<?php echo e($booking->id); ?>" required onchange="previewSlip(event)">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">ยืนยันการแจ้งชำระเงิน</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        function previewSlip(event) {
            const bookingId = event.target.id.split('-')[1];
            const previewImage = document.getElementById('slip-preview-' + bookingId);
            if (event.target.files && event.target.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                }
                reader.readAsDataURL(event.target.files[0]);
            }
        }
    </script>
<?php $__env->stopPush(); ?>
<?php $__env->startSection('scripts'); ?>
    <script>
        // ตรวจสอบว่ามี session 'success' หรือไม่
        <?php if(session('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
                text: '<?php echo e(session('success')); ?>',
                timer: 3000, // แสดงผล 3 วินาทีแล้วหายไป
                showConfirmButton: false
            });
        <?php endif; ?>

        // ตรวจสอบว่ามี session 'error' หรือไม่
        <?php if(session('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '<?php echo e(session('error')); ?>'
            });
        <?php endif; ?>

        // ตรวจสอบว่ามี session 'warning' หรือไม่
        <?php if(session('warning')): ?>
            Swal.fire({
                icon: 'warning',
                title: 'คำเตือน',
                text: '<?php echo e(session('warning')); ?>'
            });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sportsrental\resources\views/user/dashboard.blade.php ENDPATH**/ ?>