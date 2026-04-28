<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Auth') | Secure Access</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Google Font --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    {{-- Animate.css for extra animations --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated particles background */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            animation: floatParticle linear infinite;
            backdrop-filter: blur(2px);
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.8;
            }

            90% {
                opacity: 0.8;
            }

            100% {
                transform: translateY(-20vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* Animated gradient orb */
        .orb {
            position: fixed;
            width: 60vw;
            height: 60vw;
            background: radial-gradient(circle, rgba(46, 47, 48, 0.3), rgba(37, 99, 235, 0.05));
            border-radius: 50%;
            top: -20vh;
            right: -10vw;
            z-index: 0;
            animation: orbMove 20s ease-in-out infinite;
            filter: blur(60px);
            pointer-events: none;
        }

        .orb2 {
            position: fixed;
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.25), rgba(79, 70, 229, 0.05));
            border-radius: 50%;
            bottom: -20vh;
            left: -10vw;
            z-index: 0;
            animation: orbMove2 25s ease-in-out infinite;
            filter: blur(70px);
            pointer-events: none;
        }

        @keyframes orbMove {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(-30px, 40px) scale(1.05);
            }
        }

        @keyframes orbMove2 {

            0%,
            100% {
                transform: translate(0, 0) scale(1);
            }

            50% {
                transform: translate(40px, -30px) scale(1.08);
            }
        }

        /* Auth Card with depth and glass effect */
        .auth-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 470px;
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(0px);
            border-radius: 2rem;
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.2);
            padding: 2rem 2rem 2.2rem;
            transition: all 0.4s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            animation: cardGlow 3s ease-in-out infinite, slideUpFade 0.8s ease-out;
        }

        @keyframes slideUpFade {
            0% {
                opacity: 0;
                transform: translateY(40px) scale(0.96);
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes cardGlow {

            0%,
            100% {
                box-shadow: 0 25px 45px rgba(0, 0, 0, 0.2), 0 0 0 1px rgba(255, 255, 255, 0.2);
            }

            50% {
                box-shadow: 0 30px 55px rgba(0, 0, 0, 0.3), 0 0 0 2px rgba(59, 130, 246, 0.3);
            }
        }

        .auth-card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }

        /* Header with animated underline */
        .auth-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }

        .auth-header h3 {
            font-weight: 700;
            background: linear-gradient(135deg, #1e3a8a, #3b82f6);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            font-size: 1.85rem;
            margin-bottom: 0.5rem;
            letter-spacing: -0.3px;
        }

        .brand-icon {
            font-size: 2.8rem;
            background: linear-gradient(145deg, #2563eb, #1e40af);
            background-clip: text;
            -webkit-background-clip: text;
            color: transparent;
            margin-bottom: 0.25rem;
            display: inline-block;
            animation: pulseIcon 2s infinite;
        }

        @keyframes pulseIcon {

            0%,
            100% {
                transform: scale(1);
                text-shadow: 0 0 0px rgba(37, 99, 235, 0);
            }

            50% {
                transform: scale(1.05);
                text-shadow: 0 0 8px rgba(37, 99, 235, 0.4);
            }
        }

        .subtitle {
            color: #6b7280;
            font-size: 0.9rem;
            font-weight: 400;
        }

        /* Form styling */
        .form-floating {
            margin-bottom: 1.2rem;
        }

        .form-control,
        .form-select {
            border-radius: 1rem;
            border: 1.5px solid #e5e7eb;
            transition: all 0.25s ease;
            font-weight: 500;
            padding: 0.75rem 1rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            transform: scale(1.01);
        }

        /* Animated button */
        .btn-auth {
            width: 100%;
            padding: 0.8rem;
            border-radius: 2rem;
            font-weight: 600;
            font-size: 1rem;
            background: linear-gradient(95deg, #2563eb, #1d4ed8);
            border: none;
            color: white;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 1;
            margin-top: 0.5rem;
        }

        .btn-auth::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(95deg, #1e40af, #3b82f6);
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-auth:hover::before {
            left: 0;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-auth:active {
            transform: translateY(1px);
        }

        /* Link styling */
        .auth-link {
            text-align: center;
            margin-top: 1.2rem;
            font-size: 0.9rem;
        }

        .auth-link a {
            color: #2563eb;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            position: relative;
        }

        .auth-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #2563eb;
            transition: width 0.3s;
        }

        .auth-link a:hover::after {
            width: 100%;
        }

        .auth-link a:hover {
            color: #1e40af;
        }

        /* Floating label animations */
        .form-floating>label {
            padding: 0.5rem 1rem;
            font-weight: 500;
        }

        /* File upload enhanced style */
        .file-upload-container {
            margin-bottom: 1.2rem;
        }

        .file-upload-label {
            display: block;
            margin-bottom: 0.6rem;
            font-weight: 600;
            color: #1e293b;
            font-size: 0.9rem;
        }

        .file-upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 1.2rem;
            padding: 1.2rem;
            text-align: center;
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.2);
            cursor: pointer;
            background: #f8fafc;
        }

        .file-upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
            transform: scale(1.01);
        }

        .file-upload-area.dragover {
            border-color: #10b981;
            background: #ecfdf5;
            transform: scale(1.02);
        }

        .file-upload-icon {
            font-size: 2rem;
            color: #3b82f6;
            margin-bottom: 0.5rem;
            transition: transform 0.3s;
        }

        .file-upload-area:hover .file-upload-icon {
            transform: translateY(-4px);
        }

        .file-preview {
            margin-top: 1rem;
            text-align: center;
            animation: fadeSlide 0.4s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .file-preview img {
            max-width: 90px;
            max-height: 90px;
            border-radius: 16px;
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            border: 2px solid #fff;
        }

        .file-remove {
            background: #ef4444;
            color: white;
            border: none;
            padding: 5px 12px;
            border-radius: 30px;
            font-size: 0.7rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .file-remove:hover {
            background: #dc2626;
            transform: scale(1.02);
        }

        .file-input {
            display: none;
        }

        /* animated input group */
        .input-group-custom {
            position: relative;
        }

        /* toast improvements */
        .toast-container {
            position: fixed;
            top: 25px;
            right: 25px;
            z-index: 11000;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .toast {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 14px 18px;
            min-width: 320px;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
            border-left: 5px solid;
            transform: translateX(450px);
            opacity: 0;
            transition: all 0.5s cubic-bezier(0.34, 1.2, 0.64, 1);
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            overflow: hidden;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-success {
            border-left-color: #10b981;
        }

        .toast-error {
            border-left-color: #ef4444;
        }

        .toast-warning {
            border-left-color: #f59e0b;
        }

        .toast-info {
            border-left-color: #3b82f6;
        }

        .toast-icon {
            font-size: 24px;
        }

        .toast-success .toast-icon {
            color: #10b981;
        }

        .toast-error .toast-icon {
            color: #ef4444;
        }

        .toast-warning .toast-icon {
            color: #f59e0b;
        }

        .toast-info .toast-icon {
            color: #3b82f6;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.9rem;
        }

        .toast-message {
            font-size: 0.8rem;
            color: #475569;
        }

        .toast-close {
            background: none;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.2s;
        }

        .toast-close:hover {
            color: #1e293b;
            transform: scale(1.1);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: currentColor;
            opacity: 0.4;
            width: 100%;
            transform-origin: left;
            animation: progressBar 5s linear forwards;
        }

        @keyframes progressBar {
            to {
                transform: scaleX(0);
            }
        }

        /* divider */
        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            color: #94a3b8;
            font-size: 0.75rem;
            margin: 1rem 0;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .divider span {
            margin: 0 10px;
            font-weight: 500;
        }

        /* responsive */
        @media (max-width: 560px) {
            .auth-card {
                margin: 1rem;
                padding: 1.5rem;
            }

            .toast {
                min-width: 280px;
                right: 10px;
            }
        }

        /* ripple effect */
        .ripple {
            position: relative;
            overflow: hidden;
        }

        .ripple:after {
            content: "";
            display: block;
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            pointer-events: none;
            background-image: radial-gradient(circle, #fff 10%, transparent 10.01%);
            background-repeat: no-repeat;
            background-position: 50%;
            transform: scale(10);
            opacity: 0;
            transition: transform .5s, opacity 1s;
        }

        .ripple:active:after {
            transform: scale(0);
            opacity: 0.3;
            transition: 0s;
        }
    </style>
    @yield('css')
</head>

<body>

    <!-- Invisible animated particles & orbs for attraction -->
    <div class="particles" id="particles-js"></div>
    <div class="orb"></div>
    <div class="orb2"></div>

    <div id="toast-container" class="toast-container"></div>

    <div class="auth-card animate__animated animate__zoomIn animate__faster">
        @yield('content')
    </div>

    {{-- jQuery --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ========== Enhanced Toast System ==========
        function showToast(type, title, message, duration = 5000) {
            const toastContainer = document.getElementById('toast-container');
            const icons = {
                success: 'fa-circle-check',
                error: 'fa-circle-xmark',
                warning: 'fa-triangle-exclamation',
                info: 'fa-circle-info'
            };
            const toast = document.createElement('div');
            toast.className = `toast toast-${type}`;
            toast.innerHTML = `
                <i class="fas ${icons[type]} toast-icon"></i>
                <div class="toast-content">
                    <div class="toast-title">${escapeHtml(title)}</div>
                    <p class="toast-message">${escapeHtml(message)}</p>
                </div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
                <div class="toast-progress" style="animation-duration: ${duration/1000}s;"></div>
            `;
            toastContainer.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 50);
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => removeToast(toast));
            if (duration > 0) {
                setTimeout(() => {
                    if (toast.parentNode) removeToast(toast);
                }, duration);
            }
            return toast;
        }

        function removeToast(toast) {
            toast.classList.remove('show');
            toast.classList.add('hide');
            setTimeout(() => {
                if (toast.parentNode) toast.parentNode.removeChild(toast);
            }, 400);
        }

        function escapeHtml(str) {
            return str.replace(/[&<>]/g, function(m) {
                if (m === '&') return '&amp;';
                if (m === '<') return '&lt;';
                if (m === '>') return '&gt;';
                return m;
            });
        }

        // ========== Ripple effect on buttons ==========
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.btn-auth, .file-upload-btn, .file-remove');
            if (btn) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple-effect');
                const rect = btn.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                ripple.style.position = 'absolute';
                ripple.style.background = 'rgba(255,255,255,0.5)';
                ripple.style.borderRadius = '50%';
                ripple.style.pointerEvents = 'none';
                ripple.style.transform = 'scale(0)';
                ripple.style.transition = 'transform 0.4s, opacity 0.6s';
                btn.style.position = 'relative';
                btn.style.overflow = 'hidden';
                btn.appendChild(ripple);
                setTimeout(() => {
                    ripple.style.transform = 'scale(4)';
                    ripple.style.opacity = '0';
                }, 10);
                setTimeout(() => ripple.remove(), 500);
            }
        });

        // ========== Particle generator ==========
        function createParticles() {
            const particlesContainer = document.querySelector('.particles');
            const particleCount = 50;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                const size = Math.random() * 6 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = Math.random() * 8 + 6 + 's';
                particle.style.animationDelay = Math.random() * 10 + 's';
                particle.style.opacity = Math.random() * 0.5 + 0.2;
                particlesContainer.appendChild(particle);
            }
        }
        createParticles();

        // ========== Generic File Upload Handler (for any .file-upload-area) ==========
        function initFileUploads() {
            document.querySelectorAll('.file-upload-container').forEach(container => {
                const fileInput = container.querySelector('.file-input');
                const uploadArea = container.querySelector('.file-upload-area');
                const previewContainer = container.querySelector('.file-preview');
                const removeBtn = container.querySelector('.file-remove');

                if (!fileInput) return;

                const updatePreview = (file) => {
                    if (!previewContainer) return;
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            previewContainer.innerHTML = `
                                <div class="preview-inner">
                                    <img src="${e.target.result}" alt="Preview">
                                    <div class="file-info mt-2">${file.name.length > 20 ? file.name.slice(0,18)+'...' : file.name}</div>
                                    <button type="button" class="file-remove mt-2"><i class="fas fa-trash-alt me-1"></i> Remove</button>
                                </div>
                            `;
                            const newRemove = previewContainer.querySelector('.file-remove');
                            if (newRemove) {
                                newRemove.addEventListener('click', (e) => {
                                    e.stopPropagation();
                                    fileInput.value = '';
                                    previewContainer.innerHTML = '';
                                    if (uploadArea) uploadArea.classList.remove('has-file');
                                    showToast('info', 'File removed',
                                        'Selected file has been cleared.', 2000);
                                });
                            }
                            if (uploadArea) uploadArea.classList.add('has-file');
                        };
                        reader.readAsDataURL(file);
                    } else if (file) {
                        previewContainer.innerHTML = `
                            <div class="file-info">
                                <i class="fas fa-file-alt me-1"></i> ${file.name}
                                <button type="button" class="file-remove d-block mt-2"><i class="fas fa-times"></i> Remove</button>
                            </div>
                        `;
                        const newRemove = previewContainer.querySelector('.file-remove');
                        if (newRemove) newRemove.addEventListener('click', () => {
                            fileInput.value = '';
                            previewContainer.innerHTML = '';
                            if (uploadArea) uploadArea.classList.remove('has-file');
                        });
                        if (uploadArea) uploadArea.classList.add('has-file');
                    } else {
                        previewContainer.innerHTML = '';
                        if (uploadArea) uploadArea.classList.remove('has-file');
                    }
                };

                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        updatePreview(file);
                        if (uploadArea) showToast('success', 'File attached', `${file.name} is ready`,
                            2500);
                    } else {
                        updatePreview(null);
                    }
                });

                if (uploadArea) {
                    uploadArea.addEventListener('click', () => fileInput.click());
                    uploadArea.addEventListener('dragover', (e) => {
                        e.preventDefault();
                        uploadArea.classList.add('dragover');
                    });
                    uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
                    uploadArea.addEventListener('drop', (e) => {
                        e.preventDefault();
                        uploadArea.classList.remove('dragover');
                        const files = e.dataTransfer.files;
                        if (files.length > 0) {
                            fileInput.files = files;
                            updatePreview(files[0]);
                            showToast('success', 'File dropped', `${files[0].name} attached`, 2000);
                        }
                    });
                }

                if (removeBtn) {
                    removeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        fileInput.value = '';
                        updatePreview(null);
                        showToast('info', 'Reset', 'File removed', 1500);
                    });
                }
            });
        }

        // Run after any dynamic content, but also after DOM ready
        document.addEventListener('DOMContentLoaded', () => {
            initFileUploads();

            // Optional: Welcome toast after page load - attractive greeting
            setTimeout(() => {
                showToast('success', '🎉 Welcome back!', 'Secure access portal ready', 3500);
            }, 500);
        });

        // If there is AJAX / future forms, re-init file uploads for newly added forms
        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(() => initFileUploads());
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    </script>

    @yield('js')

    {{-- Example default content: login form if no yield? But we keep layout flexible, you can replace yield content --}}
    @hasSection('content')
    @else
        {{-- Dummy fallback nice demo --}}
        <div class="auth-header">
            <div class="brand-icon"><i class="fas fa-shield-alt"></i></div>
            <h3>Secure Access</h3>
            <div class="subtitle">Sign in to your account</div>
        </div>
        <form id="demoForm">
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" placeholder="name@example.com"
                    value="demo@example.com">
                <label for="email"><i class="fas fa-envelope me-2"></i>Email address</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" placeholder="Password" value="123456">
                <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
            </div>
            <button type="button" class="btn-auth ripple" id="demoLoginBtn"><i
                    class="fas fa-arrow-right-to-bracket me-2"></i> Sign In</button>
            <div class="divider"><span>or</span></div>
            <div class="auth-link">New user? <a href="#" id="demoSignupLink">Create account</a></div>
        </form>
        <script>
            document.getElementById('demoLoginBtn')?.addEventListener('click', () => {
                showToast('success', 'Login demo', 'Welcome! This is an interactive preview.', 3000);
            });
            document.getElementById('demoSignupLink')?.addEventListener('click', (e) => {
                e.preventDefault();
                showToast('info', 'Explore', 'You can replace @yield with custom registration forms.', 2800);
            });
        </script>
    @endif
</body>

</html>
