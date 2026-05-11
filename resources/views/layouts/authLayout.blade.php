<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="sris-demo-token">
    <title>SRIS | Spatial Revenue Intelligent System - Surveyor Dashboard</title>

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
            background-color: #f4f2ef2d;
            position: relative;
            overflow-x: hidden;
            padding: 0;
        }

        /* Heritage Background */
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
            transform: scale(1.02);
            animation: slowZoom 22s ease infinite alternate;
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
            background: rgba(0, 0, 0, 0.2);
            z-index: -1;
        }

        /* Particles */
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

        /* Toast Container */
        .toast-container {
            position: fixed;
            bottom: 1.5rem;
            right: 1rem;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
            max-width: 380px;
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
        }

        .toast.show {
            transform: translateX(0);
            opacity: 1;
        }

        .toast-success {
            border-left-color: #27ae60;
        }

        .toast-error {
            border-left-color: #e74c3c;
        }

        .toast-info {
            border-left-color: #2980b9;
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
        }

        .toast-message {
            font-size: 0.75rem;
            opacity: 0.8;
        }

        .toast-close {
            background: none;
            border: none;
            color: #7e8b9e;
            cursor: pointer;
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

        /* DASHBOARD LAYOUT */
        .dashboard-wrapper {
            max-width: 1600px;
            margin: 1rem auto;
            padding: 0 1rem;
            position: relative;
            z-index: 10;
        }

        /* Top Navbar */
        .sris-navbar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(12px);
            border-radius: 2rem;
            padding: 0.75rem 1.8rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
        }

        .sris-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            background: #e67e22;
            width: 42px;
            height: 42px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.2rem;
            color: #1e3a5f;
        }

        .brand-sub {
            font-size: 0.65rem;
            font-weight: 600;
            color: #e67e22;
        }

        .surveyor-badge {
            background: #fef5e8;
            padding: 6px 15px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        /* Main grid */
        .dashboard-grid {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        @media (min-width: 992px) {
            .dashboard-grid {
                flex-direction: row;
            }
        }

        /* Sidebar info card */
        .info-card {
            flex: 1.2;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(4px);
            border-radius: 1.8rem;
            padding: 1.5rem;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
        }

        /* Main content area */
        .main-content {
            flex: 2.5;
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(4px);
            border-radius: 1.8rem;
            padding: 1.5rem;
            box-shadow: 0 20px 35px -12px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-weight: 800;
            font-size: 1.3rem;
            color: #1e3a5f;
            border-left: 5px solid #e67e22;
            padding-left: 14px;
            margin-bottom: 1.2rem;
        }

        .highlight {
            color: #e67e22;
            font-weight: 700;
        }

        .sris-bullet-list {
            list-style: none;
            padding-left: 0;
        }

        .sris-bullet-list li {
            margin-bottom: 0.8rem;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }

        .sris-bullet-list li i {
            color: #e67e22;
            width: 22px;
        }

        .stat-badge {
            background: #fef5e8;
            border-radius: 1rem;
            padding: 0.5rem 1rem;
            margin-bottom: 0.7rem;
            font-weight: 500;
        }

        /* Map placeholder */
        .map-placeholder {
            background: #eef2f5;
            border-radius: 1.2rem;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            background-image: radial-gradient(circle at 10% 30%, rgba(230, 126, 34, 0.05) 2%, transparent 2.5%);
            background-size: 28px 28px;
            border: 1px solid rgba(230, 126, 34, 0.2);
            position: relative;
            overflow: hidden;
        }

        .map-mock {
            text-align: center;
        }

        .map-mock i {
            font-size: 3.5rem;
            color: #e67e22;
            margin-bottom: 0.5rem;
        }

        /* property table */
        .property-table-wrapper {
            overflow-x: auto;
            margin-top: 1rem;
        }

        .property-table {
            width: 100%;
            font-size: 0.8rem;
        }

        .property-table th {
            background: #fef5e8;
            padding: 0.7rem;
        }

        .property-table td {
            padding: 0.6rem;
            border-bottom: 1px solid #eee;
        }

        .btn-sm-sris {
            background: #e67e22;
            border: none;
            color: white;
            border-radius: 30px;
            padding: 0.2rem 0.7rem;
            font-size: 0.7rem;
        }

        /* action buttons */
        .action-buttons {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
            margin: 1.2rem 0 1rem;
        }

        .btn-sris {
            background: linear-gradient(95deg, #e67e22, #f39c12);
            border: none;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
            color: white;
        }

        .btn-sris-outline {
            background: transparent;
            border: 1.5px solid #e67e22;
            color: #e67e22;
            border-radius: 40px;
            padding: 8px 20px;
            font-weight: 600;
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

        @media (max-width: 576px) {
            .dashboard-wrapper {
                padding: 0 0.8rem;
            }

            .info-card,
            .main-content {
                padding: 1rem;
            }

            .sris-navbar {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
                text-align: center;
            }
        }
    </style>
</head>

<body>
    <div class="heritage-bg">
        <img src="https://placehold.co/1600x900/f5f0e6/d9b48b?text=Tamil+Nadu+Heritage"
            alt="Tamil Nadu Government Heritage">
    </div>
    <div class="bg-overlay"></div>
    <div class="particles" id="particles-container"></div>
    <div id="toast-container" class="toast-container"></div>

    <div class="dashboard-wrapper">
        <!-- Navbar -->
        <div class="sris-navbar">
            <div class="sris-brand">
                <div class="brand-icon"><i class="fas fa-map-marked-alt"></i></div>
                <div>
                    <div class="brand-text">Spatial Revenue Intelligent System (SRIS)</div>
                    <div class="brand-sub">Tamil Nadu Municipal · GIS Property Governance</div>
                </div>
            </div>
            <div class="surveyor-badge">
                <i class="fas fa-user-hard-hat"></i> Surveyor Dashboard · Active Session
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- LEFT INFO PANEL (SRIS Description & Stats) -->
            <div class="info-card">
                <div class="section-title">SRIS · The GIS Revolution</div>
                <p style="font-size:0.85rem; line-height:1.5; color:#2c4c6e;">
                    <strong>Spatial Revenue Intelligent System (SRIS)</strong> is a smart digital Web GIS platform
                    developed for efficient mapping and management of municipal properties.
                    It enables real-time visualization, monitoring, and spatial analysis of assets such as buildings,
                    roads, water connections, and tax properties through an interactive map-based system.
                </p>
                <div class="stat-badge mt-2">
                    <i class="fas fa-chart-line"></i> <strong>Supports local bodies in:</strong>
                </div>
                <ul class="sris-bullet-list">
                    <li><i class="fas fa-check-circle"></i> Improving property administration</li>
                    <li><i class="fas fa-coins"></i> Revenue generation</li>
                    <li><i class="fas fa-city"></i> Urban planning</li>
                    <li><i class="fas fa-chalkboard-user"></i> Decision-making through accurate GIS-enabled spatial data
                    </li>
                </ul>
                <hr>
                <div class="d-flex justify-content-between flex-wrap">
                    <div><i class="fas fa-building"></i> <strong>1,284</strong> Properties Mapped</div>
                    <div><i class="fas fa-road"></i> <strong>342</strong> Road Assets</div>
                    <div><i class="fas fa-tint"></i> <strong>561</strong> Water Connections</div>
                </div>
                <div class="mt-3 text-center small text-muted">
                    <i class="fas fa-sync-alt"></i> Live GIS Sync · Today
                </div>
            </div>

            <!-- RIGHT MAIN DASHBOARD (Surveyor Actions + Property List + Map Preview) -->
            <div class="main-content">
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h4 class="mb-0" style="font-weight:800;"><i class="fas fa-clipboard-list text-warning"></i>
                        Surveyor Dashboard</h4>
                    <span class="badge bg-light text-dark mt-2 mt-sm-0"><i class="fas fa-map-pin"></i> Zone: Central
                        Municipal Corporation</span>
                </div>

                <!-- Action Buttons for Surveyor -->
                <div class="action-buttons">
                    <button class="btn-sris" id="syncMapBtn"><i class="fas fa-draw-polygon"></i> Refresh GIS
                        Layer</button>
                    <button class="btn-sris-outline" id="addPropertyBtn"><i class="fas fa-plus-circle"></i> Log New
                        Property</button>
                    <button class="btn-sris-outline" id="genReportBtn"><i class="fas fa-file-alt"></i> Revenue
                        Snapshot</button>
                </div>

                <!-- Map Mock / GIS Visualization -->
                <div class="map-placeholder" id="gisMapMock">
                    <div class="map-mock">
                        <i class="fas fa-map"></i>
                        <p class="mt-2"><strong>Interactive GIS Map</strong><br>Real-time property boundaries, tax
                            zones, water network</p>
                        <button class="btn btn-sm btn-outline-secondary" id="mockMapInteract"
                            style="border-radius:30px;"><i class="fas fa-location-dot"></i> Simulate Spatial
                            View</button>
                    </div>
                </div>

                <!-- Recent Properties & Asset Table -->
                <div class="mt-4">
                    <div class="d-flex justify-content-between">
                        <h6 class="fw-bold"><i class="fas fa-table-list"></i> Recently Mapped Assets / Properties</h6>
                        <span class="small text-muted">GIS accuracy ±0.5m</span>
                    </div>
                    <div class="property-table-wrapper">
                        <table class="property-table">
                            <thead>
                                <tr>
                                    <th>Property ID</th>
                                    <th>Type</th>
                                    <th>Tax Status</th>
                                    <th>Water Conn.</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="propertyTableBody">
                                <tr>
                                    <td>TN-MC-1024</td>
                                    <td>Residential</td>
                                    <td>Paid</td>
                                    <td>Active</td>
                                    <td><button class="btn-sm-sris viewDetailsBtn" data-id="1024">View GIS</button></td>
                                </tr>
                                <tr>
                                    <td>TN-MC-1089</td>
                                    <td>Commercial</td>
                                    <td>Pending</td>
                                    <td>Active</td>
                                    <td><button class="btn-sm-sris viewDetailsBtn" data-id="1089">View GIS</button></td>
                                </tr>
                                <tr>
                                    <td>TN-MC-2056</td>
                                    <td>Industrial</td>
                                    <td>Overdue</td>
                                    <td>Inactive</td>
                                    <td><button class="btn-sm-sris viewDetailsBtn" data-id="2056">View GIS</button></td>
                                </tr>
                                <tr>
                                    <td>TN-MC-3120</td>
                                    <td>Mixed Use</td>
                                    <td>Paid</td>
                                    <td>Active</td>
                                    <td><button class="btn-sm-sris viewDetailsBtn" data-id="3120">View GIS</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted"><i class="fas fa-chart-simple"></i> Revenue projection this quarter:
                            <strong class="text-success">+12.4%</strong> via GIS analytics</small>
                    </div>
                </div>

                <!-- Spatial insight card -->
                <div class="alert alert-warning mt-3 bg-light border-0 shadow-sm" style="border-radius: 20px;">
                    <i class="fas fa-lightbulb text-warning"></i> <strong>Spatial Insight:</strong> High-density zone
                    'Anna Nagar' shows 23% tax potential increase. SRIS recommends field surveyor revisit.
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        // Toast system
        function showToast(type, title, message, duration = 4000) {
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
                <div class="toast-content"><div class="toast-title">${escapeHtml(title)}</div><p class="toast-message">${escapeHtml(message)}</p></div>
                <button class="toast-close"><i class="fas fa-times"></i></button>
                <div class="toast-progress" style="animation-duration: ${duration/1000}s;"></div>
            `;
            container.appendChild(toast);
            setTimeout(() => toast.classList.add('show'), 20);
            const closeBtn = toast.querySelector('.toast-close');
            closeBtn.addEventListener('click', () => removeToast(toast));
            setTimeout(() => removeToast(toast), duration);
        }

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
            const btn = e.target.closest('.btn-sris, .btn-sris-outline, .btn-sm-sris, #mockMapInteract');
            if (btn && !btn.disabled) {
                const ripple = document.createElement('span');
                ripple.classList.add('ripple-effect');
                const rect = btn.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                ripple.style.width = ripple.style.height = size + 'px';
                ripple.style.left = e.clientX - rect.left - size / 2 + 'px';
                ripple.style.top = e.clientY - rect.top - size / 2 + 'px';
                ripple.style.position = 'absolute';
                ripple.style.background = 'radial-gradient(circle, rgba(255,255,255,0.5), rgba(255,255,255,0))';
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

        // Particles generator
        function createParticles() {
            const container = document.querySelector('.particles');
            if (!container) return;
            for (let i = 0; i < 35; i++) {
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

        // ---------- SRIS SURVEYOR DASHBOARD INTERACTIONS ----------
        $(document).ready(function() {
            // 1. Sync GIS Layer Button
            $('#syncMapBtn').on('click', function() {
                showToast('success', 'GIS Layer Refreshed',
                    'Real-time property boundaries and asset layers synchronized with SRIS server.');
                // Simulate updating map mock
                $('#gisMapMock .map-mock i').addClass('fa-spin').removeClass('fa-map').addClass(
                    'fa-sync-alt');
                setTimeout(() => {
                    $('#gisMapMock .map-mock i').removeClass('fa-spin fa-sync-alt').addClass(
                        'fa-map');
                    $('#gisMapMock .map-mock p').html(
                        '<strong>Live GIS Map</strong><br>Updated: new water connections & tax zones visible'
                        );
                }, 1200);
            });

            // 2. Add Property (mock surveyor log)
            $('#addPropertyBtn').on('click', function() {
                let newId = 'TN-MC-' + Math.floor(Math.random() * 9000 + 1000);
                let types = ['Residential', 'Commercial', 'Institutional', 'Vacant Land'];
                let randomType = types[Math.floor(Math.random() * types.length)];
                let taxStatus = ['Paid', 'Pending', 'Overdue'][Math.floor(Math.random() * 3)];
                let waterStat = ['Active', 'Inactive', 'Proposed'][Math.floor(Math.random() * 3)];
                $('#propertyTableBody').prepend(`
                    <tr>
                        <td>${newId}</td>
                        <td>${randomType}</td>
                        <td>${taxStatus}</td>
                        <td>${waterStat}</td>
                        <td><button class="btn-sm-sris viewDetailsBtn" data-id="${newId.split('-')[2]}">View GIS</button></td>
                    </tr>
                `);
                showToast('info', 'Property Logged',
                    `New property ${newId} added to SRIS GIS registry. Revenue mapping updated.`);
                // trigger spatial notification
                $('.alert-warning').html(
                    `<i class="fas fa-lightbulb text-warning"></i> <strong>Spatial Alert:</strong> New property added at ${newId}. SRIS recommends surveyor field verification for tax assessment.`
                    );
                bindViewButtons();
            });

            // 3. Revenue Snapshot
            $('#genReportBtn').on('click', function() {
                showToast('success', 'Revenue Snapshot',
                    'GIS-based revenue projection: ₹2.84Cr this quarter. Improvement +8% YoY due to property revaluation.'
                    );
                $('.alert-warning').html(
                    `<i class="fas fa-chart-line text-success"></i> <strong>Revenue Insight:</strong> Digital property indexing improved collection efficiency by 15.3% in last 2 months.`
                    );
            });

            // 4. Mock Map Interact - simulate real GIS spatial analysis
            $('#mockMapInteract').on('click', function() {
                showToast('info', 'SRIS Spatial Intelligence',
                    'Displaying asset heatmap: buildings, roads, water connections. Tax delinquent zones highlighted in orange.'
                    );
                $('#gisMapMock').css({
                    background: 'linear-gradient(145deg, #f8e3cb, #fdebd0)',
                    transition: '0.3s'
                });
                setTimeout(() => $('#gisMapMock').css('background', ''), 800);
                // extra demo: update table with sample GIS coordinates
                let randomProp = $('#propertyTableBody tr:first td:first').text();
                if (randomProp) showToast('warning', 'GIS Coordinates',
                    `Property ${randomProp} located at Lat 13.0827, Lon 80.2707 · Tax zone evaluation ready`,
                    3000);
            });

            // View Details for each property - simulate GIS deep dive
            function bindViewButtons() {
                $('.viewDetailsBtn').off('click').on('click', function() {
                    let propId = $(this).data('id');
                    showToast('success', `GIS Property Details - ID ${propId}`,
                        `Spatial data: Building footprint, annual tax value, water connection status, road proximity. Surveyor geo-tag verified.`
                        );
                    // update map mock to reflect
                    $('#gisMapMock .map-mock p').html(
                        `<strong>Viewing Property #${propId}</strong><br>GIS polygon: 245 sqm · Zoning: Mixed Use · Revenue: High priority`
                        );
                    $('#gisMapMock .map-mock i').removeClass('fa-map').addClass('fa-location-dot').css(
                        'color', '#e67e22');
                });
            }
            bindViewButtons();

            // Additional spatial message: Display Quote from original description (custom)
            $('.info-card').append(`
                <div class="mt-3 pt-2 border-top">
                    <i class="fas fa-microchip"></i> <span style="font-size:0.7rem;">SRIS integrates real-time monitoring and spatial analytics for smart urban planning and revenue decision-making.</span>
                </div>
            `);

            // Demo auto toast for surveyor welcome (optional)
            setTimeout(() => {
                showToast('info', 'SRIS Surveyor Dashboard',
                    'Welcome! Use GIS tools to manage properties and boost revenue. Real-time data sync active.',
                    5000);
            }, 800);
        });
    </script>
</body>

</html>
