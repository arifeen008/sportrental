<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    
    <link rel="icon" href="<?php echo e(asset('images/licensed-image.jpeg')); ?>" type="image/x-icon">
    <title><?php echo $__env->yieldContent('title', default: 'ระบบเช่าสนามกีฬา'); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
    <style>
        body {
            background-image: url("<?php echo e(asset('images/68-04-022.jpg')); ?>");
            background-size: cover;
            background-attachment: fixed;
            background-position: center center;
            background-repeat: no-repeat;
        }

        @media (max-width: 768px) {
            body {
                background-image: url("<?php echo e(asset('images/68-04-023.jpg')); ?>");
                background-attachment: scroll;
            }
        }
    </style>
    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            
            <a class="navbar-brand" href="<?php echo e(url('/')); ?>">
                <i class="fas fa-futbol me-2"></i>
                SKF STADIUM
            </a>

            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar"
                aria-controls="mainNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                    <?php if(auth()->guard()->guest()): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('login') ? 'active' : ''); ?>"
                                href="<?php echo e(route('login')); ?>">เข้าสู่ระบบ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo e(request()->routeIs('register') ? 'active' : ''); ?>"
                                href="<?php echo e(route('register')); ?>">สมัครสมาชิก</a>
                        </li>
                    <?php endif; ?>

                    <?php if(auth()->guard()->check()): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarUserDropdown" role="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo e(Auth::user()->name); ?>

                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarUserDropdown">
                                <?php if(Auth::user()->role == 'admin'): ?>
                                    <li><a class="dropdown-item" href="<?php echo e(route('admin.dashboard')); ?>">Admin Dashboard</a>
                                    </li>
                                <?php else: ?>
                                    <li><a class="dropdown-item" href="<?php echo e(route('user.dashboard')); ?>">แดชบอร์ดของฉัน</a>
                                    </li>
                                <?php endif; ?>

                                <li><a class="dropdown-item" href="<?php echo e(route('user.profile.edit')); ?>">แก้ไขข้อมูลส่วนตัว</a>
                                </li>

                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <a class="dropdown-item" href="<?php echo e(route('logout')); ?>"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="fas fa-sign-out-alt fa-fw me-2"></i>ออกจากระบบ
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    <?php endif; ?>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\xampp\htdocs\sportsrental\resources\views/layouts/app.blade.php ENDPATH**/ ?>