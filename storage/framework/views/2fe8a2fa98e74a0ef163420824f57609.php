<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Auth'); ?></title>

    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f0f2f5, #e2e8f0);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .auth-card {
            width: 100%;
            max-width: 450px;
            background: #fff;
            border-radius: 15px;

            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            padding: 35px;
        }

        .auth-card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
            border-radius: 20px;
        }
        .auth-card h3 {
            text-align: center;
            font-weight: 600;
            margin-bottom: 25px;
            color: #333;
        }
        /* Toast Notification Styles */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            background: #fff;
            border-radius: 8px;
            padding: 15px 20px;
            margin-bottom: 10px;
            min-width: 300px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            border-left: 4px solid;
            transform: translateX(400px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(400px);
            opacity: 0;
        }

        .toast-success {
            border-left-color: #28a745;
            background: linear-gradient(135deg, #f8fff9, #ffffff);
        }

        .toast-error {
            border-left-color: #dc3545;
            background: linear-gradient(135deg, #fff8f8, #ffffff);
        }

        .toast-warning {
            border-left-color: #ffc107;
            background: linear-gradient(135deg, #fffdf5, #ffffff);
        }

        .toast-info {
            border-left-color: #17a2b8;
            background: linear-gradient(135deg, #f5fdff, #ffffff);
        }

        .toast-icon {
            font-size: 20px;
            margin-right: 12px;
        }

        .toast-success .toast-icon {
            color: #28a745;
        }

        .toast-error .toast-icon {
            color: #dc3545;
        }

        .toast-warning .toast-icon {
            color: #ffc107;
        }

        .toast-info .toast-icon {
            color: #17a2b8;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 2px;
        }

        .toast-message {
            font-size: 13px;
            color: #666;
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            font-size: 16px;
            color: #999;
            cursor: pointer;
            padding: 0;
            margin-left: 15px;
            transition: color 0.3s;
        }

        .toast-close:hover {
            color: #666;
        }

        /* Progress Bar */
        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.3;
            width: 100%;
            transform: scaleX(1);
            transform-origin: left;
            animation: progressBar 5s linear forwards;
        }

        @keyframes progressBar {
            to {
                transform: scaleX(0);
            }
        }

        /* File Upload Styles */
        .file-upload-container {
            margin-bottom: 1rem;
        }

        .file-upload-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #333;
        }

        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: #f8f9fa;
        }

        .file-upload-area:hover {
            border-color: #28a745;
            background: #f0fff4;
        }

        .file-upload-area.dragover {
            border-color: #28a745;
            background: #e8f5e8;
        }

        .file-upload-area.has-file {
            border-color: #28a745;
            background: #f0fff4;
        }

        .file-upload-icon {
            font-size: 2rem;
            color: #6c757d;
            margin-bottom: 0.5rem;
        }

        .file-upload-text {
            margin-bottom: 0.5rem;
        }

        .file-upload-text .primary {
            color: #333;
            font-weight: 500;
        }

        .file-upload-text .secondary {
            color: #6c757d;
            font-size: 0.875rem;
        }

        .file-upload-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .file-upload-btn:hover {
            background: #218838;
        }

        .file-preview {
            margin-top: 1rem;
            text-align: center;
        }

        .file-preview img {
            max-width: 100px;
            max-height: 100px;
            border-radius: 4px;
            object-fit: cover;
        }

        .file-preview .file-info {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: #666;
        }

        .file-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.75rem;
            margin-top: 0.5rem;
        }

        .file-remove:hover {
            background: #c82333;
        }

        /* Hide actual file input */
        .file-input {
            display: none;
        }
    </style>

    <?php echo $__env->yieldContent('css'); ?>
</head>

<body>
    <div id="toast-container" class="toast-container"></div>
    <div class="auth-card">
        <?php echo $__env->yieldContent('content'); ?>
    </div>

    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showToast(type, title, message, duration = 5000) {
            const toastContainer = document.getElementById('toast-container');

            // Create toast element
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;

            // Icons for different toast types
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };

            toast.innerHTML = `
        <i class="fas ${icons[type]} toast-icon"></i>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <p class="toast-message">${message}</p>
        </div>
        <button class="toast-close">
            <i class="fas fa-times"></i>
        </button>
        <div class="toast-progress"></div>
    `;

            // Add to container
            toastContainer.appendChild(toast);

            // Animate in
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);

            // Close button event
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => {
                removeToast(toast);
            });

            // Auto remove after duration
            if (duration > 0) {
                setTimeout(() => {
                    if (toast.parentNode) {
                        removeToast(toast);
                    }
                }, duration);
            }

            return toast;
        }

        function removeToast(toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');

            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 500);
        }
    </script>
    <?php echo $__env->yieldContent('js'); ?>
</body>

</html>
<?php /**PATH D:\testinggayu\resources\views/layouts/authLayout.blade.php ENDPATH**/ ?>