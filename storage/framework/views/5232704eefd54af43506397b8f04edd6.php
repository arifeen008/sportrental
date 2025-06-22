 

<?php $__env->startSection('styles'); ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container py-4">
        
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h2 class="card-title text-primary mb-1">Admin Dashboard</h2>
                                <p class="card-text text-muted">ยินดีต้อนรับ, <?php echo e(Auth::user()->name); ?></p>
                            </div>
                            <a href="<?php echo e(route('user.booking.create')); ?>" class="btn btn-success">
                                <i class="fas fa-plus-circle me-2"></i> สร้างการจองใหม่
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0 text-dark">
                            <i class="fas fa-exclamation-triangle me-2"></i> การจองที่ต้องดำเนินการ (รอตรวจสอบสลิป)
                        </h5>
                    </div>
                    <div class="card-body">
                        
                        <?php if($bookings->where('payment_status', 'verifying')->count() > 0): ?>
                            <ul class="list-group list-group-flush">
                                <?php $__currentLoopData = $bookings->where('payment_status', 'verifying'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="list-group-item d-flex flex-wrap justify-content-between align-items-center">
                                        <div>
                                            <strong>#<?php echo e($booking->id); ?> - <?php echo e($booking->user->name); ?></strong>
                                            <small class="d-block text-muted">
                                                วันที่ใช้บริการ: <?php echo e($booking->booking_date->format('d/m/Y')); ?> | ยอดเงิน:
                                                <?php echo e(number_format($booking->total_price, 2)); ?> บาท
                                            </small>
                                        </div>
                                        <div class="mt-2 mt-md-0">
                                            <?php if($booking->payment_status === 'verifying'): ?>
                                                <div class="d-flex justify-content-center gap-2">
                                                    
                                                    <button type="button" class="btn btn-sm btn-info"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#viewSlipModal-<?php echo e($booking->id); ?>">
                                                        <i class="fas fa-receipt me-1"></i> ดูสลิป
                                                    </button>

                                                    
                                                    <form action="<?php echo e(route('admin.booking.approve', $booking->id)); ?>"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('ยืนยันการอนุมัติการจอง #<?php echo e($booking->id); ?>?');">
                                                        <?php echo csrf_field(); ?>
                                                        <button type="submit" class="btn btn-sm btn-success">
                                                            <i class="fas fa-check-circle me-1"></i> อนุมัติ
                                                        </button>
                                                    </form>

                                                    
                                                    <button class="btn btn-sm btn-danger">ปฏิเสธ</button>
                                                </div>
                                            <?php elseif($booking->payment_status === 'paid'): ?>
                                                <span class="text-success fw-bold">การจองสมบูรณ์</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php else: ?>
                            <div class="alert alert-success text-center" role="alert">
                                ไม่มีรายการที่ต้องดำเนินการในขณะนี้
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-history me-2"></i> ประวัติการจองทั้งหมด</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>รหัส</th>
                                        <th>ผู้จอง</th>
                                        <th>วันที่ใช้บริการ</th>
                                        <th>รายการ</th>
                                        <th class="text-end">ยอดชำระ</th>
                                        <th class="text-center">สถานะ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $bookings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr>
                                            <td>#<?php echo e($booking->id); ?></td>
                                            <td><?php echo e($booking->user->name); ?></td>
                                            <td><?php echo e($booking->booking_date->format('d M Y')); ?></td>
                                            <td>
                                                <?php if($booking->booking_type === 'daily_package'): ?>
                                                    
                                                    
                                                    <strong><?php echo e($booking->price_calculation_details['package_name'] ?? 'แพ็กเกจเหมาวัน'); ?></strong>

                                                    
                                                    <?php if(isset($booking->price_calculation_details['rental_type'])): ?>
                                                        <small
                                                            class="d-block text-muted"><?php echo e($booking->price_calculation_details['rental_type']); ?></small>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    

                                                    
                                                    <strong><?php echo e($booking->fieldType->name ?? 'ไม่ระบุสนาม'); ?></strong>

                                                    
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
                                                    <span class="badge bg-secondary">ยังไม่ชำระเงิน</span>
                                                <?php elseif($booking->payment_status == 'verifying'): ?>
                                                    <span class="badge bg-warning text-dark">รอตรวจสอบ</span>
                                                <?php else: ?>
                                                    <span class="badge bg-dark"><?php echo e($booking->payment_status); ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted py-4">ยังไม่มีประวัติการจอง
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        
                        <?php echo e($bookings->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
    
    
    <?php $__currentLoopData = $bookings->where('payment_status', 'verifying'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $booking): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="modal fade" id="viewSlipModal-<?php echo e($booking->id); ?>" tabindex="-1"
            aria-labelledby="slipModalLabel-<?php echo e($booking->id); ?>" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="slipModalLabel-<?php echo e($booking->id); ?>">สลิปการโอนเงินสำหรับการจอง
                            #<?php echo e($booking->id); ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body text-center">
                        <?php if($booking->slip_image_path): ?>
                            
                            <img src="<?php echo e(Storage::url($booking->slip_image_path)); ?>" class="img-fluid" alt="Payment Slip">
                        <?php else: ?>
                            <p class="text-danger">ไม่พบไฟล์สลิป</p>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sportsrental\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>