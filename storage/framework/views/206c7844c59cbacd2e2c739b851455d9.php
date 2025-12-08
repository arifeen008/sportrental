

<?php $__env->startSection('content'); ?>
    <div class="container py-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bold">ข่าวสารและกิจกรรมทั้งหมด</h1>
            <p class="lead text-muted">ติดตามโปรโมชันและการอัปเดตล่าสุดจาก SKF STADIUM</p>
        </div>

        <div class="row g-4">
            <?php $__empty_1 = true; $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card shadow-sm h-100">
                        <?php if($post->cover_image_path): ?>
                            <img src="<?php echo e(Storage::url($post->cover_image_path)); ?>" class="card-img-top"
                                alt="<?php echo e($post->title); ?>" style="height: 220px; object-fit: cover;">
                        <?php endif; ?>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?php echo e($post->title); ?></h5>
                            <p class="card-text text-muted flex-grow-1"><?php echo e(Str::limit($post->content, 120)); ?></p>
                            <div class="mt-auto">
                                <a href="<?php echo e(route('posts.show', $post)); ?>" class="btn btn-primary">อ่านเพิ่มเติม</a>
                            </div>
                        </div>
                        <div class="card-footer text-muted small">
                            เผยแพร่เมื่อ: <?php echo e(thaidate('j M Y', $post->published_at)); ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center">ยังไม่มีข่าวสารและกิจกรรมในขณะนี้</div>
                </div>
            <?php endif; ?>
        </div>

        <div class="d-flex justify-content-center mt-5">
            <?php echo e($posts->links()); ?>

        </div>

    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\sportsrental\resources\views/posts/index.blade.php ENDPATH**/ ?>