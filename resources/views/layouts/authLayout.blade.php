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
        /* ===== RESET & GLOBAL ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0a0f2a 0%, #0a1a3a 50%, #0b1120 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            --x: 0px;
            --y: 0px;
            --mouse-x: 0px;
            --mouse-y: 0px;
        }

        /* Animated Gradient Mesh Background */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 30% 40%, rgba(79, 70, 229, 0.25), rgba(0, 0, 0, 0) 60%),
                radial-gradient(circle at 80% 70%, rgba(168, 85, 247, 0.2), rgba(0, 0, 0, 0) 55%),
                radial-gradient(circle at 20% 90%, rgba(59, 130, 246, 0.2), rgba(0, 0, 0, 0) 50%);
            pointer-events: none;
            z-index: 0;
            animation: meshFloat 20s ease infinite alternate;
        }

        @keyframes meshFloat {
            0% {
                opacity: 0.6;
                transform: scale(1) translate(0, 0);
            }

            100% {
                opacity: 1;
                transform: scale(1.05) translate(2%, 1%);
            }
        }

        /* Floating Blobs / Orbs (Premium look) */
        .floating-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.4;
            pointer-events: none;
            z-index: 0;
            animation: floatBlob 18s ease-in-out infinite alternate;
        }

        .blob-1 {
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(79, 70, 229, 0.7), rgba(139, 92, 246, 0.3));
            top: -10%;
            left: -15%;
            animation-duration: 22s;
        }

        .blob-2 {
            width: 50vw;
            height: 50vw;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.6), rgba(6, 182, 212, 0.2));
            bottom: -20%;
            right: -15%;
            animation-duration: 25s;
            animation-delay: -5s;
        }

        .blob-3 {
            width: 35vw;
            height: 35vw;
            background: radial-gradient(circle, rgba(168, 85, 247, 0.5), rgba(236, 72, 153, 0.2));
            top: 40%;
            left: 60%;
            animation-duration: 19s;
            animation-delay: -2s;
        }

        @keyframes floatBlob {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(5%, 8%) scale(1.1);
            }
        }

        /* Particles container (preserved structure, enhanced visuals) */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            pointer-events: none;
            animation: floatParticle linear infinite;
            backdrop-filter: blur(2px);
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }

            10% {
                opacity: 0.6;
            }

            90% {
                opacity: 0.6;
            }

            100% {
                transform: translateY(-20vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* ===== TOAST CONTAINER (enhanced design) ===== */
        .toast-container {
            position: fixed;
            bottom: 2rem;
            right: 1.5rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            max-width: 360px;
            width: calc(100% - 2rem);
        }

        /* Enhanced Toast Styles with spring/bounce + glassmorphism */
        .toast {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border-left: 4px solid;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.08);
            padding: 0.9rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.3s ease;
            position: relative;
            overflow: hidden;
            color: #fff;
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
            font-size: 1.6rem;
            flex-shrink: 0;
        }

        .toast-content {
            flex: 1;
        }

        .toast-title {
            font-weight: 700;
            font-size: 0.95rem;
            margin-bottom: 0.2rem;
        }

        .toast-message {
            font-size: 0.8rem;
            opacity: 0.85;
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            padding: 4px;
        }

        .toast-close:hover {
            color: white;
            transform: scale(1.1);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #818cf8, #c084fc);
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

        /* ===== AUTH CARD - Glassmorphism + Neumorphism hybrid ===== */
        .auth-card {
            width: 100%;
            max-width: 460px;
            background: rgba(15, 23, 42, 0.55);
            backdrop-filter: blur(16px);
            border-radius: 36px;
            padding: 2rem 1.8rem;
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.3), 0 0 0 0.5px rgba(255, 255, 255, 0.05), inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transition: transform 0.3s ease, box-shadow 0.4s ease;
            animation: cardEntrance 0.6s cubic-bezier(0.23, 1, 0.32, 1);
            position: relative;
            z-index: 10;
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: scale(0.92) translateY(25px) rotateX(-3deg);
            }

            100% {
                opacity: 1;
                transform: scale(1) translateY(0) rotateX(0);
            }
        }

        /* Mouse-follow subtle tilt/parallax effect */
        .auth-card:hover {
            transform: perspective(1000px) rotateX(0.5deg) rotateY(0.8deg);
            box-shadow: 0 30px 55px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.2);
        }

        /* ===== HEADER ===== */
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #4f46e5, #a855f7);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem auto;
            box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.4);
            transition: all 0.3s;
        }

        .brand-icon i {
            font-size: 2rem;
            color: white;
        }

        .auth-header h3 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f0f9ff, #c7d2fe);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.4rem;
            letter-spacing: -0.3px;
        }

        .subtitle {
            color: #a5b4fc;
            font-size: 0.85rem;
            font-weight: 500;
        }

        /* ===== FORM FLOATING LABELS + INPUTS (modern) ===== */
        .form-floating {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .form-control {
            background: rgba(20, 30, 55, 0.6);
            border: 1px solid rgba(165, 180, 252, 0.3);
            border-radius: 20px;
            padding: 1rem 1rem 0.6rem 1rem;
            height: 58px;
            font-size: 1rem;
            color: #f1f5f9;
            transition: all 0.25s ease;
            backdrop-filter: blur(4px);
        }

        .form-control:focus {
            border-color: #818cf8;
            box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.3), inset 0 1px 2px rgba(0, 0, 0, 0.1);
            background: rgba(30, 41, 59, 0.8);
            outline: none;
        }

        .form-floating label {
            color: #94a3b8;
            font-weight: 500;
            padding: 0.9rem 1rem;
            transition: all 0.25s cubic-bezier(0.2, 1, 0.3, 1);
            pointer-events: none;
        }

        .form-floating .form-control:focus~label,
        .form-floating .form-control:not(:placeholder-shown)~label {
            transform: scale(0.85) translateY(-0.5rem) translateX(0.2rem);
            color: #c7d2fe;
            background: transparent;
        }

        .form-floating i {
            margin-right: 6px;
            color: #818cf8;
            transition: transform 0.2s, color 0.2s;
        }

        .form-control:focus+label i {
            transform: scale(1.05);
            color: #a78bfa;
        }

        /* ===== BUTTON ===== */
        .btn-auth {
            width: 100%;
            border-radius: 40px;
            background: linear-gradient(95deg, #4f46e5, #7c3aed, #c084fc);
            background-size: 180% auto;
            border: none;
            padding: 0.85rem;
            font-weight: 600;
            font-size: 1rem;
            color: white;
            transition: all 0.3s cubic-bezier(0.2, 0.9, 0.4, 1.1);
            position: relative;
            overflow: hidden;
            cursor: pointer;
            box-shadow: 0 6px 18px rgba(79, 70, 229, 0.3);
            margin-top: 0.5rem;
        }

        .btn-auth:hover {
            background-position: right center;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(123, 31, 162, 0.5);
        }

        .btn-auth:active {
            transform: scale(0.97) translateY(1px);
            transition: transform 0.08s linear;
        }

        /* DIVIDER */
        .divider {
            text-align: center;
            margin: 1.5rem 0;
            position: relative;
            color: #94a3b8;
            font-size: 0.8rem;
        }

        .divider span {
            background: rgba(15, 23, 42, 0.6);
            padding: 0 1rem;
            position: relative;
            z-index: 1;
        }

        .divider::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            width: 100%;
            height: 1px;
            background: rgba(165, 180, 252, 0.3);
        }

        /* LINK */
        .auth-link {
            text-align: center;
            margin-top: 0.5rem;
        }

        .auth-link a {
            color: #a5b4fc;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            position: relative;
        }

        .auth-link a:hover {
            color: #c7d2fe;
            text-shadow: 0 0 6px rgba(129, 140, 248, 0.5);
        }

        /* ===== FILE UPLOAD AREA (Enhanced) ===== */
        .file-upload-area {
            border: 1px dashed rgba(165, 180, 252, 0.5);
            border-radius: 24px;
            padding: 1.5rem;
            background: rgba(30, 41, 59, 0.3);
            text-align: center;
            transition: all 0.25s ease;
            cursor: pointer;
            backdrop-filter: blur(4px);
        }

        .file-upload-area:hover {
            border-color: #a78bfa;
            background: rgba(79, 70, 229, 0.15);
            box-shadow: 0 0 0 2px rgba(167, 139, 250, 0.2);
            transform: scale(1.01);
        }

        .file-upload-area.dragover {
            border-color: #c084fc;
            background: rgba(192, 132, 252, 0.2);
            box-shadow: 0 0 0 3px rgba(192, 132, 252, 0.4);
        }

        .file-preview img {
            max-width: 90px;
            border-radius: 16px;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }

        .file-remove {
            background: rgba(239, 68, 68, 0.8);
            border: none;
            border-radius: 30px;
            padding: 5px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .file-remove:hover {
            background: #ef4444;
            transform: scale(0.96);
        }

        /* ripples effect (preserved) */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.4), rgba(255, 255, 255, 0));
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

        /* ===== RESPONSIVE (mobile-first improvements) ===== */
        @media (max-width: 576px) {
            .auth-card {
                margin: 1rem;
                padding: 1.5rem;
                border-radius: 28px;
            }

            .btn-auth {
                padding: 0.7rem;
            }

            .toast-container {
                right: 0.75rem;
                left: 0.75rem;
                max-width: calc(100% - 1.5rem);
            }

            .form-control {
                font-size: 0.9rem;
            }

            .brand-icon {
                width: 50px;
                height: 50px;
            }
        }

        /* Focus states for accessibility */
        .btn-auth:focus-visible,
        .form-control:focus-visible,
        .file-upload-area:focus-visible {
            outline: 2px solid #c084fc;
            outline-offset: 2px;
        }

        /* additional glow effect for inputs on focus */
        .form-control:focus {
            border-image: none;
            animation: glowPulse 1.2s infinite;
        }

        @keyframes glowPulse {
            0% {
                box-shadow: 0 0 0 0 rgba(129, 140, 248, 0.3);
            }

            70% {
                box-shadow: 0 0 0 6px rgba(129, 140, 248, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(129, 140, 248, 0);
            }
        }

        /* card subtle shimmer */
        .auth-card::after {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 36px;
            padding: 1px;
            background: radial-gradient(circle at 30% 20%, rgba(255, 255, 255, 0.2), transparent 80%);
            mask: linear-gradient(#fff, #fff) content-box, linear-gradient(#fff, #fff);
            -webkit-mask: linear-gradient(#fff, #fff) content-box, linear-gradient(#fff, #fff);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        /* custom placeholder */
        .form-control::placeholder {
            color: rgba(148, 163, 184, 0.6);
        }
    </style>
    @yield('css')
</head>

<body>

    <!-- Enhanced animated particles and floating premium blobs -->
    <div class="particles" id="particles-js"></div>
    <div class="floating-blob blob-1"></div>
    <div class="floating-blob blob-2"></div>
    <div class="floating-blob blob-3"></div>

    <div id="toast-container" class="toast-container"></div>

    <div class="auth-card animate__animated animate__zoomIn animate__faster">
        @yield('content')
    </div>

    {{-- jQuery + Bootstrap --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ========== Enhanced Toast System (PRESERVED LOGIC, BUT ANIMATIONS UPGRADED) ==========
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

        // ========== Ripple effect on buttons (preserved but with smoother visual) ==========
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

        // ========== Particle generator (enhanced with organic movement) ==========
        function createParticles() {
            const particlesContainer = document.querySelector('.particles');
            if (!particlesContainer) return;
            const particleCount = 80;
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                const size = Math.random() * 5 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = Math.random() * 12 + 8 + 's';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.opacity = Math.random() * 0.4 + 0.15;
                particle.style.background = `rgba(165, 180, 252, ${Math.random() * 0.4 + 0.2})`;
                particlesContainer.appendChild(particle);
            }
        }
        createParticles();

        // ========== Generic File Upload Handler (preserved) ==========
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
                                    <div class="file-info mt-2 text-light small">${file.name.length > 20 ? file.name.slice(0,18)+'...' : file.name}</div>
                                    <button type="button" class="file-remove mt-2 btn btn-sm btn-danger"><i class="fas fa-trash-alt me-1"></i> Remove</button>
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
                            <div class="file-info text-light">
                                <i class="fas fa-file-alt me-1"></i> ${file.name}
                                <button type="button" class="file-remove d-block mt-2 btn btn-sm btn-outline-danger"><i class="fas fa-times"></i> Remove</button>
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

        document.addEventListener('DOMContentLoaded', () => {
            initFileUploads();
            setTimeout(() => {
                showToast('success', '✨ Secure Access', 'Advanced protection ready', 3500);
            }, 500);
        });

        if (typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver(() => initFileUploads());
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    </script>

    @yield('js')

    @hasSection('content')
    @else
        {{-- Dummy fallback demo with modern interactions --}}
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
                showToast('success', 'Welcome', 'Authentication preview — secure zone ready.', 3000);
            });
            document.getElementById('demoSignupLink')?.addEventListener('click', (e) => {
                e.preventDefault();
                showToast('info', 'Explore', 'Replace @yield with custom registration forms.', 2800);
            });
            document.addEventListener("mousemove", (e) => {
                document.body.style.setProperty("--mouse-x", e.clientX + "px");
                document.body.style.setProperty("--mouse-y", e.clientY + "px");
                const dynamicBg =
                    `radial-gradient(circle at ${e.clientX}px ${e.clientY}px, rgba(99,102,241,0.2), #030617 70%)`;
                document.body.style.setProperty("background", dynamicBg);
            });
        </script>
    @endif
</body>

</html>
