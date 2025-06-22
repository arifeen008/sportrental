<?php $__env->startSection('styles'); ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
    <div class="container py-4">
        
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card shadow-sm mb-4">
                    <div class="card-body text-center">
                        <h2 class="card-title text-primary mb-3">สวัสดี, <?php echo e(Auth::user()->name); ?>!</h2>
                        <p class="card-text lead">ยินดีต้อนรับสู่แดชบอร์ดส่วนตัวของคุณ</p>
                        <hr>
                        <div class="d-grid gap-2 col-md-6 mx-auto">
                            <a href="<?php echo e(route('user.booking.create')); ?>" class="btn btn-success btn-lg">
                                <i class="fas fa-plus-circle me-2"></i> ทำการจองใหม่
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row mt-2">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i> ประวัติการจองทั้งหมด
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัสจอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายการ</th>
                                        <th class="text-end">ยอดชำระ</th>
                                        <th class="text-center">สถานะ</th>
                                        <th class="text-center">จัดการ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            
                                            <td>#<?php echo e($booking->id); ?></td>

                                            
                                            <td><?php echo e($booking->booking_date->format('d M Y')); ?></td>

                                            
                                            <td>
                                                <?php if($booking->booking_type === 'daily_package'): ?>
                                                    <?php echo e($booking->price_calculation_details['package_name'] ?? 'เหมาวัน'); ?>

                                                    <small
                                                        class="d-block text-muted"><?php echo e($booking->price_calculation_details['rental_type'] ?? ''); ?></small>
                                                <?php else: ?>
                                                    <?php echo e($booking->fieldType->name ?? 'N/A'); ?>

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
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e($booking->payment_status); ?></span>
                                                <?php endif; ?>
                                            </td>

                                            
                                            <td class="text-center">
                                                <?php if($booking->payment_status == 'unpaid'): ?>
                                                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                                        data-bs-target="#uploadSlipModal-<?php echo e($booking->id); ?>">
                                                        <i class="fas fa-upload me-1"></i> อัปโหลดสลิป
                                                    </button>
                                                <?php else: ?>
                                                    <a href="#" class="btn btn-sm btn-outline-secondary">
                                                        <i class="fas fa-eye me-1"></i> ดูรายละเอียด
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">คุณยังไม่มีประวัติการจอง
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    
    
    <?php $__currentLoopData = $bookings->where('payment_status', 'unpaid'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="uploadSlipModal-<?php echo e($booking->id); ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    
                    <form action="<?php echo e(route('user.booking.uploadSlip', $booking->id)); ?>" method="POST"
                        enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="modal-header">
                            <h5 class="modal-title">อัปโหลดสลิปสำหรับ #<?php echo e($booking->id); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p>ยอดชำระ: <strong class="text-danger"><?php echo e(number_format($booking->total_price, 2)); ?>

                                    บาท</strong></p>
                            <div class="mb-3">
                                <label for="slipImage-<?php echo e($booking->id); ?>" class="form-label">เลือกไฟล์รูปภาพสลิป</label>
                                <input class="form-control" type="file" name="slip_image"
                                    id="slipImage-<?php echo e($booking->id); ?>" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                            <button type="submit" class="btn btn-primary">ยืนยันการอัปโหลด</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>
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