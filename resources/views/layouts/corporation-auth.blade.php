<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corporation Portal | Property Tax System')</title>

    <!-- Bootstrap 5 CSS + Icons + Animate.css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #1a3c5c 0%, #0f2b44 100%);
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated gradient orbs */
        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: #2d6a4f;
            filter: blur(120px);
            opacity: 0.15;
            top: -100px;
            right: -50px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob 12s infinite alternate ease-in-out;
        }

        body::after {
            content: "";
            position: absolute;
            width: 400px;
            height: 400px;
            background: #1b4332;
            filter: blur(140px);
            opacity: 0.2;
            bottom: -100px;
            left: -80px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob2 15s infinite alternate ease-in-out;
        }

        @keyframes floatBlob {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.15;
            }
            100% {
                transform: translate(40px, 30px) scale(1.2);
                opacity: 0.25;
            }
        }

        @keyframes floatBlob2 {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 0.15;
            }
            100% {
                transform: translate(-30px, -40px) scale(1.3);
                opacity: 0.25;
            }
        }

        /* Main card container */
        .auth-wrapper {
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.3), 0 10px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Split layout */
        .auth-grid {
            display: flex;
            flex-wrap: wrap;
        }

        .left-panel {
            flex: 1.2;
            background: linear-gradient(135deg, #1b5e3f 0%, #0f3b26 100%);
            padding: 2.8rem 2.2rem;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::after {
            content: "🏛️";
            font-size: 260px;
            position: absolute;
            bottom: -40px;
            right: -50px;
            opacity: 0.06;
            pointer-events: none;
            animation: spinSlow 30s infinite linear;
        }

        @keyframes spinSlow {
            from {
                transform: rotate(0deg);
            }
            to {
                transform: rotate(360deg);
            }
        }

        .right-panel {
            flex: 1;
            background: #ffffff;
            padding: 2.5rem 2.5rem;
        }

        /* Top emblem */
        .top-emblem {
            background: #ffffff;
            padding: 1rem 2rem 0.8rem 2rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 3px solid #2d6a4f;
        }

        .emblem-img {
            width: 70px;
            height: auto;
            transition: transform 0.3s ease;
        }

        .emblem-img:hover {
            transform: scale(1.05);
        }

        .gov-text h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #1a3c5c;
        }

        .gov-text p {
            font-size: 0.85rem;
            margin: 0;
            color: #5c6e7e;
            font-weight: 500;
        }

        .tamil-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #2d6a4f;
            letter-spacing: 0.5px;
        }

        .left-panel h3 {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
        }

        .feature-list li {
            margin-bottom: 1.1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.9rem;
            animation: slideInLeft 0.5s ease forwards;
            opacity: 0;
            animation-delay: calc(0.1s * var(--order, 1));
        }

        .feature-list li:nth-child(1) { --order: 1; }
        .feature-list li:nth-child(2) { --order: 2; }
        .feature-list li:nth-child(3) { --order: 3; }
        .feature-list li:nth-child(4) { --order: 4; }
        .feature-list li:nth-child(5) { --order: 5; }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-list li i {
            font-size: 1.25rem;
            width: 28px;
            color: #ffd166;
        }

        /* Form styling */
        .form-label {
            font-weight: 600;
            color: #1a3c5c;
            font-size: 0.85rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-right: none;
            color: #2d6a4f;
            border-color: #dee2e6;
        }

        .form-control {
            border-left: none;
            padding: 0.75rem;
            font-size: 0.9rem;
            border-color: #dee2e6;
            background-color: #ffffff;
            transition: all 0.25s;
        }

        .form-control:focus {
            border-color: #2d6a4f;
            box-shadow: 0 0 0 3px rgba(45, 106, 79, 0.2);
            background-color: #ffffff;
        }

        select.form-control {
            border-left: 1px solid #dee2e6;
        }

        .btn-primary-custom {
            background-color: #2d6a4f;
            border: none;
            padding: 0.8rem;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 60px;
            width: 100%;
            transition: all 0.3s;
            color: white;
            box-shadow: 0 4px 10px rgba(45, 106, 79, 0.3);
        }

        .btn-primary-custom:hover {
            background-color: #1b5e3f;
            transform: translateY(-2px);
            box-shadow: 0 12px 20px rgba(45, 106, 79, 0.4);
        }

        .btn-primary-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #2d6a4f;
            text-decoration: none;
            font-weight: 600;
        }

        .forgot-link:hover {
            text-decoration: underline;
            color: #1a3c5c;
        }

        hr {
            background-color: #e9ecef;
            opacity: 0.5;
        }

        /* Form transition animations */
        .form-container {
            transition: opacity 0.4s ease, transform 0.4s ease;
        }

        .form-container.fade-out {
            opacity: 0;
            transform: translateY(10px);
            pointer-events: none;
        }

        .form-container.fade-in {
            opacity: 1;
            transform: translateY(0);
        }

        /* Loader */
        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
            visibility: hidden;
            opacity: 0;
            transition: 0.2s;
        }

        .loader-overlay.active {
            visibility: visible;
            opacity: 1;
        }

        .spinner-custom {
            width: 60px;
            height: 60px;
            border: 5px solid #ffffff;
            border-top: 5px solid #2d6a4f;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Toast message */
        .toast-message {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 10000;
            background: white;
            border-left: 5px solid #2d6a4f;
            border-radius: 14px;
            padding: 14px 20px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            max-width: 340px;
            font-size: 0.9rem;
            font-weight: 500;
            color: #1a3c5c;
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Password strength */
        .strength-meter {
            height: 4px;
            background: #e9ecef;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-bar {
            width: 0%;
            height: 100%;
            transition: width 0.3s, background 0.3s;
        }

        /* Municipal badge */
        .municipal-badge {
            background: rgba(45, 106, 79, 0.15);
            border-radius: 40px;
            padding: 0.25rem 0.75rem;
            font-size: 0.7rem;
            font-weight: 600;
            color: #2d6a4f;
            display: inline-block;
        }

        /* File upload styles */
        .file-upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .file-upload-area:hover {
            border-color: #2d6a4f;
            background: #f0fdf4;
        }

        .file-upload-area.dragover {
            border-color: #2d6a4f;
            background: #e8f5e9;
            transform: scale(1.01);
        }

        .file-preview {
            margin-top: 15px;
            text-align: center;
        }

        .file-preview img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #2d6a4f;
            padding: 3px;
        }

        .file-remove {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .auth-grid {
                flex-direction: column;
            }
            .left-panel {
                text-align: center;
                padding: 2rem 1.5rem;
            }
            .feature-list li {
                justify-content: center;
            }
            .right-panel {
                padding: 2rem 1.5rem;
            }
            .top-emblem {
                padding: 0.8rem 1.2rem;
            }
            .gov-text h2 {
                font-size: 1.1rem;
            }
        }
    </style>

    @stack('styles')
</head>

<body>

    <div class="loader-overlay" id="loaderOverlay">
        <div class="spinner-custom"></div>
    </div>

    <div class="auth-wrapper">
        <!-- Top emblem - Corporation branding -->
        <div class="top-emblem">
            <img src="{{ asset('images/TamilNadu_Logo.png') }}" alt="TamilNadu" class="emblem-img" onerror="this.src='https://via.placeholder.com/70x70?text=TN'">
            <div class="gov-text">
                <h2>Tamil Nadu Corporation Portal</h2>
                <p>Urban Local Body | Property Tax & e-Governance Services</p>
                <div class="tamil-text">தமிழ்நாடு மாநகராட்சி | வரி மேலாண்மை அமைப்பு</div>
            </div>
        </div>

        <div class="auth-grid">
            <!-- Left panel - Corporation specific features -->
            <div class="left-panel">
                <h3><i class="fas fa-building me-2" style="font-size: 1.8rem; color: #ffd166;"></i> மாநகராட்சி</h3>
                <p>Access property tax, building approvals, trade licenses, water supply, and citizen services — all in one place.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-file-invoice-dollar"></i> <span>Property Tax & e-Payment</span></li>
                    <li><i class="fas fa-hard-hat"></i> <span>Building Plan Approval</span></li>
                    <li><i class="fas fa-tint"></i> <span>Water & Sewerage Connection</span></li>
                    <li><i class="fas fa-trash-alt"></i> <span>Solid Waste Management</span></li>
                    <li><i class="fas fa-chart-line"></i> <span>Real-time Analytics Dashboard</span></li>
                </ul>
                <div class="mt-4 pt-2 border-top border-light opacity-50 small d-flex gap-3 flex-wrap">
                    <span><i class="fas fa-mobile-alt"></i> Mobile App Integration</span>
                    <span><i class="fas fa-globe"></i> Smart City Mission</span>
                    <span><i class="fas fa-shield-alt"></i> Secure Portal</span>
                </div>
                <div class="mt-3">
                    <span class="municipal-badge"><i class="fas fa-check-circle me-1"></i> Official Corporation Portal</span>
                </div>
            </div>

            <!-- Right panel: Dynamic forms -->
            <div class="right-panel">
                @yield('form-content')
            </div>
        </div>

        <div class="bg-white py-2 text-center border-top" style="background: #ffffff !important; font-size: 0.7rem; color: #5c6e7e;">
            <i class="fas fa-shield-alt"></i> Secure SSL Portal | © {{ date('Y') }} Tamil Nadu Corporation | All Urban Local Bodies | e-Governance Initiative
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        // Toast message function
        function showToast(type, title, message, duration = 4000) {
            const icons = {
                success: 'fa-check-circle',
                error: 'fa-exclamation-triangle',
                warning: 'fa-exclamation-circle',
                info: 'fa-info-circle'
            };

            const toast = document.createElement('div');
            toast.className = 'toast-message';
            toast.innerHTML = `
                <div class="d-flex align-items-center gap-2">
                    <i class="fas ${icons[type] || 'fa-info-circle'}" style="color: ${type === 'success' ? '#2d6a4f' : '#dc3545'};"></i>
                    <div>
                        <strong>${title}</strong><br>
                        <small>${message}</small>
                    </div>
                    <button type="button" class="btn-close ms-2 btn-sm" style="font-size: 0.65rem;"></button>
                </div>
            `;
            document.body.appendChild(toast);
            const closeBtn = toast.querySelector('.btn-close');
            closeBtn.addEventListener('click', () => toast.remove());
            setTimeout(() => toast.remove(), duration);
        }

        // Toggle password visibility
        document.querySelectorAll('.toggle-pwd').forEach(btn => {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const pwdField = document.getElementById(targetId);
                if (pwdField) {
                    const type = pwdField.getAttribute('type') === 'password' ? 'text' : 'password';
                    pwdField.setAttribute('type', type);
                    const icon = this.querySelector('i');
                    icon.classList.toggle('fa-eye-slash');
                    icon.classList.toggle('fa-eye');
                }
            });
        });

        // Password strength meter
        function initPasswordStrength() {
            const regPassword = document.getElementById('regPassword');
            const strengthBar = document.getElementById('strengthBar');
            if (regPassword && strengthBar) {
                regPassword.addEventListener('input', function() {
                    let val = this.value;
                    let strength = 0;
                    if (val.length >= 6) strength += 1;
                    if (val.length >= 8) strength += 1;
                    if (/[A-Z]/.test(val)) strength += 1;
                    if (/[0-9]/.test(val)) strength += 1;
                    if (/[^A-Za-z0-9]/.test(val)) strength += 1;
                    let percent = Math.min(100, strength * 20);
                    strengthBar.style.width = percent + '%';
                    if (percent < 30) strengthBar.style.backgroundColor = '#dc3545';
                    else if (percent < 60) strengthBar.style.backgroundColor = '#ffc107';
                    else strengthBar.style.backgroundColor = '#28a745';
                });
            }
        }

        // File upload handling
        function initFileUpload() {
            const fileUploadArea = document.getElementById('fileUploadArea');
            const fileInput = document.getElementById('profile_picture');
            const filePreview = document.getElementById('filePreview');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');
            const removeFile = document.getElementById('removeFile');

            if (fileUploadArea && fileInput) {
                fileUploadArea.addEventListener('click', () => fileInput.click());
                fileUploadArea.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    fileUploadArea.classList.add('dragover');
                });
                fileUploadArea.addEventListener('dragleave', () => fileUploadArea.classList.remove('dragover'));
                fileUploadArea.addEventListener('drop', (e) => {
                    e.preventDefault();
                    fileUploadArea.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    if (files.length) {
                        fileInput.files = files;
                        handleFileSelect(files[0]);
                    }
                });

                fileInput.addEventListener('change', function() {
                    if (this.files.length) {
                        handleFileSelect(this.files[0]);
                    }
                });

                function handleFileSelect(file) {
                    if (file && file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                            filePreview.style.display = 'block';
                            fileUploadArea.style.display = 'none';
                            fileName.textContent = file.name;
                        };
                        reader.readAsDataURL(file);
                    }
                }

                if (removeFile) {
                    removeFile.addEventListener('click', function() {
                        fileInput.value = '';
                        filePreview.style.display = 'none';
                        fileUploadArea.style.display = 'block';
                        previewImage.src = '';
                    });
                }
            }
        }

        // Ripple effect
        const btns = document.querySelectorAll('.btn-primary-custom');
        btns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                let ripple = document.createElement('span');
                ripple.style.position = 'absolute';
                ripple.style.borderRadius = '50%';
                ripple.style.backgroundColor = 'rgba(255,255,255,0.6)';
                ripple.style.transform = 'scale(0)';
                ripple.style.animation = 'rippleAnim 0.4s linear';
                ripple.style.width = '40px';
                ripple.style.height = '40px';
                ripple.style.left = e.offsetX + 'px';
                ripple.style.top = e.offsetY + 'px';
                ripple.style.pointerEvents = 'none';
                btn.style.position = 'relative';
                btn.style.overflow = 'hidden';
                btn.appendChild(ripple);
                setTimeout(() => ripple.remove(), 400);
            });
        });

        const styleSheet = document.createElement("style");
        styleSheet.textContent = `@keyframes rippleAnim { from { transform: scale(0); opacity: 0.6; } to { transform: scale(12); opacity: 0; } }`;
        document.head.appendChild(styleSheet);

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            initPasswordStrength();
            initFileUpload();
        });
    </script>

    @stack('scripts')
</body>

</html>
