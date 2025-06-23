<?php $__env->startSection('styles'); ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        .summary-table dt { font-weight: 500; }
        .summary-table dd { text-align: right; }
        .total-price { font-size: 1.5rem; font-weight: bold; color: var(--bs-danger); }
    </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">กรุณาตรวจสอบและยืนยันการจอง</h4>
                </div>
                <div class="card-body p-4">
                    <h5 class="card-title"><?php echo e($summary['title']); ?></h5>
                    <hr>
                    <dl class="row summary-table gy-2">
                        <?php if($summary['booking_inputs']['booking_type'] === 'hourly' || $summary['booking_inputs']['booking_type'] === 'membership'): ?>
                            <dt class="col-5">สนาม</dt>
                            <dd class="col-7"><?php echo e($summary['field_name']); ?></dd>
                        <?php else: ?>
                            <dt class="col-5">แพ็กเกจ</dt>
                            <dd class="col-7"><?php echo e($summary['package_name']); ?></dd>
                        <?php endif; ?>

                        <dt class="col-5">วันที่</dt>
                        <dd class="col-7"><?php echo e(thaidate('lที่ j F พ.ศ. Y', $summary['booking_date'])); ?></dd>

                        <dt class="col-5">เวลา</dt>
                        <dd class="col-7"><?php echo e($summary['time_range']); ?></dd>

                        <hr class="my-2">

                        <?php if($summary['booking_inputs']['booking_type'] === 'membership'): ?>
                            <dt class="col-5">ชั่วโมงที่จะถูกหัก</dt>
                            <dd class="col-7"><?php echo e($summary['hours_to_deduct']); ?> ชั่วโมง</dd>
                        <?php else: ?>
                            <?php if(isset($summary['base_price'])): ?>
                                
                                <dt class="col-5">ราคาเหมา</dt>
                                <dd class="col-7"><?php echo e(number_format($summary['base_price'], 2)); ?> บาท</dd>
                                <dt class="col-5">ค่าบริการล่วงเวลา</dt>
                                <dd class="col-7"><?php echo e(number_format($summary['overtime_cost'], 2)); ?> บาท</dd>
                            <?php else: ?>
                                
                                <dt class="col-5">ราคารวม (ก่อนหักส่วนลด)</dt>
                                <dd class="col-7"><?php echo e(number_format($summary['subtotal_price'], 2)); ?> บาท</dd>

                                
                                <?php if(isset($summary['discount_amount']) && $summary['discount_amount'] > 0): ?>
                                    <dt class="col-5 text-success">
                                        <i class="fas fa-tags me-1"></i> <?php echo e($summary['discount_reason']); ?>

                                    </dt>
                                    <dd class="col-7 text-success">-<?php echo e(number_format($summary['discount_amount'], 2)); ?> บาท</dd>
                                <?php endif; ?>
                                
                            <?php endif; ?>
                            
                            <hr class="my-2">
                            <dt class="col-5 fw-bold">ยอดชำระสุทธิ</dt>
                            <dd class="col-7 total-price"><?php echo e(number_format($summary['total_price'], 2)); ?> บาท</dd>
                        <?php endif; ?>

                        <?php if(!empty($summary['special_perks'])): ?>
                            <hr class="my-2">
                            <dt class="col-5 text-success">สิทธิพิเศษ</dt>
                            <dd class="col-7 text-success"><?php echo e($summary['special_perks']); ?></dd>
                        <?php endif; ?>
                    </dl>
                    
                    <?php if(isset($summary['deposit_amount'])): ?>
                        <div class="alert alert-warning mt-4">
                            <h5 class="alert-heading">เงื่อนไขการชำระเงิน</h5>
                            <p class="mb-2">กรุณาชำระเงินมัดจำ 50% เพื่อยืนยันการจองของท่านภายในวันที่ทำการจอง</p>
                            <ul class="list-unstyled mb-0">
                                <li>- ยอดมัดจำที่ต้องชำระวันนี้: <strong class="fs-5"><?php echo e(number_format($summary['deposit_amount'], 2)); ?> บาท</strong></li>
                                <li>- เงินประกันสนาม: <strong><?php echo e(number_format($summary['security_deposit'], 2)); ?> บาท</strong> (ชำระพร้อมยอดคงเหลือ)</li>
                            </ul>
                            <hr>
                            <p class="mb-0"><small>หมายเหตุ: ยอดคงเหลือและเงินประกันสนาม ต้องชำระล่วงหน้า 5 วันก่อนวันใช้งาน</small></p>
                        </div>
                    <?php endif; ?>
                    
                    <form action="<?php echo e(route('user.booking.store')); ?>" method="POST" class="mt-4">
                        <?php echo csrf_field(); ?>
                        
                        <?php $__currentLoopData = $summary['booking_inputs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if(is_array($value)): ?>
                                <?php $__currentLoopData = $value; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub_key => $sub_value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <input type="hidden" name="<?php echo e($key); ?>[<?php echo e($sub_key); ?>]" value="<?php echo e($sub_value); ?>">
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        
                        
                        <input type="hidden" name="base_price" value="<?php echo e($summary['subtotal_price'] ?? ($summary['base_price'] ?? 0)); ?>">
                        <input type="hidden" name="overtime_charges" value="<?php echo e($summary['overtime_cost'] ?? 0); ?>">
                        <input type="hidden" name="discount" value="<?php echo e($summary['discount_amount'] ?? 0); ?>">
                        <input type="hidden" name="total_price" value="<?php echo e($summary['total_price']); ?>">
                        <input type="hidden" name="hours_deducted" value="<?php echo e($summary['hours_to_deduct'] ?? null); ?>">
                        <input type="hidden" name="duration_in_hours" value="<?php echo e($summary['duration_in_hours']); ?>">
                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('user.booking.create')); ?>" class="btn btn-secondary">&laquo; แก้ไขข้อมูล</a>
                            <button type="submit" class="btn btn-success">ยืนยันการจอง</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sportsrental\resources\views/user/booking/confirm.blade.php ENDPATH**/ ?>