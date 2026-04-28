<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>AeroCore | Secure Drone Command Access</title>

    {{-- Bootstrap 5 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    {{-- Google Fonts: Inter & Plus Jakarta Sans --}}
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
        /* ===== RESET & GLOBAL ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        /* FULLSCREEN DRONE BACKGROUND with low brightness overlay */
        .drone-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            overflow: hidden;
        }

        .drone-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.35) contrast(1.1);
            transform: scale(1.02);
            animation: slowZoom 25s ease infinite alternate;
        }

        @keyframes slowZoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.08);
            }
        }

        /* Dark gradient overlay for depth and readability */
        .drone-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(0, 0, 0, 0.55), rgba(0, 0, 0, 0.85));
            z-index: -1;
        }

        /* Sophisticated pattern overlay (subtle tech grid) */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(0, 255, 255, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(0, 255, 255, 0.04) 1px, transparent 1px);
            background-size: 45px 45px;
            pointer-events: none;
            z-index: -1;
        }

        /* floating particles - subtle tech ambiance */
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
            background: rgba(0, 255, 255, 0.25);
            border-radius: 50%;
            pointer-events: none;
            animation: floatParticle linear infinite;
            box-shadow: 0 0 4px rgba(0, 255, 255, 0.5);
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
                opacity: 0.4;
            }

            100% {
                transform: translateY(-20vh) rotate(360deg);
                opacity: 0;
            }
        }

        /* ===== TOAST SYSTEM (premium glass) ===== */
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
            background: rgba(10, 20, 30, 0.92);
            backdrop-filter: blur(16px);
            border-radius: 20px;
            border-left: 4px solid;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45), 0 0 0 1px rgba(0, 255, 255, 0.2);
            padding: 0.9rem 1rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            transform: translateX(120%);
            opacity: 0;
            transition: transform 0.4s cubic-bezier(0.34, 1.2, 0.64, 1), opacity 0.3s ease;
            position: relative;
            overflow: hidden;
            color: #eef5ff;
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
            opacity: 0.85;
            margin: 0;
        }

        .toast-close {
            background: none;
            border: none;
            color: #8aa2c0;
            cursor: pointer;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }

        .toast-close:hover {
            color: #0ff;
            transform: scale(1.05);
        }

        .toast-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 3px;
            background: linear-gradient(90deg, #0ff, #00aaff);
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
            border-left-color: #0ff;
        }

        /* ===== MAIN CARD: Dark-glass tactical design ===== */
        .auth-card {
            width: 100%;
            max-width: 1280px;
            background: rgba(6, 16, 28, 0.65);
            backdrop-filter: blur(18px);
            border-radius: 2.5rem;
            box-shadow: 0 30px 50px -15px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(0, 255, 255, 0.2);
            display: flex;
            overflow: hidden;
            transition: all 0.3s ease;
            z-index: 10;
            animation: fadeSlideUp 0.6s ease-out;
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

        /* LEFT SIDE: Drone/Aerospace Branding */
        .login-hero {
            flex: 1.2;
            background: linear-gradient(125deg, rgba(0, 20, 40, 0.85) 0%, rgba(0, 35, 55, 0.9) 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: white;
            position: relative;
            overflow: hidden;
            backdrop-filter: blur(4px);
        }

        .login-hero::after {
            content: "⚡";
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 80px;
            opacity: 0.08;
            pointer-events: none;
            font-family: monospace;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 2.5rem;
            z-index: 2;
        }

        .brand-icon {
            background: rgba(0, 255, 255, 0.2);
            backdrop-filter: blur(6px);
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            box-shadow: 0 0 15px rgba(0, 255, 255, 0.3);
            border: 1px solid rgba(0, 255, 255, 0.4);
        }

        .brand-text {
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.5px;
            background: linear-gradient(135deg, #fff, #88ddff);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .hero-content {
            z-index: 2;
        }

        .hero-content h1 {
            font-size: 2.6rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.2rem;
            letter-spacing: -0.02em;
        }

        .hero-highlight {
            color: #0ff;
            text-shadow: 0 0 8px rgba(0, 255, 255, 0.5);
            border-bottom: 2px solid #0ff;
        }

        .hero-description {
            font-size: 1rem;
            line-height: 1.5;
            opacity: 0.85;
            max-width: 90%;
            margin-bottom: 2rem;
        }

        .trust-badge {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            flex-wrap: wrap;
        }

        .trust-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
            background: rgba(0, 255, 255, 0.12);
            padding: 6px 14px;
            border-radius: 40px;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(0, 255, 255, 0.25);
        }

        .quote-area {
            margin-top: auto;
            padding-top: 2rem;
        }

        .quote {
            font-style: normal;
            font-weight: 500;
            font-size: 0.9rem;
            line-height: 1.4;
            border-left: 3px solid #0ff;
            padding-left: 1rem;
            opacity: 0.9;
        }

        /* RIGHT SIDE: FORM SECTION (corporate command center style) */
        .login-form-section {
            flex: 1;
            background: rgba(2, 12, 22, 0.7);
            backdrop-filter: blur(12px);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header h2 {
            font-size: 2rem;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: -0.3px;
        }

        .form-header p {
            color: #9ab3d0;
            margin-top: 8px;
            font-size: 0.95rem;
        }

        .input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #ccdeff;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-field {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-field i {
            position: absolute;
            left: 16px;
            color: #4a9eff;
            font-size: 1.1rem;
            transition: color 0.2s;
        }

        .input-field input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            border: 1.5px solid rgba(0, 150, 255, 0.4);
            border-radius: 16px;
            background: rgba(2, 20, 35, 0.7);
            transition: all 0.2s ease;
            outline: none;
            color: #f0f9ff;
            font-weight: 500;
        }

        .input-field input:focus {
            border-color: #0ff;
            box-shadow: 0 0 0 4px rgba(0, 255, 255, 0.2);
            background: rgba(5, 25, 45, 0.9);
        }

        .input-field input::placeholder {
            color: #6a8bb0;
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
            color: #bdd4ff;
        }

        .checkbox input {
            width: 16px;
            height: 16px;
            cursor: pointer;
            accent-color: #0ff;
        }

        .forgot-link {
            font-size: 0.85rem;
            font-weight: 500;
            color: #0ff;
            text-decoration: none;
        }

        .forgot-link:hover {
            color: #88f0ff;
            text-decoration: underline;
        }

        .login-btn {
            background: linear-gradient(95deg, #004466, #0088aa, #00ccff);
            background-size: 200% auto;
            color: white;
            width: 100%;
            padding: 14px 0;
            border: none;
            border-radius: 40px;
            font-weight: 700;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 6px 18px rgba(0, 160, 255, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            background-position: right center;
            box-shadow: 0 12px 24px -8px rgba(0, 200, 255, 0.5);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.8rem 0 1.5rem;
            color: #6c8db0;
            font-size: 0.8rem;
        }

        .divider::before,
        .divider::after {
            content: "";
            flex: 1;
            border-bottom: 1px solid rgba(0, 150, 255, 0.3);
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
            background: rgba(10, 40, 60, 0.7);
            border: 1px solid rgba(0, 200, 255, 0.4);
            border-radius: 44px;
            padding: 11px 0;
            font-weight: 600;
            font-size: 0.85rem;
            color: #e0f0ff;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sso-btn:hover {
            background: rgba(0, 120, 180, 0.6);
            border-color: #0ff;
            box-shadow: 0 0 12px rgba(0, 255, 255, 0.2);
        }

        .register-prompt {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
            color: #9ab3cf;
        }

        .register-prompt a {
            color: #0ff;
            font-weight: 700;
            text-decoration: none;
        }

        .register-prompt a:hover {
            text-decoration: underline;
        }

        /* Ripple effect */
        .ripple-effect {
            position: absolute;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0, 255, 255, 0.6), rgba(0, 255, 255, 0));
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
                padding: 2rem 1.5rem;
            }
        }

        @media (max-width: 480px) {
            .sso-buttons {
                flex-direction: column;
            }
        }
    </style>
    @yield('css')
