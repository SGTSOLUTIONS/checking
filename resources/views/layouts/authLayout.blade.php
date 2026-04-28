<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>TN Municipal | Property Tax & Revenue Portal | Drone Survey Integration</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Google Fonts: Inter & Poppins -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <style>
        /* ===== RESET & GLOBAL ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            min-height: 100vh;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 1.5rem;
        }

        /* --- DRONE AERIAL BACKGROUND (UPDATED) --- */
        /* The background now features a stunning drone-shot cityscape / municipal landscape to reflect modern tax administration & smart city monitoring */
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
            filter: brightness(0.45) contrast(1.05) saturate(1.1);
            transform: scale(1.02);
            animation: slowDroneZoom 32s ease infinite alternate;
        }

        @keyframes slowDroneZoom {
            0% {
                transform: scale(1);
            }

            100% {
                transform: scale(1.08);
            }
        }

        /* Dark gradient overlay for readability (matching municipal theme) */
        .bg-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(0, 0, 0, 0.5), rgba(8, 20, 40, 0.65));
            z-index: -1;
        }

        /* Subtle pattern overlay (city grid / drone flight path inspired) */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                linear-gradient(rgba(255, 215, 120, 0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 215, 120, 0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: -1;
        }

        /* Floating "drone" particles: small tech-like elements to suggest UAV survey */
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
            animation: floatDroneParticle linear infinite;
        }

        @keyframes floatDroneParticle {
            0% {
                transform: translateY(120vh) rotate(0deg);
                opacity: 0;
            }

            15% {
                opacity: 0.6;
            }

            85% {
                opacity: 0.3;
            }

            100% {
                transform: translateY(-30vh) rotate(380deg);
                opacity: 0;
            }
        }

        /* ===== TOAST SYSTEM ===== */
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
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.15);
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
        }

        .toast-close:hover {
            color: #1e2f3e;
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

        /* ===== MAIN CARD ===== */
        .auth-card {
            width: 100%;
            max-width: 1280px;
            background: rgba(255, 255, 255, 0.98);
            border-radius: 2rem;
            box-shadow: 0 30px 50px -20px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 215, 120, 0.4);
            display: flex;
            overflow: hidden;
            transition: all 0.3s ease;
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

        /* LEFT SIDE: Municipal Branding + Drone Initiative mention */
        .login-hero {
            flex: 1.2;
            background: linear-gradient(135deg, #f9f3e0 0%, #fff8ed 100%);
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            color: #2c3e4e;
            position: relative;
            overflow: hidden;
        }

        .login-hero::after {
            content: "🚁";
            position: absolute;
            bottom: 20px;
            right: 20px;
            font-size: 85px;
            opacity: 0.08;
            pointer-events: none;
            transform: rotate(-10deg);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 2rem;
            z-index: 2;
        }

        .brand-icon {
            background: #e67e22;
            width: 55px;
            height: 55px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            box-shadow: 0 8px 16px rgba(230, 126, 34, 0.2);
        }

        .brand-text {
            font-size: 1.6rem;
            font-weight: 800;
            letter-spacing: -0.3px;
            color: #2c3e50;
        }

        .brand-sub {
            font-size: 0.8rem;
            font-weight: 500;
            color: #e67e22;
            letter-spacing: 0.5px;
        }

        .hero-content h1 {
            font-size: 2.4rem;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            color: #1e3a5f;
        }

        .hero-highlight {
            color: #e67e22;
            border-bottom: 2px solid #f39c12;
            display: inline-block;
        }

        .hero-description {
            font-size: 0.95rem;
            line-height: 1.5;
            color: #4a627a;
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
            font-size: 0.75rem;
            background: #fef5e8;
            padding: 6px 14px;
            border-radius: 40px;
            color: #b45f1b;
            font-weight: 500;
        }

        .quote-area {
            margin-top: auto;
            padding-top: 2rem;
        }

        .quote {
            font-style: normal;
            font-weight: 500;
            font-size: 0.85rem;
            line-height: 1.4;
            border-left: 3px solid #e67e22;
            padding-left: 1rem;
            color: #5d6f83;
        }

        /* RIGHT SIDE: FORM */
        .login-form-section {
            flex: 1;
            background: white;
            padding: 3rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header h2 {
            font-size: 1.9rem;
            font-weight: 700;
            color: #1e3a5f;
            letter-spacing: -0.3px;
        }

        .form-header p {
            color: #5e7a93;
            margin-top: 6px;
            font-size: 0.9rem;
        }

        .input-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: #2c4c6e;
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
            color: #e67e22;
            font-size: 1.1rem;
        }

        .input-field input {
            width: 100%;
            padding: 14px 16px 14px 46px;
            font-size: 0.95rem;
            border: 1.5px solid #e2e8f0;
            border-radius: 16px;
            background: #ffffff;
            transition: all 0.2s;
            outline: none;
            font-weight: 500;
        }

        .input-field input:focus {
            border-color: #e67e22;
            box-shadow: 0 0 0 4px rgba(230, 126, 34, 0.15);
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
            font-size: 0.85rem;
            color: #2c4c6e;
        }

        .checkbox input {
            accent-color: #e67e22;
            width: 16px;
            height: 16px;
        }

        .forgot-link {
            font-size: 0.85rem;
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
            padding: 14px 0;
            border: none;
            border-radius: 44px;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 12px rgba(230, 126, 34, 0.3);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(230, 126, 34, 0.4);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 1.8rem 0 1.5rem;
            color: #a0b8d0;
            font-size: 0.75rem;
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
            gap: 1rem;
            justify-content: center;
        }

        .sso-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 44px;
            padding: 11px 0;
            font-weight: 600;
            font-size: 0.8rem;
            color: #1e3a5f;
            cursor: pointer;
            transition: all 0.2s;
        }

        .sso-btn:hover {
            background: #fff4e6;
            border-color: #e67e22;
        }

        .register-prompt {
            text-align: center;
            margin-top: 2rem;
            font-size: 0.85rem;
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
            background: radial-gradient(circle, rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0));
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
                font-size: 1.9rem;
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
</head>

<body>

    <!-- DRONE AERIAL BACKGROUND IMAGE: Urban flyover shot representing smart city property tax drone survey -->
    <div class="heritage-bg">
        <img src="https://images.pexels.com/photos/12823145/pexels-photo-12823145.jpeg?auto=compress&cs=tinysrgb&w=1600"
            alt="Drone Aerial View of Greater Chennai Corporation / Urban Landscape - Tamil Nadu Tax Survey"
            onerror="this.src='https://images.pexels.com/photos/2366525/pexels-photo-2366525.jpeg?auto=compress&cs=tinysrgb&w=1600'">
    </div>
    <div class="bg-overlay"></div>
    <div class="particles" id="particles-container"></div>

    <div id="toast-container" class="toast-container"></div>

    <div class="auth-card">
        <!-- LEFT SIDE: Branding with drone-enabled tax administration -->
        <div class="login-hero">
            <div class="brand">
                <div class="brand-icon"><i class="fas fa-drone"></i></div>
                <div>
                    <div class="brand-text">Greater Chennai Corporation</div>
                    <div class="brand-sub">Tamil Nadu • Drone-Assisted Tax Governance</div>
                </div>
            </div>
            <div class="hero-content">
                <h1>Property Tax<br><span class="hero-highlight">Smart Drone Seva</span></h1>
                <p class="hero-description">
                    Official municipal portal enhanced with GIS drone surveys for accurate property assessment, online
                    tax payments, and e-recognition certificates. Government of Tamil Nadu's advanced aerial tax mapping
                    initiative.
                </p>
                <div class="trust-badge">
                    <div class="trust-item"><i class="fas fa-drone"></i> <span>Aerial Survey 2025</span></div>
                    <div class="trust-item"><i class="fas fa-chart-line"></i> <span>Real-time Assessment</span></div>
                    <div class="trust-item"><i class="fas fa-hand-holding-usd"></i> <span>Online Collection</span></div>
                </div>
            </div>
            <div class="quote-area">
                <div class="quote">“Drone-enabled transparency — revolutionizing urban property tax for Viksit Tamil
                    Nadu” — Drone Commissionerate, Municipal Admin</div>
            </div>
        </div>

        <!-- RIGHT SIDE: Login panel -->
        <div class="login-form-section">
            <div class="form-header">
                <h2>Taxpayer Access</h2>
                <p>Sign in to view drone-survey property data, file returns & download e-receipts</p>
            </div>
            <form id="loginForm" action="#" method="post">
                <div class="input-group">
                    <label class="input-label" for="email">Registered Mobile / Email (Tamil Nadu Drone ID)</label>
                    <div class="input-field">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" placeholder="taxpayer@tn.gov.in"
                            autocomplete="email" required>
                    </div>
                </div>
                <div class="input-group">
                    <label class="input-label" for="password">Tax Portal Password / OTP PIN</label>
                    <div class="input-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••"
                            autocomplete="current-password" required>
                    </div>
                </div>
                <div class="form-options">
                    <label class="checkbox">
                        <input type="checkbox" id="rememberCheck"> <span>Keep me signed in (secured device)</span>
                    </label>
                    <a href="#" class="forgot-link" id="forgotPwdLink">Forgot password / PID?</a>
                </div>
                <button type="submit" class="login-btn" id="loginBtn"><i class="fas fa-drone"></i> Access Drone Tax
                    Dashboard</button>
            </form>
            <div class="divider"><span>OR SIGN IN WITH</span></div>
            <div class="sso-buttons">
                <button class="sso-btn" id="ssoGoogleBtn"><i class="fab fa-google"></i> TN e-Seva</button>
                <button class="sso-btn" id="ssoOktaBtn"><i class="fas fa-building"></i> UMANG SSO</button>
                <button class="sso-btn" id="ssoMsftBtn"><i class="fas fa-database"></i> DigiLocker</button>
            </div>
            <div class="register-prompt">New taxpayer? <a href="#" id="signupLink">Register property for
                    drone-based tax recognition</a></div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // ========== TOAST SYSTEM ==========
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

        // Ripple effect for buttons
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
                ripple.style.background = 'radial-gradient(circle, rgba(230,126,34,0.5), rgba(230,126,34,0))';
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

        // Generate particles that simulate drone dust / aerial tech glow
        function createParticles() {
            const container = document.querySelector('.particles');
            if (!container) return;
            for (let i = 0; i < 70; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                p.style.width = (Math.random() * 5 + 2) + 'px';
                p.style.height = p.style.width;
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDuration = Math.random() * 14 + 8 + 's';
                p.style.animationDelay = Math.random() * 15 + 's';
                p.style.opacity = Math.random() * 0.35 + 0.1;
                p.style.background = `radial-gradient(circle, rgba(230, 126, 34, 0.7), rgba(255, 200, 100, 0.3))`;
                container.appendChild(p);
            }
        }
        createParticles();

        // DRONE THEMED LOGIC: handles login and showcases drone tax survey features
        function handleDroneTaxLogin(email, password) {
            if (!email || !password) {
                showToast("error", "Drone Authentication Required",
                    "Please enter registered email & password for property tax portal", 3800);
                return false;
            }
            if (!email.includes('@') || !email.includes('.')) {
                showToast("error", "Invalid Taxpayer ID", "Enter valid email / mobile linked with drone survey record",
                    3500);
                return false;
            }
            if (password.length < 4) {
                showToast("error", "Access Denied",
                    "Invalid credentials (minimum 4 characters for demo). Use demo drone account.", 3500);
                return false;
            }
            // Smart detection: tn.gov, municipality, or just any email simulates success with drone dataset
            const userName = email.split('@')[0];
            showToast("success", "🚁 Drone Taxpayer Verified",
                `Welcome ${userName}, property tax dashboard with aerial GIS data loading...`, 3800);
            setTimeout(() => {
                showToast("info", "Aerial Survey 2025",
                    "Your property has been mapped via drone imaging. PTIN: TN-DRONE-9842 | Last tax: ₹5,420",
                    4500);
            }, 1500);
            setTimeout(() => {
                showToast("success", "e-Recognition",
                    "New drone-based property certificate available for download.", 3800);
            }, 3200);
            return true;
        }

        // Bind login event
        const loginForm = document.getElementById('loginForm');
        if (loginForm) {
            loginForm.addEventListener('submit', (e) => {
                e.preventDefault();
                const email = document.getElementById('email')?.value.trim();
                const pwd = document.getElementById('password')?.value;
                if (email && pwd) handleDroneTaxLogin(email, pwd);
                else showToast("warning", "Fields Missing",
                    "Please enter both credentials to access smart tax portal", 2800);
            });
        }

        // Demo quick-fill: double click on right panel for demo credentials
        const rightPanel = document.querySelector('.login-form-section');
        if (rightPanel) {
            rightPanel.addEventListener('dblclick', (e) => {
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'BUTTON') return;
                const emailField = document.getElementById('email');
                const pwdField = document.getElementById('password');
                if (emailField && pwdField) {
                    emailField.value = 'drone.taxpayer@tn.gov.in';
                    pwdField.value = 'TNdrone2025';
                    showToast("success", "Demo Drone Credentials",
                        "Sample taxpayer account with drone property survey data loaded.", 2600);
                }
            });
        }

        // Forgot password link
        document.getElementById('forgotPwdLink')?.addEventListener('click', (e) => {
            e.preventDefault();
            showToast("info", "Drone ID Recovery",
                "Reset link sent to registered mobile (Tamil Nadu Drone Registry)", 4000);
        });
        // Signup / register
        document.getElementById('signupLink')?.addEventListener('click', (e) => {
            e.preventDefault();
            showToast("info", "Drone-Assisted Registration",
                "New property registration with aerial survey initiated. e-KYC via UMANG link shared.", 4800);
        });
        // SSO Buttons with relevant drone/municipal integration
        document.getElementById('ssoGoogleBtn')?.addEventListener('click', () => {
            showToast("info", "TN e-Seva", "Redirecting to Tamil Nadu Single Sign-On (Drone data integration)",
                2800);
            setTimeout(() => showToast("success", "SSO Aerial Verified", "Taxpayer property polygons fetched",
                2400), 1400);
        });
        document.getElementById('ssoOktaBtn')?.addEventListener('click', () => {
            showToast("info", "UMANG Platform", "Connecting to Unified Mobile App with drone survey module", 2600);
            setTimeout(() => showToast("success", "Verified", "Drone assessment summary available", 2200), 1300);
        });
        document.getElementById('ssoMsftBtn')?.addEventListener('click', () => {
            showToast("info", "DigiLocker", "Authenticating via DigiLocker issued property documents", 2800);
            setTimeout(() => showToast("success", "Authorized", "Tax certificates & drone orthophoto accessible",
                2300), 1300);
        });

        // Remember me message
        const rememberCheck = document.getElementById('rememberCheck');
        if (rememberCheck) {
            rememberCheck.addEventListener('change', (e) => {
                if (e.target.checked) showToast("info", "Session persistence",
                    "Secured cookie enabled for 30 days. Drone preferences saved.", 3000);
            });
        }

        // Welcome message on load: highlighting drone tech
        document.addEventListener('DOMContentLoaded', () => {
            showToast("info", "🚁 Drone-Enabled Tax Portal",
                "Welcome to Tamil Nadu's smart property tax system | Aerial Surveys for accurate billing", 4500);
            // Extra drone interactivity: show badge imagery
            console.log("Drone background integration active: Greater Chennai Corporation - Aerial Tax Governance");
        });
    </script>
</body>

</html>
