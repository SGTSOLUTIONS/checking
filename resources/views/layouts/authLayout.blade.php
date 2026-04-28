<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Corporate Access | Secure Login Hub</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Google Fonts: Inter & Plus Jakarta Sans for modern touch --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* ===== RESET & GLOBAL (Corporate refined from first design, merged with second's premium backdrop) ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #f5f7fc 0%, #e9eef5 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        /* Sophisticated pattern overlay from first design (corporate grid) */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(44, 107, 255, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(44, 107, 255, 0.03) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 0;
        }

        /* Abstract subtle blobs (preserving elegance, no distraction) */
        .floating-blob {
            position: fixed;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.25;
            pointer-events: none;
            z-index: 0;
            animation: floatBlob 18s ease-in-out infinite alternate;
        }

        .blob-1 {
            width: 40vw;
            height: 40vw;
            background: radial-gradient(circle, rgba(44, 107, 255, 0.4), rgba(79, 70, 229, 0.1));
            top: -15%;
            left: -10%;
        }

        .blob-2 {
            width: 45vw;
            height: 45vw;
            background: radial-gradient(circle, rgba(28, 78, 112, 0.3), rgba(6, 182, 212, 0.05));
            bottom: -20%;
            right: -10%;
        }

        @keyframes floatBlob {
            0% {
                transform: translate(0, 0) scale(1);
            }

            100% {
                transform: translate(5%, 8%) scale(1.1);
            }
        }

        /* particles (light & corporate) */
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
            background: rgba(44, 107, 255, 0.2);
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
                opacity: 0.4;
            }

            90% {
                opacity: 0.4;
            }

            100% {
                transform: translateY(-20vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* ===== TOAST SYSTEM (modern, clean, preserved logic) ===== */
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

        .toast {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            border-left: 4px solid;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08), 0 0 0 1px rgba(0, 0, 0, 0.02);
            padding: 0.9rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.3s ease;
            position: relative;
            overflow: hidden;
            color: #1e2f3e;
            font-family: 'Inter', sans-serif;
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
            font-size: 0.9rem;
            margin-bottom: 0.2rem;
        }

        .toast-message {
            font-size: 0.8rem;
            opacity: 0.8;
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            color: #7e8b9e;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .toast-close:hover {
            color: #1e2f3e;
            transform: scale(1.05);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #2C6BFF, #4f46e5);
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
            border-left-color: #2C6BFF;
        }

        /* ===== MAIN CARD: Hybrid design from first page (clean white, rounded, shadow) ===== */
        .auth-card {
            width: 100%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.02);
            display: flex;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 10;
            animation: fadeSlideUp 0.5s ease-out;
            padding: 0;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* LEFT SIDE: Brand / Hero section (corporate sophistication like first design) */
        .login-hero {
            flex: 1.2;
            background: linear-gradient(125deg, #0A2540 0%, #1C3E5C 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .login-hero::after {
            content: "";
            position: absolute;
            top: -30%;
            right: -20%;
            width: 320px;
            height: 320px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.08) 0%, rgba(255, 255, 255, 0) 70%);
            border-radius: 50%;
            pointer-events: none;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 3rem;
            z-index: 2;
        }

        .brand-icon {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .brand-text {
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.3px;
        }

        .hero-content {
            z-index: 2;
            margin-top: 1rem;
        }

        .hero-content h1 {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1.2;
            margin-bottom: 1rem;
            letter-spacing: -0.02em;
        }

        .hero-highlight {
            color: #FFD966;
            border-bottom: 2px solid rgba(255, 217, 102, 0.5);
        }

        .hero-description {
            font-size: 1rem;
            line-height: 1.5;
            opacity: 0.85;
            max-width: 90%;
            margin-bottom: 1.8rem;
        }

        .trust-badge {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 6px 14px;
            border-radius: 40px;
            backdrop-filter: blur(4px);
        }

        .quote-area {
            margin-top: auto;
            padding-top: 3rem;
            z-index: 2;
        }

        .quote {
            font-style: normal;
            font-weight: 500;
            font-size: 0.9rem;
            line-height: 1.4;
            border-left: 3px solid #FFD966;
            padding-left: 1rem;
            opacity: 0.9;
        }

        /* RIGHT SIDE: FORM SECTION (clean corporate) */
        .login-form-section {
            flex: 1;
            background: white;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 2rem;
        }

        .form-header h2 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #1E2F3E;
            letter-spacing: -0.3px;
        }

        .form-header p {
            color: #5B6E8C;
            margin-top: 8px;
            font-size: 0.95rem;
        }

        .input-group {
            margin-bottom: 1.5rem;
        }

        .input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #2C3E58;
            margin-bottom: 8px;
        }

        .input-field {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-field i {
            position: absolute;
            left: 16px;
            color: #8A99B4;
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .input-field input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid #E2E8F0;
            border-radius: 16px;
            background: #FFFFFF;
            transition: all 0.2s ease;
            outline: none;
            color: #1E2F3E;
            font-weight: 500;
        }

        .input-field input:focus {
            border-color: #2C6BFF;
            box-shadow: 0 0 0 4px rgba(44, 107, 255, 0.1);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 1rem 0 1.8rem;
        }

        .checkbox {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-size: 0.85rem;
            color: #3A4E6B;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #2C6BFF;
        }

        .forgot-link {
            font-size: 0.85rem;
            font-weight: 500;
            color: #2C6BFF;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #1645c0;
            text-decoration: underline;
        }

        .login-btn {
            background: linear-gradient(95deg, #0F2B3D 0%, #1C4E70 100%);
            color: white;
            width: 100%;
            padding: 14px 0;
            border: none;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 20px -10px rgba(28, 78, 112, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.8rem 0 1.5rem;
            color: #A0B0C8;
            font-size: 0.8rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid #E9EDF2;
        }

        .divider span {
            margin: 0 12px;
        }

        .sso-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .sso-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            background: #F8FAFE;
            border: 1px solid #E4E9F2;
            border-radius: 44px;
            padding: 11px 0;
            font-weight: 500;
            font-size: 0.85rem;
            color: #1F3A57;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Inter', sans-serif;
        }

        .sso-btn:hover {
            background: #ffffff;
            border-color: #C0CFE6;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        }

        .register-prompt {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #5C6F91;
        }

        .register-prompt a {
            color: #2C6BFF;
            font-weight: 600;
            text-decoration: none;
            margin-left: 5px;
        }

        .register-prompt a:hover {
            text-decoration: underline;
        }

        /* File upload area (preserved for potential usage) */
        .file-upload-area {
            border: 1px dashed #CBD5E1;
            border-radius: 20px;
            padding: 1rem;
            background: #F8FAFE;
            text-align: center;
            transition: all 0.2s;
            cursor: pointer;
        }

        .file-upload-area:hover {
            border-color: #2C6BFF;
            background: #F0F4FF;
        }

        .file-preview img {
            max-width: 70px;
            border-radius: 12px;
        }

        /* ripple effect (kept from second) */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.5), rgba(255, 255, 255, 0));
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

        /* Responsive */
        @media (max-width: 900px) {
            .auth-card {
                flex-direction: column;
                max-width: 550px;
            }

            .login-hero {
                padding: 2rem 1.5rem;
            }

            .hero-content h1 {
                font-size: 2rem;
            }

            .login-form-section {
                padding: 2rem 1.8rem;
            }

            .sso-buttons {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .sso-buttons {
                flex-direction: column;
            }

            .auth-card {
                margin: 0;
            }
        }
    </style>
    @yield('css')
</head>

<body>

    <div class="particles" id="particles-js"></div>
    <div class="floating-blob blob-1"></div>
    <div class="floating-blob blob-2"></div>

    <div id="toast-container" class="toast-container"></div>

    <!-- Main Card: Corporate Design from first reference, functions preserved from second -->
    <div class="auth-card">
        <!-- Left Hero Section (first design elegance) -->
        <div class="login-hero">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-chart-line"></i></div>
                <div class="brand-text">Stratus<span style="font-weight:400">Core</span></div>
            </div>
            <div class="hero-content">
                <h1>Enterprise<br>Access <span class="hero-highlight">Hub</span></h1>
                <p class="hero-description">Secure, frictionless authentication for modern corporations. Access your
                    dashboard, analytics, and team tools with one trusted identity.</p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-shield-alt"></i> <span>256-bit SSL</span></div>
                    <div class="trust-item"><i class="fas fa-check-circle"></i> <span>SOC 2 Type II</span></div>
                    <div class="trust-item"><i class="fas fa-fingerprint"></i> <span>MFA ready</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“Streamlined corporate identity & access management — trusted by 2,500+ global
                    teams.”</div>
            </div>
        </div>

        <!-- Right Form Section: exact functionality from second code (demo login, SSO, toasts, file preview not needed but preserved globally) -->
        <div class="login-form-section">
            @yield('content')

            {{-- Fallback content (identical to first page's login form but using enhanced toast logic from second) --}}
            @hasSection('content')
            @else
                <div class="form-header">
                    <h2>Welcome back</h2>
                    <p>Sign in to continue to your corporate workspace</p>
                </div>
                <form id="loginForm" action="#" method="post">
                    <div class="input-group">
                        <label class="input-label" for="email">Corporate email</label>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="alex.chen@company.com"
                                autocomplete="email" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label class="input-label" for="password">Password</label>
                        <div class="input-field">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••"
                                autocomplete="current-password" required>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox">
                            <input type="checkbox" id="rememberCheck"> <span>Keep me signed in</span>
                        </label>
                        <a href="#" class="forgot-link" id="forgotPwdLink">Forgot password?</a>
                    </div>
                    <button type="submit" class="login-btn" id="loginBtn"><i
                            class="fas fa-arrow-right-to-bracket"></i> Sign in</button>
                </form>
                <div class="divider"><span>OR CONTINUE WITH</span></div>
                <div class="sso-buttons">
                    <button class="sso-btn" id="ssoGoogleBtn"><i class="fab fa-google"></i> Google</button>
                    <button class="sso-btn" id="ssoOktaBtn"><i class="fas fa-building"></i> SSO (SAML)</button>
                    <button class="sso-btn" id="ssoMsftBtn"><i class="fab fa-microsoft"></i> Entra ID</button>
                </div>
                <div class="register-prompt">Don't have an account? <a href="#" id="signupLink">Request corporate
                        access</a></div>
            @endif
        </div>
    </div>

    {{-- jQuery + Bootstrap (from second code) --}}
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ========== PRESERVED FUNCTIONS FROM SECOND PAGE: Toast, Ripple, Particles, FileUpload logic (intact) ==========
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
            if (duration > 0) setTimeout(() => {
                if (toast.parentNode) removeToast(toast);
            }, duration);
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

        // Ripple effect
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.login-btn, .sso-btn, .file-remove');
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

        // Particles generation (light corporate style)
        function createParticles() {
            const container = document.querySelector('.particles');
            if (!container) return;
            for (let i = 0; i < 60; i++) {
                const particle = document.createElement('div');
                particle.classList.add('particle');
                const size = Math.random() * 4 + 2;
                particle.style.width = size + 'px';
                particle.style.height = size + 'px';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDuration = Math.random() * 12 + 8 + 's';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.opacity = Math.random() * 0.3 + 0.1;
                particle.style.background = `rgba(44, 107, 255, ${Math.random() * 0.3 + 0.15})`;
                container.appendChild(particle);
            }
        }
        createParticles();

        // ========== LOGIC: EXACT FUNCTIONS FROM FIRST PAGE's DEMO plus enhanced toasts (corporate demo) ==========
        function handleLogin(email, password) {
            if (!email || !password) {
                showToast("error", "Authentication error", "Please fill in both email and password", 3500);
                return false;
            }
            if (!email.includes('@') || !email.includes('.')) {
                showToast("error", "Invalid email", "Please enter a valid corporate email address", 3500);
                return false;
            }
            if (password.length < 4) {
                showToast("error", "Access denied", "Invalid credentials. Minimum 4 characters required (demo)", 3500);
                return false;
            }
            const domain = email.split('@')[1] || '';
            if (domain && (domain.includes('company') || domain.includes('corp') || domain.includes('demo') || domain
                    .includes('stratuscorp'))) {
                showToast("success", "Welcome back", `Redirecting ${email.split('@')[0]} to dashboard...`, 3200);
                setTimeout(() => showToast("info", "Demo Mode", "Corporate dashboard access granted (secure session)",
                    2800), 1300);
            } else {
                showToast("success", "Demo corporate login", `Welcome ${email.split('@')[0]}! (simulated secure login)`,
                    3000);
            }
            return true;
        }

        // Bind events if elements exist (fallback form)
        const form = document.getElementById('loginForm');
        if (form) {
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                const emailInput = document.getElementById('email');
                const passwordInput = document.getElementById('password');
                if (emailInput && passwordInput) handleLogin(emailInput.value.trim(), passwordInput.value);
            });
        }
        const forgotLink = document.getElementById('forgotPwdLink');
        if (forgotLink) forgotLink.addEventListener('click', (e) => {
            e.preventDefault();
            showToast("info", "Reset link", "Password reset link sent to your corporate email (demo)", 3500);
        });
        const signupLink = document.getElementById('signupLink');
        if (signupLink) signupLink.addEventListener('click', (e) => {
            e.preventDefault();
            showToast("info", "Access request", "Corporate access request submitted — our IT team will contact you",
                4000);
        });
        const ssoGoogle = document.getElementById('ssoGoogleBtn');
        if (ssoGoogle) ssoGoogle.addEventListener('click', () => {
            showToast("info", "Google SSO", "Redirecting to Google Workspace SSO (corporate auth)", 2500);
            setTimeout(() => showToast("success", "SSO Success", "Welcome back!", 2000), 1200);
        });
        const ssoOkta = document.getElementById('ssoOktaBtn');
        if (ssoOkta) ssoOkta.addEventListener('click', () => {
            showToast("info", "Okta SAML", "Initiating Okta handshake — corporate identity provider", 2500);
            setTimeout(() => showToast("success", "Verified", "Access granted via Okta", 2000), 1200);
        });
        const ssoMsft = document.getElementById('ssoMsftBtn');
        if (ssoMsft) ssoMsft.addEventListener('click', () => {
            showToast("info", "Entra ID", "Connecting to Microsoft Entra ID (Azure AD)", 2500);
            setTimeout(() => showToast("success", "Authenticated", "Secure session established via Entra ID", 2000),
                1200);
        });
        const rememberCheck = document.getElementById('rememberCheck');
        if (rememberCheck) rememberCheck.addEventListener('change', (e) => {
            if (e.target.checked) showToast("info", "Session persistence",
                "Session will be remembered on this device (corporate policy compliant)", 2800);
        });

        // Double-click demo fill (enhance UX)
        const rightPanel = document.querySelector('.login-form-section');
        if (rightPanel) {
            rightPanel.addEventListener('dblclick', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
                const emailField = document.getElementById('email');
                const pwdField = document.getElementById('password');
                if (emailField && pwdField) {
                    emailField.value = 'julie.wong@stratuscorp.com';
                    pwdField.value = 'demo1234';
                    showToast("success", "Demo credentials", "Corporate test account filled", 2000);
                }
            });
        }

        // init file uploads placeholder for completeness (preserved function)
        function initFileUploads() {
            /* no file uploads needed but kept for compatibility */
        }
        document.addEventListener('DOMContentLoaded', () => {
            initFileUploads();
            showToast("info", "🔐 Secure Access", "Corporate-grade encryption active", 3000);
        });
    </script>

    @yield('js')
</body>

</html>
