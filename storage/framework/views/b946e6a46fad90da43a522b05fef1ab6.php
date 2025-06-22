<?php $__env->startSection('content'); ?>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">กรุณาตรวจสอบและยืนยันการจอง</h4>
                    </div>
                    <div class="card-body p-4">
                        <h5 class="card-title"><?php echo e($summary['title']); ?></h5>
                        <hr>
                        <dl class="row summary-table gy-2">
                            <?php if(
                                $summary['booking_inputs']['booking_type'] === 'hourly' ||
                                    $summary['booking_inputs']['booking_type'] === 'membership'): ?>
                                <dt class="col-5">สนาม</dt>
                                <dd class="col-7"><?php echo e($summary['field_name']); ?></dd>
                            <?php else: ?>
                                <dt class="col-5">แพ็กเกจ</dt>
                                <dd class="col-7"><?php echo e($summary['package_name']); ?></dd>
                            <?php endif; ?>

                            <dt class="col-5">วันที่</dt>
                            <dd class="col-7"><?php echo e($summary['booking_date_formatted']); ?></dd>

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
                                    
                                    <dt class="col-12">รายละเอียดราคา (<?php echo e($summary['duration_in_hours']); ?> ชั่วโมง)</dt>
                                    <?php $__currentLoopData = $summary['price_breakdown_details']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <dt class="col-6 ps-4"><small><?php echo e($detail['time']); ?></small></dt>
                                        <dd class="col-6"><small><?php echo e(number_format($detail['price'])); ?> บาท</small></dd>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                                <dt class="col-5 fw-bold">ราคารวม</dt>
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
                                    <li>- ยอดมัดจำที่ต้องชำระวันนี้: <strong
                                            class="fs-5"><?php echo e(number_format($summary['deposit_amount'], 2)); ?> บาท</strong>
                                    </li>
                                    <li>- เงินประกันสนาม: <strong><?php echo e(number_format($summary['security_deposit'], 2)); ?>

                                            บาท</strong></li>
                                </ul>
                                <hr>
                                <p class="mb-0"><small>หมายเหตุ: ยอดคงเหลือและเงินประกันสนาม ต้องชำระล่วงหน้า 5
                                        วันก่อนวันใช้งาน</small></p>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo e(route('user.booking.store')); ?>" method="POST" class="mt-4">
                            <?php echo csrf_field(); ?>

                            <?php $__currentLoopData = $summary['booking_inputs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <input type="hidden" name="total_price" value="<?php echo e($summary['total_price']); ?>">
                            <?php if(isset($summary['hours_to_deduct'])): ?>
                                <input type="hidden" name="hours_deducted" value="<?php echo e($summary['hours_to_deducted']); ?>">
                            <?php endif; ?>

                            <div class="d-flex justify-content-between">
                                <a href="<?php echo e(route('user.booking.create')); ?>" class="btn btn-secondary">&laquo;
                                    แก้ไขข้อมูล</a>
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