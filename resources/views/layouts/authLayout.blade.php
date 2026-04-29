<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TN Municipal | Property Tax & Revenue Portal - @yield('title', 'Taxpayer Portal')</title>

    <!-- Bootstrap 5 CSS + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

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
        }

        /* FULLSCREEN HERITAGE BG */
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

        /* floating particles */
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

        /* Toast System */
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
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast.hide {
            transform: translateX(120%);
            opacity: 0;
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

        /* MAIN CARD */
        .auth-card {
            width: 100%;
            max-width: 1280px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 2rem;
            box-shadow: 0 30px 50px -20px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 215, 120, 0.2);
            display: flex;
            flex-wrap: wrap;
            overflow: hidden;
            z-index: 10;
            animation: fadeSlideUp 0.5s ease-out;
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

        /* LEFT SIDE BRANDING - Hidden on mobile */
        .login-hero {
            flex: 1.2;
            min-width: 260px;
            background: linear-gradient(135deg, #fbf7ef 0%, #fffaf2 100%);
            padding: 2rem 1.8rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #2c3e4e;
        }

        /* Hide left content on mobile devices */
        @media (max-width: 768px) {
            .login-hero {
                display: none;
            }

            .auth-card {
                max-width: 500px;
                margin: 0 auto;
            }

            .login-form-section {
                width: 100%;
                flex: none;
            }
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.8rem;
            flex-wrap: wrap;
        }

        .brand-icon {
            background: #e67e22;
            width: 50px;
            height: 50px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: white;
            box-shadow: 0 8px 16px rgba(230, 126, 34, 0.2);
        }

        .brand-text {
            font-size: 1.3rem;
            font-weight: 800;
            letter-spacing: -0.2px;
            line-height: 1.2;
            color: #1e3a5f;
        }

        .brand-sub {
            font-size: 0.7rem;
            font-weight: 600;
            color: #e67e22;
        }

        .hero-content h1 {
            font-size: 1.7rem;
            font-weight: 800;
            margin-bottom: 0.8rem;
            color: #1e3a5f;
        }

        @media (min-width: 769px) and (max-width: 992px) {
            .hero-content h1 {
                font-size: 1.8rem;
            }
        }

        @media (min-width: 993px) {
            .hero-content h1 {
                font-size: 2.2rem;
            }

            .brand-text {
                font-size: 1.5rem;
            }

            .login-hero {
                padding: 2.5rem;
            }
        }

        .hero-highlight {
            color: #e67e22;
            border-bottom: 2px solid #f39c12;
            display: inline-block;
        }

        .hero-description {
            font-size: 0.85rem;
            line-height: 1.45;
            color: #4a627a;
            margin-bottom: 1rem;
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
            margin-top: 1.8rem;
        }

        .quote {
            font-weight: 500;
            font-size: 0.75rem;
            border-left: 3px solid #e67e22;
            padding-left: 0.9rem;
            color: #5d6f83;
            line-height: 1.35;
        }

        /* RIGHT SIDE FORM SECTION */
        .login-form-section {
            flex: 1;
            min-width: 300px;
            background: white;
            padding: 1.8rem;
        }

        /* Mobile header (visible only on mobile) */
        .mobile-header {
            display: none;
            text-align: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #fef5e8;
        }

        .mobile-header .mobile-brand-icon {
            background: #e67e22;
            width: 45px;
            height: 45px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            margin-bottom: 0.5rem;
        }

        .mobile-header h3 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #1e3a5f;
            margin: 0.5rem 0 0.25rem;
        }

        .mobile-header p {
            font-size: 0.7rem;
            color: #e67e22;
            font-weight: 600;
            margin: 0;
        }

        @media (max-width: 768px) {
            .mobile-header {
                display: block;
            }
        }

        @media (min-width: 576px) {
            .login-form-section {
                padding: 2rem 2rem;
            }
        }

        @media (min-width: 769px) {
            .login-form-section {
                padding: 2.5rem 2.5rem;
            }
        }

        .form-header h2 {
            font-size: 1.6rem;
            font-weight: 700;
            color: #1e3a5f;
        }

        .form-header p {
            color: #5e7a93;
            font-size: 0.85rem;
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
            padding: 11px 14px 11px 44px;
            font-size: 0.9rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.2s;
            outline: none;
        }

        .input-field select {
            appearance: none;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="%23e67e22" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>');
            background-repeat: no-repeat;
            background-position: right 16px center;
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

        .invalid-feedback-custom {
            font-size: 0.7rem;
            color: #e74c3c;
            margin-top: 4px;
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
            padding: 12px 0;
            border: none;
            border-radius: 44px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
        }

        .login-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(230, 126, 34, 0.4);
        }

        .login-btn:disabled {
            opacity: 0.7;
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
            padding: 9px 6px;
            font-weight: 600;
            font-size: 0.7rem;
            color: #1e3a5f;
            cursor: pointer;
            transition: all 0.2s;
        }

        @media (min-width: 480px) {
            .sso-btn {
                font-size: 0.75rem;
                padding: 10px 0;
                min-width: 115px;
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

        /* File Upload Styles for Registration */
        .file-upload-mini {
            margin-top: 5px;
            background: #fef9f0;
            border: 1.5px dashed #e2e8f0;
            border-radius: 16px;
            padding: 10px;
            text-align: center;
            cursor: pointer;
            transition: 0.2s;
        }

        .file-upload-mini:hover {
            border-color: #e67e22;
            background: #fff6ea;
        }

        .file-preview-mini {
            margin-top: 8px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .preview-img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 12px;
            border: 2px solid #e67e22;
        }

        /* Password strength meter styles */
        .strength-container {
            margin-top: 0.5rem;
        }

        .strength-bar {
            height: 6px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 6px;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 10px;
            transition: width 0.3s ease, background 0.3s ease;
        }

        .strength-fill.weak {
            background: #e74c3c;
            width: 25%;
        }

        .strength-fill.fair {
            background: #f39c12;
            width: 50%;
        }

        .strength-fill.good {
            background: #3498db;
            width: 75%;
        }

        .strength-fill.strong {
            background: #27ae60;
            width: 100%;
        }

        .strength-text {
            font-size: 0.7rem;
            color: #7e8b9e;
        }

        .toggle-password {
            background: transparent;
            border: none;
            color: #e67e22;
            cursor: pointer;
            transition: color 0.2s;
            z-index: 5;
            position: absolute;
            right: 12px;
        }

        .toggle-password:hover {
            color: #c0392b;
        }

        @media (max-width: 480px) {
            body {
                padding: 0.75rem;
            }

            .auth-card {
                border-radius: 1.5rem;
                max-width: 100%;
            }

            .login-form-section {
                padding: 1.3rem;
            }

            .form-header h2 {
                font-size: 1.3rem;
            }

            .sso-buttons {
                flex-direction: column;
                gap: 0.6rem;
            }

            .sso-btn {
                width: 100%;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="heritage-bg">
        <img src="https://images.pexels.com/photos/8967822/pexels-photo-8967822.jpeg"
            alt="Tamil Nadu Government Heritage Building"
            onerror="this.src='https://images.pexels.com/photos/7655710/pexels-photo-7655710.jpeg?auto=compress&cs=tinysrgb&w=1600'">
    </div>
    <div class="bg-overlay"></div>
    <div class="particles" id="particles-container"></div>
    <div id="toast-container" class="toast-container"></div>

    <div class="auth-card">
        <!-- LEFT: Branding & municipal info (hidden on mobile) -->
        <div class="login-hero">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-landmark"></i></div>
                <div>
                    <div class="brand-text">Greater Chennai Corporation</div>
                    <div class="brand-sub">Tamil Nadu • Tax Recognition Portal</div>
                </div>
            </div>
            <div class="hero-content">
                <h1>Property Tax<br><span class="hero-highlight">Digital Seva</span></h1>
                <p class="hero-description">
                    Official portal for property tax assessment, online payment, and e-recognition certificates.
                    Government of Tamil Nadu initiative for transparent governance.
                </p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>e-Governance</span></div>
                    <div class="trust-item"><i class="fas fa-file-certificate"></i> <span>Tax Recognition</span></div>
                    <div class="trust-item"><i class="fas fa-hand-holding-usd"></i> <span>Online Collection</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“நேர்மையான வரி செலுத்துதல் - நகரத்தின் வளர்ச்சிக்கு” — Hon'ble Minister of Municipal
                    Administration</div>
            </div>
        </div>

        <!-- RIGHT: Dynamic Content -->
        <div class="login-form-section">
            <!-- Mobile Header (visible only on mobile) -->
            <div class="mobile-header">
                <div class="mobile-brand-icon">
                    <i class="fas fa-landmark"></i>
                </div>
                <h3>Greater Chennai Corporation</h3>
                <p>Tamil Nadu • Tax Recognition Portal</p>
            </div>

            @yield('content')
        </div>
    </div>


    <script>
        // ========== TOAST SYSTEM ==========
        window.showToast = function(type, title, message, duration = 4500) {
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
            if (duration > 0) setTimeout(() => {
                if (toast.parentNode) removeToast(toast);
            }, duration);
            return toast;
        };

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
            const btn = e.target.closest('.login-btn, .sso-btn');
            if (btn && btn.tagName === 'BUTTON') {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple-effect');
                const rect = btn.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                ripple.style.position = 'absolute';
                ripple.style.background = 'radial-gradient(circle, rgba(230,126,34,0.5), rgba(230,126,34,0))';
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
            for (let i = 0; i < 45; i++) {
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

        // Welcome toast only on desktop (optional)
        if (window.innerWidth > 768) {
            setTimeout(() => {
                if (window.showToast) showToast("info", "📜 Tamil Nadu Municipal Tax",
                    "Welcome to property tax e-portal | Secure GSTN integration", 4000);
            }, 500);
        }
    </script>

    @yield('script')
</body>

</html>
