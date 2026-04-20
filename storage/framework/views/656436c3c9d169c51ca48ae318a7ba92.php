<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Surveyor Dashboard'); ?></title>

    <!-- ✅ Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- ✅ Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ✅ OpenLayers CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@latest/ol.css">

    <!-- ✅ jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">


    <!-- ✅ Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ OpenLayers JS -->
    <script src="https://cdn.jsdelivr.net/npm/ol@latest/dist/ol.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/ol-layerswitcher/4.1.0/ol-layerswitcher.js"></script>


    <!-- ✅ Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/ol@v7.4.0/ol.css">
<script src="https://cdn.jsdelivr.net/npm/ol@v7.4.0/dist/ol.js"></script>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4895ef;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --warning-color: #f72585;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fb;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            background: linear-gradient(180deg, var(--primary-color), var(--secondary-color));
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            z-index: 1050;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar.show {
            transform: translateX(0);
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .sidebar-header h3 {
            margin: 0;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 0.75rem 1.5rem;
            margin: 0.25rem 0.5rem;
            border-radius: 0.5rem;
            transition: all 0.2s;
            display: flex;
            align-items: center;
        }

        .sidebar-menu .nav-link:hover,
        .sidebar-menu .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .sidebar-menu .nav-link i {
            width: 24px;
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }

        /* Main Content */
        .main-content {
            margin-left: 0;
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .main-content.sidebar-open {
            margin-left: 280px;
        }

        /* Navbar */
        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 1rem 1.5rem;
        }

        .navbar-brand {
            font-weight: 600;
            color: var(--primary-color);
        }

        .btn-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: var(--dark-color);
        }

        /* Toasts */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }

        .toast {
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: none;
            overflow: hidden;
        }

        .toast-header {
            border-bottom: none;
        }

        /* Logout button reset */
        .logout-btn {
            background: none;
            border: none;
            color: inherit;
            font: inherit;
            cursor: pointer;
            padding: 0;
        }

        @media (max-width: 768px) {
            .main-content.sidebar-open {
                margin-left: 0;
            }

            .sidebar {
                width: 100%;
            }
        }
    </style>
    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-chart-line me-2"></i> SurveyPro</h3>
            <button class="btn-close btn-close-white" id="closeSidebar"></button>
        </div>

        <div class="sidebar-menu">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="<?php echo e(route('surveyor.dashboard')); ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-clipboard-list"></i> My Surveys
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-plus-circle"></i> Create Survey
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-chart-bar"></i> Analytics
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fas fa-users"></i> Respondents
                    </a>
                </li>
                <li class="nav-item mt-4">
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="nav-link logout-btn">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main -->
    <main class="main-content" id="mainContent">
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <button class="btn-toggle" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>

                <a class="navbar-brand ms-3" href="#">Surveyor Dashboard</a>

                <div class="d-flex align-items-center ms-auto">
                    <div class="dropdown me-3">
                        <button class="btn btn-light position-relative" type="button" id="notificationDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">New survey assigned</a></li>
                            <li><a class="dropdown-item" href="#">Survey completed</a></li>
                            <li><a class="dropdown-item" href="#">Weekly report ready</a></li>
                        </ul>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <img src="https://ui-avatars.com/api/?name=<?php echo e(urlencode(Auth::user()->name ?? 'Surveyor User')); ?>&background=4361ee&color=fff"
                                 class="rounded-circle me-2" width="32" height="32" alt="User">
                            <span><?php echo e(Auth::user()->name ?? 'Surveyor User'); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i> Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i> Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="dropdown-item logout-btn">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container-fluid py-4">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Toast Container -->
    <div class="toast-container"></div>

    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');

        document.getElementById('toggleSidebar').addEventListener('click', () => {
            sidebar.classList.add('show');
            mainContent.classList.add('sidebar-open');
        });

        document.getElementById('closeSidebar').addEventListener('click', () => {
            sidebar.classList.remove('show');
            mainContent.classList.remove('sidebar-open');
        });

        document.addEventListener('click', function(event) {
            if (window.innerWidth <= 768 &&
                !sidebar.contains(event.target) &&
                !document.getElementById('toggleSidebar').contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
                mainContent.classList.remove('sidebar-open');
            }
        });

        // Toast Function
        function showToast(message, type = 'success') {
            const container = document.querySelector('.toast-container');
            const id = 'toast-' + Date.now();

            const html = `
                <div class="toast align-items-center text-white bg-${type} border-0" id="${id}" role="alert">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'} me-2"></i> ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', html);
            const toastEl = document.getElementById(id);
            const toast = new bootstrap.Toast(toastEl);
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                showToast('Surveyor dashboard loaded successfully!', 'success');
            }, 800);
        });

    </script>
<script>
function sendLocation() {
    navigator.geolocation.getCurrentPosition((position) => {

        fetch('/surveyor/track-location', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                latitude: position.coords.latitude,
                longitude: position.coords.longitude
            })
        })
        .then(res => res.json())
        .then(data => console.log("Success:", data))
        .catch(err => console.error("Error:", err));

    });
}

// first call
sendLocation();

// every 2 minutes
setInterval(sendLocation, 120000);
</script>
    <?php echo $__env->yieldContent('script'); ?>
</body>
</html>
<?php /**PATH D:\testinggayu\resources\views/layouts/surveyor-layout.blade.php ENDPATH**/ ?>