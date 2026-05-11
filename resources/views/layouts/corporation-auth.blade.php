<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SRIS | Spatial Revenue Intelligent System')</title>

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
            background: linear-gradient(145deg, #0B3B3F 0%, #1A2A4F 100%);
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
            width: 400px;
            height: 400px;
            background: #2B7A6E;
            filter: blur(140px);
            opacity: 0.25;
            top: -150px;
            right: -80px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob 14s infinite alternate ease-in-out;
        }

        body::after {
            content: "";
            position: absolute;
            width: 500px;
            height: 500px;
            background: #4A6FA5;
            filter: blur(150px);
            opacity: 0.2;
            bottom: -120px;
            left: -100px;
            border-radius: 50%;
            z-index: 0;
            animation: floatBlob2 18s infinite alternate ease-in-out;
        }

        @keyframes floatBlob {
            0% { transform: translate(0, 0) scale(1); opacity: 0.2; }
            100% { transform: translate(30px, 40px) scale(1.25); opacity: 0.35; }
        }

        @keyframes floatBlob2 {
            0% { transform: translate(0, 0) scale(1); opacity: 0.15; }
            100% { transform: translate(-40px, -30px) scale(1.35); opacity: 0.3; }
        }

        .auth-wrapper {
            max-width: 1300px;
            width: 100%;
            margin: 0 auto;
            background: #F8FAF0;
            border-radius: 56px;
            overflow: hidden;
            box-shadow: 0 35px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.2);
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
            background: linear-gradient(135deg, #0F2C3D 0%, #1D3E53 100%);
            padding: 2.8rem 2.2rem;
            color: #FEF9E6;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .left-panel::after {
            content: "🗺️";
            font-size: 280px;
            position: absolute;
            bottom: -50px;
            right: -50px;
            opacity: 0.07;
            pointer-events: none;
            animation: spinSlow 35s infinite linear;
        }

        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .right-panel {
            flex: 1;
            background: #F8FAF0;
            padding: 2.5rem 2.5rem;
        }

        .top-emblem {
            background: #F8FAF0;
            padding: 1rem 2rem 0.8rem 2rem;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
            border-bottom: 3px solid #C49A6C;
        }

        .emblem-img {
            width: 70px;
            height: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        .gov-text h2 {
            font-size: 1.5rem;
            font-weight: 700;
            margin: 0;
            color: #1D4E5F;
        }

        .gov-text p {
            font-size: 0.85rem;
            margin: 0;
            color: #5B6E5C;
            font-weight: 500;
        }

        .tamil-text {
            font-size: 0.8rem;
            font-weight: 600;
            color: #C49A6C;
            letter-spacing: 0.3px;
        }

        .left-panel h3 {
            font-weight: 800;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: #F8E3C2;
        }

        .left-panel .subtitle {
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            opacity: 0.9;
            line-height: 1.5;
        }

        .sris-description {
            background: rgba(255,255,255,0.08);
            padding: 1.2rem;
            border-radius: 20px;
            margin: 1rem 0;
            border-left: 4px solid #E0B574;
        }

        .sris-description p {
            font-size: 0.85rem;
            line-height: 1.5;
            margin-bottom: 0.8rem;
        }

        .feature-list {
            list-style: none;
            padding: 0;
            margin-top: 1rem;
        }

        .feature-list li {
            margin-bottom: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            font-size: 0.85rem;
            color: #E7F0E5;
        }

        .feature-list li i {
            font-size: 1.1rem;
            width: 24px;
            color: #E0B574;
        }

        .form-label {
            font-weight: 600;
            color: #1F4E5F;
            font-size: 0.85rem;
            letter-spacing: -0.2px;
        }

        .input-group-text {
            background-color: #FEF6E8;
            border-right: none;
            color: #C49A6C;
            border-color: #DCD3C0;
        }

        .form-control {
            border-left: none;
            padding: 0.75rem;
            font-size: 0.9rem;
            border-color: #DCD3C0;
            background-color: #FFFFFF;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #C49A6C;
            box-shadow: 0 0 0 3px rgba(196, 154, 108, 0.25);
        }

        .btn-primary-custom {
            background-color: #1C6E6B;
            border: none;
            padding: 0.8rem;
            font-weight: 700;
            font-size: 1rem;
            border-radius: 60px;
            width: 100%;
            transition: all 0.3s;
            color: white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }

        .btn-primary-custom:hover {
            background-color: #C49A6C;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .btn-primary-custom:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            background-color: #7D9B94;
        }

        .forgot-link {
            font-size: 0.8rem;
            color: #1C6E6B;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #C49A6C;
            text-decoration: underline;
        }

        .loader-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(11, 59, 63, 0.75);
            backdrop-filter: blur(6px);
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
            border: 5px solid #E3EBD9;
            border-top: 5px solid #C49A6C;
            border-right: 5px solid #1C6E6B;
            border-radius: 50%;
            animation: spin 0.9s cubic-bezier(0.68, -0.55, 0.265, 1.55) infinite;
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
            background: #FFFFFF;
            border-left: 5px solid #1C6E6B;
            border-radius: 20px;
            padding: 14px 20px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
            max-width: 350px;
            animation: slideInRight 0.3s ease-out;
            display: flex;
            align-items: center;
            gap: 12px;
            font-family: 'Inter', sans-serif;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(60px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .toast-success {
            border-left-color: #2E7D64;
            background: #F0F9F0;
        }

        .toast-error {
            border-left-color: #D95B5B;
            background: #FFF5F5;
        }

        .invalid-feedback {
            display: block;
            font-size: 0.75rem;
            color: #D95B5B;
            margin-top: 5px;
        }

        @media (max-width: 768px) {
            .auth-grid { flex-direction: column; }
            .left-panel { text-align: center; padding: 2rem 1.5rem; }
            .feature-list li { justify-content: center; }
            .right-panel { padding: 2rem 1.5rem; }
            .gov-text h2 { font-size: 1.1rem; }
            .top-emblem { justify-content: center; text-align: center; }
            .sris-description { text-align: left; }
        }

        .btn-close-custom {
            background: transparent;
            border: none;
            font-size: 0.8rem;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        .btn-close-custom:hover { opacity: 1; }

        .brand-icon-highlight {
            background: rgba(28, 110, 107, 0.12);
            border-radius: 40px;
            padding: 2px 8px;
        }

        .stats-badge {
            background: rgba(196,154,108,0.2);
            border-radius: 30px;
            padding: 6px 14px;
            font-size: 0.7rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>
    <div class="loader-overlay" id="loaderOverlay">
        <div class="spinner-custom"></div>
    </div>

    <div class="auth-wrapper">
        <div class="top-emblem">
            <img src="{{ asset('images/TamilNadu_Logo.png') }}" alt="Tamil Nadu" class="emblem-img" onerror="this.src='https://via.placeholder.com/70x70?text=TN'">
            <div class="gov-text">
                <h2>Spatial Revenue Intelligent System (SRIS)</h2>
                <p>GIS-Enabled Municipal Property Management & Revenue Intelligence Platform</p>
                <div class="tamil-text">இடஞ்சார் வருவாய் அறிவு முறை | ஸ்மார்ட் நகராட்சி சேவைகள்</div>
            </div>
        </div>

        <div class="auth-grid">
            <div class="left-panel">
                <h3><i class="fas fa-map-marked-alt me-2" style="font-size: 1.8rem; color: #E0B574;"></i> SRIS</h3>
                <div class="subtitle">Spatial Revenue Intelligent System</div>

                <div class="sris-description">
                    <p><strong>Spatial Revenue Intelligent System (SRIS)</strong> is a smart digital Web GIS platform developed for efficient mapping and management of municipal properties. It enables real-time visualization, monitoring, and spatial analysis of assets such as buildings, roads, water connections, and tax properties through an interactive map-based system.</p>
                    <p>The application supports local bodies in:</p>
                </div>

                <ul class="feature-list">
                    <li><i class="fas fa-chart-line"></i> <span>Improving property administration</span></li>
                    <li><i class="fas fa-rupee-sign"></i> <span>Revenue generation</span></li>
                    <li><i class="fas fa-city"></i> <span>Urban planning</span></li>
                    <li><i class="fas fa-brain"></i> <span>Decision-making through accurate GIS-enabled spatial data</span></li>
                </ul>

                <div class="mt-3 d-flex flex-wrap gap-2 justify-content-start">
                    <span class="stats-badge"><i class="fas fa-building"></i> 2.5M+ Properties</span>
                    <span class="stats-badge"><i class="fas fa-map"></i> 100% GIS Coverage</span>
                    <span class="stats-badge"><i class="fas fa-cloud-upload-alt"></i> Real-time Sync</span>
                </div>

                <div class="mt-3 pt-2 border-top border-light opacity-50 small">
                    <span><i class="fas fa-mobile-alt"></i> Mobile GIS App</span>
                    <span class="ms-3"><i class="fas fa-globe"></i> Smart City Mission</span>
                    <span class="ms-3"><i class="fas fa-chart-pie"></i> Revenue Analytics</span>
                </div>
                <div class="mt-2">
                    <span class="badge" style="background: rgba(196,154,108,0.25); color:#F8E3C2;">
                        <i class="fas fa-check-circle me-1"></i> Powered by Web GIS Technology
                    </span>
                </div>
            </div>

            <div class="right-panel">
                @yield('form-content')
            </div>
        </div>

        <div class="py-2 text-center border-top" style="background: #F8FAF0; font-size: 0.7rem; color: #686F64;">
            <i class="fas fa-map-marked-alt"></i> SRIS v2.0 | GIS-Enabled Municipal Platform | © {{ date('Y') }} Spatial Revenue Intelligent System
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.showToast = function(type, title, message, duration = 4000) {
            const toast = document.createElement('div');
            toast.className = `toast-message toast-${type}`;
            toast.innerHTML = `
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'}"
                   style="color: ${type === 'success' ? '#2E7D64' : '#D95B5B'}; font-size: 20px;"></i>
                <div style="flex: 1;">
                    <strong style="color: #1A3B3A;">${title}</strong><br>
                    <small style="color: #52635F;">${message}</small>
                </div>
                <button type="button" class="btn-close-custom" style="font-size: 0.65rem;"
                        onclick="this.parentElement.remove()"><i class="fas fa-times"></i></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                }
            }, duration);
        };

        window.showLoader = function() {
            document.getElementById('loaderOverlay').classList.add('active');
        };

        window.hideLoader = function() {
            document.getElementById('loaderOverlay').classList.remove('active');
        };

        $(document).on('click', '.toggle-pwd', function() {
            const targetId = $(this).data('target');
            const pwdField = $('#' + targetId);
            const type = pwdField.attr('type') === 'password' ? 'text' : 'password';
            pwdField.attr('type', type);
            $(this).find('i').toggleClass('fa-eye-slash fa-eye');
        });

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
