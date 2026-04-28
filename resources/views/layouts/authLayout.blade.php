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
        /* ===== GLOBAL ===== */
        /* ===== BASE ===== */
        body {
            font-family: 'Inter', sans-serif;
            background: #f1f5f9;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== CARD ===== */
        .auth-card {
            width: 100%;
            max-width: 440px;
            background: #ffffff;
            border-radius: 12px;
            padding: 2rem;

            border: 1px solid #e2e8f0;

            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);

            animation: fadeInUp 0.4s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== HEADER ===== */
        .auth-header {
            text-align: center;
            margin-bottom: 1.5rem;
        }

        .auth-header h3 {
            font-size: 1.6rem;
            font-weight: 600;
            color: #1e293b;
        }

        .subtitle {
            color: #64748b;
            font-size: 0.9rem;
        }

        /* ===== INPUTS ===== */
        .form-control {
            border-radius: 8px;
            border: 1px solid #cbd5e1;
            padding: 0.7rem 0.9rem;
            font-size: 0.95rem;
            transition: border 0.2s ease, box-shadow 0.2s ease;
        }

        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15);
        }

        .form-floating label {
            color: #475569;
        }

        /* ===== BUTTON ===== */
        .btn-auth {
            width: 100%;
            border-radius: 8px;
            background: #2563eb;
            border: none;
            padding: 0.7rem;
            font-weight: 600;
            color: white;
            transition: background 0.2s ease, transform 0.1s ease;
        }

        .btn-auth:hover {
            background: #1d4ed8;
        }

        .btn-auth:active {
            transform: scale(0.98);
        }

        /* ===== LINKS ===== */
        .auth-link a {
            color: #2563eb;
            font-weight: 500;
        }

        .auth-link a:hover {
            text-decoration: underline;
        }

        /* ===== FILE UPLOAD ===== */
        .file-upload-area {
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 1rem;
            background: #f8fafc;
            text-align: center;
            transition: border 0.2s ease;
        }

        .file-upload-area:hover {
            border-color: #2563eb;
        }

        /* ===== TOAST ===== */
        .toast {
            background: #ffffff;
            border-radius: 8px;
            border-left: 4px solid;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);

            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        /* ===== COLORS ===== */
        .toast-success {
            border-left-color: #16a34a;
        }

        .toast-error {
            border-left-color: #dc2626;
        }

        .toast-warning {
            border-left-color: #f59e0b;
        }

        .toast-info {
            border-left-color: #2563eb;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 576px) {
            .auth-card {
                margin: 1rem;
                padding: 1.5rem;
            }
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
            // mouse light follow
            document.addEventListener("mousemove", (e) => {
                const light = document.body;
                light.style.setProperty("--x", e.clientX + "px");
                light.style.setProperty("--y", e.clientY + "px");

                document.body.style.setProperty(
                    "background-position",
                    `${e.clientX / 50}px ${e.clientY / 50}px`
                );

                document.body.style.setProperty("--mouse-x", e.clientX + "px");
                document.body.style.setProperty("--mouse-y", e.clientY + "px");

                document.body.style.setProperty(
                    "background",
                    `radial-gradient(circle at ${e.clientX}px ${e.clientY}px, rgba(59,130,246,0.15), #020617 60%)`
                );
            });
        </script>
    @endif
</body>

</html>
