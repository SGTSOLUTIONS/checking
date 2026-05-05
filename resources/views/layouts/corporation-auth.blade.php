<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Corporation Portal | Property Tax System')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(#F97300, #32012F);
            font-family: 'Inter', 'Poppins', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            background: #F97300;
            filter: blur(120px);
            opacity: 0.2;
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
            background: #524C42;
            filter: blur(140px);
            opacity: 0.25;
            bottom: -100px;
            left: -80px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob2 15s infinite alternate ease-in-out;
        }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            100% { transform: translate(40px, 30px) scale(1.2); opacity: 0.35; }
        }

        @keyframes floatBlob2 {
            0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            100% { transform: translate(-30px, -40px) scale(1.3); opacity: 0.3; }
        }

        .auth-wrapper {
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
            background: #E2DFD0;
            border-radius: 48px;
            overflow: hidden;
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 2;
            animation: fadeInUp 0.8s cubic-bezier(0.2, 0.9, 0.4, 1.1);
        }

        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(40px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .auth-grid {
            display: flex;
            flex-wrap: wrap;
        }

        .left-panel {
            flex: 1.2;
            background: linear-gradient(135deg, #32012F 0%, #1f0a1d 100%);
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
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .right-panel {
            flex: 1;
            background: #E2DFD0;
            padding: 2.5rem 2.5rem;
        }

        .top-emblem {
            background: #E2DFD0;
            padding: 1rem 2rem 0.8rem 2rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 3px solid #F97300;
        }

        .emblem-img {
            width: 70px;
            height: auto;
        }

        .gov-text h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #32012F;
        }

        .gov-text p {
            font-size: 0.85rem;
            margin: 0;
            color: #524C42;
            font-weight: 500;
        }

        .tamil-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #F97300;
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
        }

        .feature-list li i {
            font-size: 1.25rem;
            width: 28px;
            color: #F97300;
        }

        .form-label {
            font-weight: 600;
            color: #32012F;
            font-size: 0.85rem;
        }

        .input-group-text {
            background-color: #fff6eb;
            border-right: none;
            color: #F97300;
            border-color: #d6cfbf;
        }

        .form-control {
            border-left: none;
            padding: 0.75rem;
            font-size: 0.9rem;
            border-color: #d6cfbf;
            background-color: #ffffff;
        }

        .form-control:focus {
            border-color: #F97300;
            box-shadow: 0 0 0 3px rgba(249, 115, 0, 0.2);
        }

        .btn-primary-custom {
            background-color: #F97300;
            border: none;
            padding: 0.8rem;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 60px;
            width: 100%;
            transition: all 0.3s;
            color: white;
        }

        .btn-primary-custom:hover {
            background-color: #e06700;
            transform: translateY(-2px);
        }

        .btn-primary-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #F97300;
            text-decoration: none;
            font-weight: 600;
        }

        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }

        .loader-overlay.active {
            display: flex;
        }

        .spinner-custom {
            width: 60px;
            height: 60px;
            border: 5px solid #E2DFD0;
            border-top: 5px solid #F97300;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast-message {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 10000;
            background: white;
            border-left: 5px solid #F97300;
            border-radius: 14px;
            padding: 14px 20px;
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
            max-width: 340px;
            animation: slideInRight 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(50px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .toast-success {
            border-left-color: #28a745;
        }

        .toast-error {
            border-left-color: #dc3545;
        }

        .strength-meter {
            height: 4px;
            background: #e0d6c3;
            border-radius: 4px;
            margin-top: 8px;
            overflow: hidden;
        }

        .strength-bar {
            width: 0%;
            height: 100%;
            transition: width 0.3s;
        }

        .file-upload-area {
            border: 2px dashed #d6cfbf;
            border-radius: 16px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            background: #fff6eb;
        }

        .file-upload-area:hover {
            border-color: #F97300;
            background: #fff0e0;
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
            border: 3px solid #F97300;
        }

        .file-remove {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 12px;
            margin-top: 5px;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.75rem;
            color: #dc3545;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .auth-grid { flex-direction: column; }
            .left-panel { text-align: center; padding: 2rem 1.5rem; }
            .feature-list li { justify-content: center; }
            .right-panel { padding: 2rem 1.5rem; }
            .gov-text h2 { font-size: 1.1rem; }
        }
    </style>
</head>

<body>

    <div class="loader-overlay" id="loaderOverlay">
        <div class="spinner-custom"></div>
    </div>

    <div class="auth-wrapper">
        <div class="top-emblem">
            <img src="{{ asset('images/TamilNadu_Logo.png') }}" alt="TamilNadu" class="emblem-img" onerror="this.src='https://via.placeholder.com/70x70?text=TN'">
            <div class="gov-text">
                <h2>Tamil Nadu Municipal Corporation</h2>
                <p>Urban Local Body | e-Governance & Citizen Services</p>
                <div class="tamil-text">தமிழ்நாடு மாநகராட்சி | பொது சேவை மையம்</div>
            </div>
        </div>

        <div class="auth-grid">
            <div class="left-panel">
                <h3><i class="fas fa-city me-2" style="font-size: 1.8rem; color: #F97300;"></i> மாநகராட்சி</h3>
                <p>Access property tax, building approvals, trade licenses, water supply, solid waste management, and citizen grievance redressal — all in one place.</p>
                <ul class="feature-list">
                    <li><i class="fas fa-file-invoice-dollar"></i> <span>Property Tax & e-Payment</span></li>
                    <li><i class="fas fa-hard-hat"></i> <span>Building Plan Approval</span></li>
                    <li><i class="fas fa-tint"></i> <span>Water & Sewerage Connection</span></li>
                    <li><i class="fas fa-trash-alt"></i> <span>Solid Waste Management Services</span></li>
                    <li><i class="fas fa-comment-dots"></i> <span>Grievance Redressal (24x7)</span></li>
                </ul>
                <div class="mt-4 pt-2 border-top border-warning opacity-50 small">
                    <span><i class="fas fa-mobile-alt"></i> CMA App Integration</span>
                    <span><i class="fas fa-globe"></i> Smart City Mission</span>
                </div>
                <div class="mt-3">
                    <span class="badge" style="background: rgba(249,115,0,0.2); color:#F97300;"><i class="fas fa-check-circle me-1"></i> Official Municipal Portal</span>
                </div>
            </div>

            <div class="right-panel">
                @yield('form-content')
            </div>
        </div>

        <div class="py-2 text-center border-top" style="background: #E2DFD0; font-size: 0.7rem; color: #524C42;">
            <i class="fas fa-shield-alt"></i> Secure SSL Portal | © {{ date('Y') }} Tamil Nadu Municipal Corporation
        </div>
    </div>

    <!-- Load jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Global toast function
        window.showToast = function(type, title, message, duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `toast-message toast-${type}`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}" style="color: ${type === 'success' ? '#28a745' : '#dc3545'}; font-size: 20px;"></i>
                <div style="flex: 1;">
                    <strong>${title}</strong><br>
                    <small>${message}</small>
                </div>
                <button type="button" class="btn-close btn-sm" style="font-size: 0.65rem;" onclick="this.parentElement.remove()"></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), duration);
        };

        // Global loader functions
        window.showLoader = function() {
            document.getElementById('loaderOverlay').classList.add('active');
        };

        window.hideLoader = function() {
            document.getElementById('loaderOverlay').classList.remove('active');
        };

        // Toggle password visibility
        $(document).on('click', '.toggle-pwd', function() {
            const targetId = $(this).data('target');
            const pwdField = $('#' + targetId);
            const type = pwdField.attr('type') === 'password' ? 'text' : 'password';
            pwdField.attr('type', type);
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });

        // AJAX setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    @stack('scripts')
</body>

</html>