</head>

<body>

    <!-- Fullscreen Drone Background (low brightness) -->
    <div class="drone-bg">
        <img src="https://picsum.photos/id/96/1920/1080" alt="Drone fleet surveillance background"
            onerror="this.src='https://images.pexels.com/photos/4425877/pexels-photo-4425877.jpeg?auto=compress&cs=tinysrgb&w=1600'">
    </div>
    <div class="drone-overlay"></div>
    <div class="particles" id="particles-container"></div>

    <div id="toast-container" class="toast-container"></div>

    <div class="auth-card">
        <!-- Left Side: Aerospace & Defense Branding -->
        <div class="login-hero">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-drone"></i></div>
                <div class="brand-text">AEROCORE<span style="font-weight:500; font-size:1rem;"> | UAS</span></div>
            </div>
            <div class="hero-content">
                <h1>Drone<br>Command <span class="hero-highlight">Access</span></h1>
                <p class="hero-description">
                    Next-gen fleet management & autonomous mission control. Secure authentication for defense-grade
                    drone operations.
                </p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-shield-virus"></i> <span>Quantum Secure</span></div>
                    <div class="trust-item"><i class="fas fa-satellite-dish"></i> <span>Military Grade</span></div>
                    <div class="trust-item"><i class="fas fa-fingerprint"></i> <span>Bio-Metric Ready</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“Trusted by 300+ global defense & logistics operators — real-time drone
                    orchestration.”</div>
            </div>
        </div>

        <!-- Right Side: Access Panel (modern) -->
        <div class="login-form-section">
            @yield('content')
            @hasSection('content')
            @else
                <div class="form-header">
                    <h2>Operator access</h2>
                    <p>Authenticate to command drone fleet & intelligence hub</p>
                </div>
                <form id="loginForm" action="#" method="post">
                    <div class="input-group">
                        <label class="input-label" for="email">Tactical email / ID</label>
                        <div class="input-field">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" placeholder="pilot.call@aerocore.com"
                                autocomplete="email" required>
                        </div>
                    </div>
                    <div class="input-group">
                        <label class="input-label" for="password">Secure access key</label>
                        <div class="input-field">
                            <i class="fas fa-key"></i>
                            <input type="password" id="password" name="password" placeholder="••••••••"
                                autocomplete="current-password" required>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox">
                            <input type="checkbox" id="rememberCheck"> <span>Trust this command terminal</span>
                        </label>
                        <a href="#" class="forgot-link" id="forgotPwdLink">Regen access token?</a>
                    </div>
                    <button type="submit" class="login-btn" id="loginBtn"><i class="fas fa-drone"></i> Authenticate &
                        Launch</button>
                </form>
                <div class="divider"><span>OR FEDERATED SSO</span></div>
                <div class="sso-buttons">
                    <button class="sso-btn" id="ssoGoogleBtn"><i class="fab fa-google"></i> Gov Cloud</button>
                    <button class="sso-btn" id="ssoOktaBtn"><i class="fas fa-building"></i> DOD SSO</button>
                    <button class="sso-btn" id="ssoMsftBtn"><i class="fab fa-microsoft"></i> Azure FedRAMP</button>
                </div>
                <div class="register-prompt">New mission profile? <a href="#" id="signupLink">Request security
                        clearance</a></div>
            @endif
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ========== TOAST SYSTEM (preserved & upgraded) ==========
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
            return str.replace(/[&<>]/g, (m) => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;'
            } [m]));
        }

        // Ripple effect
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.login-btn, .sso-btn');
            if (btn) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple-effect');
                const rect = btn.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                ripple.style.position = 'absolute';
                ripple.style.background = 'radial-gradient(circle, rgba(0,255,255,0.5), rgba(0,255,255,0))';
                ripple.style.borderRadius = '50%';
                ripple.style.pointerEvents = 'none';
                ripple.style.transform = 'scale(0)';
                ripple.style.transition = 'transform 0.4s, opacity 0.6s';
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

        // Particles creation (drone-ish)
        function createTechParticles() {
            const container = document.querySelector('.particles');
            if (!container) return;
            for (let i = 0; i < 80; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                const size = Math.random() * 4 + 1.5;
                p.style.width = size + 'px';
                p.style.height = size + 'px';
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = Math.random() * 14 + 7 + 's';
                p.style.animationDelay = Math.random() * 15 + 's';
                p.style.opacity = Math.random() * 0.4 + 0.1;
                p.style.background = `rgba(0, 200, 255, ${Math.random() * 0.5 + 0.2})`;
                container.appendChild(p);
            }
        }
        createTechParticles();

        // LOGIC: AEROCORE authentication simulation
        function handleLogin(email, password) {
            if (!email || !password) {
                showToast("error", "Authentication failure", "Fill all security fields", 3500);
                return false;
            }
            if (!email.includes('@') || !email.includes('.')) {
                showToast("error", "Invalid callsign", "Enter valid tactical email", 3500);
                return false;
            }
            if (password.length < 4) {
                showToast("error", "Access denied", "Security token too short (demo requires 4+ chars)", 3500);
                return false;
            }
            const domain = email.split('@')[1] || '';
            if (domain && (domain.includes('aerocore') || domain.includes('drone') || domain.includes('tactical') || domain
                    .includes('defense'))) {
                showToast("success", "🔐 Command Authorized", `Welcome ${email.split('@')[0]}, drone telemetry loading...`,
                    3200);
                setTimeout(() => showToast("info", "Live Feed", "Fleet status: ACTIVE | 4 drones online", 2800), 1300);
            } else {
                showToast("success", "✅ Secure Entry", `Operator ${email.split('@')[0]}, demo mission ready.`, 3000);
            }
            return true;
        }

        const form = document.getElementById('loginForm');
        if (form) form.addEventListener('submit', (e) => {
            e.preventDefault();
            const email = document.getElementById('email')?.value.trim();
            const pwd = document.getElementById('password')?.value;
            if (email && pwd) handleLogin(email, pwd);
        });

        document.getElementById('forgotPwdLink')?.addEventListener('click', (e) => {
            e.preventDefault();
            showToast("info", "Token reset", "Recovery link sent to encrypted channel (demo)", 3500);
        });
        document.getElementById('signupLink')?.addEventListener('click', (e) => {
            e.preventDefault();
            showToast("info", "Clearance request", "Security team will verify your credentials", 4000);
        });
        document.getElementById('ssoGoogleBtn')?.addEventListener('click', () => {
            showToast("info", "Gov Cloud SSO", "Redirect to secure identity provider", 2500);
            setTimeout(() => showToast("success", "SSO handshake", "Access granted: Joint forces portal", 2000),
                1200);
        });
        document.getElementById('ssoOktaBtn')?.addEventListener('click', () => {
            showToast("info", "DOD SSO", "Initiating SAML with PKI", 2500);
            setTimeout(() => showToast("success", "Verified", "Mission dashboard unlocked", 2000), 1200);
        });
        document.getElementById('ssoMsftBtn')?.addEventListener('click', () => {
            showToast("info", "Azure FedRAMP", "Connecting to entra ID", 2500);
            setTimeout(() => showToast("success", "Session active", "Drone telemetry ready", 2000), 1200);
        });
        document.getElementById('rememberCheck')?.addEventListener('change', (e) => {
            if (e.target.checked) showToast("info", "Terminal trust", "Device registered for 30 days (encrypted)",
                2800);
        });

        const rightPanel = document.querySelector('.login-form-section');
        if (rightPanel) rightPanel.addEventListener('dblclick', (e) => {
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
            const emailF = document.getElementById('email');
            const pwdF = document.getElementById('password');
            if (emailF && pwdF) {
                emailF.value = 'commander@aerocore.com';
                pwdF.value = 'droneOps2025';
                showToast("success", "Demo profile", "Tactical operator credentials injected", 1800);
            }
        });

        document.addEventListener('DOMContentLoaded', () => {
            showToast("info", "🛸 AeroCore UAS", "Quantum-encrypted channel established", 3200);
        });
    </script>
    @yield('js')
</body>

</html>
