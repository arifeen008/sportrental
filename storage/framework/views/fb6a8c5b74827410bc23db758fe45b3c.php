
<?php $__env->startSection('content'); ?>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">ฟอร์มจองสนามฟุตบอล</h4>
                    </div>
                    <div class="card-body p-4">

                        <form action="<?php echo e(route('user.booking.confirm')); ?>" method="POST">
                            <?php echo csrf_field(); ?> <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="booking_type" class="form-label">ประเภทการจอง</label>
                                    <select class="form-select" id="booking_type" name="booking_type" required>
                                        <option value="" disabled selected>-- กรุณาเลือก --</option>
                                        <option value="hourly">เช่ารายชั่วโมง</option>
                                        <option value="daily_package">เช่าเหมาวัน</option>
                                        <option value="membership">ใช้บัตรสมาชิก</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="field_type_id" class="form-label">ประเภทสนาม</label>
                                    <select class="form-select" id="field_type_id" name="field_type_id" required>
                                        <option value="" disabled selected>-- กรุณาเลือก --</option>
                                        
                                        
                                        
                                        
                                        <option value="1">สนามกลางแจ้ง</option>
                                        <option value="2">สนามหลังคา</option>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="booking_date" class="form-label">วันที่ต้องการจอง</label>
                                    <input type="date" class="form-control" id="booking_date" name="booking_date"
                                        min="<?php echo e(date('Y-m-d')); ?>" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="start_time" class="form-label">เวลาเริ่ม</label>
                                    <select class="form-select" id="start_time" name="start_time" required>
                                        <?php for($i = 8; $i <= 21; $i++): ?>
                                            <option value="<?php echo e(sprintf('%02d', $i)); ?>:00"><?php echo e(sprintf('%02d', $i)); ?>:00
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="end_time" class="form-label">เวลาสิ้นสุด</label>
                                    <select class="form-select" id="end_time" name="end_time" required>
                                        <?php for($i = 9; $i <= 22; $i++): ?>
                                            <option value="<?php echo e(sprintf('%02d', $i)); ?>:00"><?php echo e(sprintf('%02d', $i)); ?>:00
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">หมายเหตุ (ถ้ามี)</label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                        placeholder="เช่น ขออุปกรณ์เพิ่มเติม, คำขอพิเศษอื่นๆ"></textarea>
                                </div>
                            </div>

                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">ตรวจสอบราคาและดำเนินการต่อ</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sportsrental\resources\views/user/booking/create.blade.php ENDPATH**/ ?>