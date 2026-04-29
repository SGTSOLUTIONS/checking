<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TN Municipal | Property Tax Portal')</title>

    <!-- Bootstrap 5 CSS + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    @stack('styles')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background-color: #f4f2ef;
            position: relative;
            overflow-x: hidden;
        }

        /* Heritage Background */
        .heritage-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .heritage-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.82) contrast(1.02) saturate(1.05);
            transform: scale(1.02);
            animation: slowZoom 28s ease infinite alternate;
        }

        @keyframes slowZoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.06);
            }
        }

        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 248, 235, 0.4) 0%, rgba(255, 245, 225, 0.3) 100%);
            z-index: -1;
        }

        /* Particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .particle {
            position: absolute;
            background: rgba(230, 126, 34, 0.25);
            border-radius: 50%;
            pointer-events: none;
            animation: floatParticle linear infinite;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.5;
            }

            90% {
                opacity: 0.25;
            }

            100% {
                transform: translateY(-20vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Toast Container */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1rem;
            left: 1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            max-width: 380px;
            margin-left: auto;
            margin-right: auto;
        }

        @media (min-width: 576px) {
            .toast-container {
                left: auto;
                right: 1.5rem;
                margin-left: 0;
            }
        }

        .toast {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border-left: 4px solid;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
            padding: 0.8rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.7rem;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.35s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.3s ease;
            color: #1e2f3e;
            font-size: 0.9rem;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(120%);
            opacity: 0;
        }

        .toast-success {
            border-left-color: #27ae60;
        }

        .toast-error {
            border-left-color: #e74c3c;
        }

        .toast-warning {
            border-left-color: #f39c12;
        }

        .toast-info {
            border-left-color: #2980b9;
        }

        .toast-icon {
            font-size: 1.4rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 0.2rem;
        }

        .toast-message {
            font-size: 0.75rem;
            opacity: 0.8;
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            color: #7e8b9e;
            cursor: pointer;
            font-size: 0.8rem;
            padding: 0 4px;
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #e67e22, #f39c12);
            width: 0%;
            animation: progressShrink linear forwards;
        }

        @keyframes progressShrink {
            from {
                width: 100%;
            }

            to {
                width: 0%;
            }
        }

        /* Main Card - Responsive */
        .auth-card {
            width: 100%;
            max-width: 1280px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 2rem;
            box-shadow: 0 30px 50px -20px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 215, 120, 0.2);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            z-index: 10;
            position: relative;
            animation: fadeSlideUp 0.5s ease-out;
        }

        @media (min-width: 992px) {
            .auth-card {
                flex-direction: row;
            }
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Left Side Branding - Hidden on mobile, visible on desktop */
        .login-hero {
            flex: 1.2;
            background: linear-gradient(135deg, #fbf7ef 0%, #fffaf2 100%);
            padding: 2rem 1.8rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #2c3e4e;
            display: none;
            /* Hidden by default on mobile */
        }

        @media (min-width: 992px) {
            .login-hero {
                display: flex;
                /* Show on desktop */
                min-width: 260px;
            }
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
        }

        .brand-icon {
            background: #e67e22;
            width: 45px;
            height: 45px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            box-shadow: 0 8px 16px rgba(230, 126, 34, 0.2);
        }

        @media (min-width: 768px) {
            .brand-icon {
                width: 50px;
                height: 50px;
                font-size: 1.6rem;
            }
        }

        .brand-text {
            font-size: 1.1rem;
            font-weight: 800;
            letter-spacing: -0.2px;
            line-height: 1.2;
            color: #1e3a5f;
        }

        .brand-sub {
            font-size: 0.65rem;
            font-weight: 600;
            color: #e67e22;
        }

        @media (min-width: 768px) {
            .brand-text {
                font-size: 1.3rem;
            }

            .brand-sub {
                font-size: 0.7rem;
            }
        }

        .hero-content h1 {
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            color: #1e3a5f;
        }

        @media (min-width: 768px) {
            .hero-content h1 {
                font-size: 1.8rem;
            }
        }

        @media (min-width: 992px) {
            .hero-content h1 {
                font-size: 2rem;
            }
        }

        .hero-highlight {
            color: #e67e22;
            border-bottom: 2px solid #f39c12;
            display: inline-block;
        }

        .hero-description {
            font-size: 0.8rem;
            line-height: 1.45;
            color: #4a627a;
            margin-bottom: 1rem;
        }

        @media (min-width: 768px) {
            .hero-description {
                font-size: 0.85rem;
            }
        }

        .trust-badge {
            display: flex;
            gap: 0.6rem;
            flex-wrap: wrap;
            margin-top: 0.2rem;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 0.7rem;
            background: #fef5e8;
            padding: 4px 12px;
            border-radius: 40px;
            color: #b45f1b;
            font-weight: 500;
        }

        .quote-area {
            margin-top: 1.5rem;
        }

        .quote {
            font-weight: 500;
            font-size: 0.7rem;
            border-left: 3px solid #e67e22;
            padding-left: 0.9rem;
            color: #5d6f83;
            line-height: 1.35;
        }

        @media (min-width: 768px) {
            .quote {
                font-size: 0.75rem;
            }
        }

        /* Right Side Form - Full width on mobile */
        .login-form-section {
            flex: 1;
            background: white;
            padding: 1.5rem;
            width: 100%;
        }

        @media (min-width: 576px) {
            .login-form-section {
                padding: 2rem;
            }
        }

        @media (min-width: 768px) {
            .login-form-section {
                padding: 2rem;
            }
        }

        @media (min-width: 992px) {
            .login-form-section {
                padding: 2.5rem;
            }
        }

        /* Mobile Header - Only visible on mobile */
        .mobile-header {
            display: none;
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #fef5e8;
        }

        @media (max-width: 991px) {
            .mobile-header {
                display: block;
            }

            .mobile-header .brand-icon {
                margin: 0 auto 10px;
                width: 55px;
                height: 55px;
                font-size: 1.8rem;
            }

            .mobile-header .brand-text {
                font-size: 1.2rem;
            }

            .mobile-header .brand-sub {
                font-size: 0.7rem;
            }
        }

        .form-header h2 {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        @media (min-width: 768px) {
            .form-header h2 {
                font-size: 1.6rem;
            }
        }

        .form-header p {
            color: #5e7a93;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        .input-label {
            font-size: 0.8rem;
            font-weight: 600;
            color: #2c4c6e;
            margin-bottom: 6px;
            display: block;
        }

        .input-field {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-field i {
            position: absolute;
            left: 14px;
            color: #e67e22;
            font-size: 1rem;
            z-index: 2;
        }

        .input-field input,
        .input-field select {
            width: 100%;
            padding: 10px 14px 10px 44px;
            font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.2s;
            outline: none;
            font-family: 'Inter', 'Poppins', sans-serif;
        }

        @media (min-width: 768px) {

            .input-field input,
            .input-field select {
                padding: 11px 14px 11px 44px;
            }
        }

        .input-field input:focus,
        .input-field select:focus {
            border-color: #e67e22;
            box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.15);
        }

        .input-field input.is-invalid,
        .input-field select.is-invalid {
            border-color: #e74c3c;
        }

        .invalid-feedback {
            font-size: 0.75rem;
            color: #e74c3c;
            margin-top: 5px;
            display: block;
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0 1.5rem;
            flex-wrap: wrap;
            gap: 0.6rem;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            color: #2c4c6e;
        }

        .checkbox input {
            accent-color: #e67e22;
            width: 16px;
            height: 16px;
            margin: 0;
        }

        .forgot-link {
            font-size: 0.8rem;
            font-weight: 600;
            color: #e67e22;
            text-decoration: none;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: #c0392b;
        }

        .login-btn {
            background: linear-gradient(95deg, #e67e22, #f39c12);
            color: white;
            width: 100%;
            padding: 11px 0;
            border: none;
            border-radius: 44px;
            font-weight: 700;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
        }

        @media (min-width: 768px) {
            .login-btn {
                padding: 12px 0;
                font-size: 0.95rem;
            }
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(230, 126, 34, 0.4);
        }

        .login-btn:disabled {
            opacity: 0.7;
            transform: none;
            cursor: not-allowed;
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.5rem 0 1rem;
            color: #a0b8d0;
            font-size: 0.7rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #e9edf2;
        }

        .divider span {
            margin: 0 12px;
        }

        .sso-buttons {
            display: flex;
            gap: 0.7rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .sso-btn {
            flex: 1;
            min-width: 100px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 44px;
            padding: 8px 6px;
            font-weight: 600;
            font-size: 0.7rem;
            color: #1e3a5f;
            cursor: pointer;
            transition: all 0.2s;
        }

        @media (min-width: 768px) {
            .sso-btn {
                padding: 9px 6px;
            }
        }

        .sso-btn:hover {
            background: #fff4e6;
            border-color: #e67e22;
        }

        .register-prompt {
            text-align: center;
            margin-top: 1.5rem;
            font-size: 0.8rem;
            color: #5e7a93;
        }

        .register-prompt a {
            color: #e67e22;
            font-weight: 700;
            text-decoration: none;
        }

        .register-prompt a:hover {
            text-decoration: underline;
        }

        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0));
            transform: scale(0);
            animation: rippleAnim 0.5s ease-out;
            pointer-events: none;
        }

        @keyframes rippleAnim {
            to {
                transform: scale(6);
                opacity: 0;
            }
        }

        /* File Upload Styles */
        .file-upload-container {
            position: relative;
        }

        .file-input {
            position: absolute;
            width: 0;
            height: 0;
            opacity: 0;
            pointer-events: none;
        }

        .file-upload-area {
            cursor: pointer;
            border: 2px dashed #e2e8f0;
            padding: 20px;
            border-radius: 20px;
            text-align: center;
            transition: all 0.3s ease;
            background: #fef9f0;
        }

        .file-upload-area:hover {
            border-color: #e67e22;
            background: #fff6ea;
        }

        .file-upload-area.dragover {
            border-color: #e67e22;
            background: #fef0e0;
            transform: scale(1.01);
        }

        .file-upload-icon {
            margin-bottom: 12px;
            color: #e67e22;
        }

        .file-upload-text .primary {
            font-weight: 600;
            margin-bottom: 5px;
            color: #2c4c6e;
            font-size: 0.9rem;
        }

        .file-upload-text .secondary {
            color: #7e8b9e;
            font-size: 11px;
        }

        .file-preview {
            margin-top: 15px;
            text-align: center;
        }

        .file-preview img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e67e22;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .file-info {
            margin-top: 10px;
            font-size: 12px;
        }

        .file-remove {
            border: none;
            background: transparent;
            color: #dc3545;
            margin-top: 6px;
            cursor: pointer;
            font-size: 12px;
            transition: color 0.3s ease;
        }

        .file-remove:hover {
            color: #c82333;
            text-decoration: underline;
        }

        .btn-outline-success {
            border: 1px solid #e67e22;
            color: #e67e22;
            background: white;
            border-radius: 40px;
            padding: 6px 16px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-success:hover {
            background: #e67e22;
            color: white;
            border-color: #e67e22;
        }

        /* Extra small devices */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .auth-card {
                border-radius: 1.2rem;
            }

            .login-form-section {
                padding: 1.2rem;
            }

            .file-upload-area {
                padding: 15px;
            }

            .sso-buttons {
                flex-direction: column;
            }

            .sso-btn {
                width: 100%;
            }
        }
    </style>

    @stack('additional-styles')
</head>

<body>

    <div class="heritage-bg">
        <img src="https://images.pexels.com/photos/258117/pexels-photo-258117.jpeg?auto=compress&cs=tinysrgb&w=1600"
            alt="Tamil Nadu Government Heritage Building">
    </div>
    <div class="bg-overlay"></div>
    <div class="particles" id="particles-container"></div>
    <div id="toast-container" class="toast-container"></div>

    @yield('content')

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Toast system
        function showToast(type, title, message, duration = 4500) {
            const container = document.getElementById('toast-container');
            if (!container) return;

            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };

            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <i class="fas ${icons[type] || 'fa-circle-info'} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${escapeHtml(title)}</div>
                    <p class="toast-message">${escapeHtml(message)}</p>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
                <div class="toast-progress" style="animation-duration: ${duration/1000}s;"></div>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 20);

            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => removeToast(toast));

            if (duration > 0) {
                setTimeout(() => removeToast(toast), duration);
            }
            return toast;
        }

        function removeToast(toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 350);
        }

        function escapeHtml(str) {
            return String(str).replace(/[&<>]/g, (m) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;'
            } [m]));
        }

        // Ripple effect
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.login-btn, .sso-btn, .btn-primary, .btn-success, .btn-outline-success');
            if (btn && !btn.disabled) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple-effect');
                const rect = btn.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                ripple.style.position = 'absolute';
                ripple.style.background = 'radial-gradient(circle, rgba(255,255,255,0.5), rgba(255,255,255,0))';
                ripple.style.borderRadius = '50%';
                ripple.style.pointerEvents = 'none';
                btn.style.position = 'relative';
                btn.style.overflow = 'hidden';
                btn.appendChild(ripple);
                setTimeout(() => {
                    ripple.style.transform = 'scale(5)';
                    ripple.style.opacity = '0';
                }, 10);
                setTimeout(() => ripple.remove(), 500);
            }
        });

        // Particles
        function createParticles() {
            const container = document.querySelector('.particles');
            if (!container) return;
            for (let i = 0; i < 35; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                const size = Math.random() * 4 + 2;
                p.style.width = size + 'px';
                p.style.height = size + 'px';
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = Math.random() * 14 + 8 + 's';
                p.style.animationDelay = Math.random() * 12 + 's';
                p.style.background = `rgba(230, 126, 34, ${Math.random() * 0.35 + 0.1})`;
                container.appendChild(p);
            }
        }
        createParticles();

        // Global AJAX setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @yield('scripts')
</body>

</html>
