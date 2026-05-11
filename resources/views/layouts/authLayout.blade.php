<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SRIS | Surveyor Dashboard - Property Tax Intelligence</title>

    <!-- Bootstrap 5 + Icons + Fonts -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;400;500;600;700;800&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Leaflet CSS for GIS Map -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Poppins', sans-serif;
            background: #f0f4f8;
            overflow-x: hidden;
        }

        /* Navbar surveyor style */
        .sris-navbar {
            background: linear-gradient(98deg, #0b2b3b 0%, #1a4a5f 100%);
            padding: 0.8rem 1.5rem;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .brand-logo {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .brand-icon {
            background: #e67e22;
            width: 40px;
            height: 40px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
            color: white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .brand-text {
            font-weight: 800;
            font-size: 1.2rem;
            color: white;
            letter-spacing: -0.3px;
        }

        .brand-sub {
            font-size: 0.7rem;
            color: #ffdfb3;
        }

        .surveyor-badge {
            background: rgba(230, 126, 34, 0.2);
            padding: 6px 14px;
            border-radius: 40px;
            font-size: 0.8rem;
            font-weight: 600;
            color: #f39c12;
            border: 1px solid rgba(243, 156, 18, 0.4);
        }

        /* Stats Cards */
        .stat-card {
            background: white;
            border-radius: 24px;
            padding: 1rem 1.2rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.02), 0 2px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            border-left: 5px solid #e67e22;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            color: #5e7a93;
            letter-spacing: 0.5px;
        }

        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1e3a5f;
            line-height: 1.2;
        }

        /* Map Container */
        .map-card {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.08);
            background: white;
            height: 100%;
            min-height: 380px;
        }

        #gisMap {
            height: 380px;
            width: 100%;
            z-index: 1;
        }

        /* Property table */
        .table-responsive-custom {
            border-radius: 20px;
            background: white;
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.03);
        }

        .table thead th {
            background: #f8fafd;
            font-weight: 700;
            font-size: 0.8rem;
            color: #1e3a5f;
            border-bottom: 2px solid #e9ecef;
        }

        .table td {
            font-size: 0.8rem;
            vertical-align: middle;
        }

        .badge-tax-paid {
            background: #d1fae5;
            color: #0b5e42;
            font-weight: 600;
            padding: 5px 12px;
            border-radius: 40px;
            font-size: 0.7rem;
        }

        .badge-tax-pending {
            background: #fee2e2;
            color: #b91c1c;
            font-weight: 600;
        }

        .btn-survey-action {
            background: white;
            border: 1px solid #e67e22;
            color: #e67e22;
            border-radius: 30px;
            padding: 4px 12px;
            font-size: 0.7rem;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-survey-action:hover {
            background: #e67e22;
            color: white;
        }

        .section-title {
            font-weight: 700;
            color: #1e3a5f;
            border-left: 5px solid #e67e22;
            padding-left: 14px;
            margin-bottom: 1.2rem;
        }

        footer {
            background: #0a2a38;
            color: #b6cddf;
            font-size: 0.75rem;
            padding: 1rem;
            text-align: center;
            margin-top: 2rem;
        }

        /* Toast custom */
        .toast-container-bottom {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            z-index: 9999;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .brand-text {
                font-size: 1rem;
            }

            .stat-value {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>

    <!-- Surveyor Navbar -->
    <nav class="sris-navbar">
        <div class="container-fluid d-flex flex-wrap align-items-center justify-content-between">
            <div class="brand-logo">
                <div class="brand-icon"><i class="fas fa-map-marked-alt"></i></div>
                <div>
                    <div class="brand-text">SRIS <span style="font-weight:600">Spatial Revenue Intelligent System</span></div>
                    <div class="brand-sub">Tamil Nadu · GIS Property Survey</div>
                </div>
            </div>
            <div class="d-flex gap-3 align-items-center mt-2 mt-sm-0">
                <div class="surveyor-badge"><i class="fas fa-hard-hat me-1"></i> Surveyor Access · SRIS Field Ops</div>
                <button id="logoutSimulateBtn" class="btn btn-sm btn-outline-light rounded-pill px-3"><i class="fas fa-sign-out-alt me-1"></i> Logout</button>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <!-- Welcome Row -->
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <h4 class="fw-bold text-dark"><i class="fas fa-tachometer-alt me-2" style="color:#e67e22"></i> Surveyor Dashboard</h4>
                <p class="text-muted small">Welcome back, Senior Surveyor Kumaravel · Real-time property intelligence & tax assessment</p>
            </div>
            <div class="mt-2 mt-md-0">
                <button class="btn btn-dark rounded-pill px-4" id="refreshDataBtn"><i class="fas fa-sync-alt me-1"></i> Sync GIS Data</button>
            </div>
        </div>

        <!-- Stats Cards: Tax & Revenue summary -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-building me-1"></i> Total Properties</div>
                    <div class="stat-value" id="totalProperties">2,486</div>
                    <small class="text-success"><i class="fas fa-arrow-up"></i> +11.4% this year</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-rupee-sign me-1"></i> Annual Tax Collection</div>
                    <div class="stat-value" id="totalTaxCollected">₹4.28 Cr</div>
                    <small class="text-muted">FY 2025-26 target: ₹5.2 Cr</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-clock me-1"></i> Pending Assessments</div>
                    <div class="stat-value" id="pendingAssessments">142</div>
                    <small class="text-warning">Needs site survey</small>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-title"><i class="fas fa-water me-1"></i> Water Tax Arrears</div>
                    <div class="stat-value" id="waterArrears">₹36.2 L</div>
                    <small class="text-danger">+ overdue notices sent</small>
                </div>
            </div>
        </div>

        <!-- GIS Map + Quick Actions row -->
        <div class="row g-4 mb-4">
            <div class="col-lg-7">
                <div class="map-card">
                    <div id="gisMap"></div>
                    <div class="p-2 bg-light border-top small text-secondary text-center">
                        <i class="fas fa-map-marker-alt text-warning"></i> Interactive GIS Layer | Property boundaries & tax zones (Tamil Nadu Urban)
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="bg-white rounded-4 p-3 h-100 shadow-sm">
                    <h6 class="fw-bold mb-3"><i class="fas fa-clipboard-list me-2" style="color:#e67e22"></i> Surveyor Quick Tasks</h6>
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-secondary rounded-pill text-start ps-3" id="newPropertySurveyBtn"><i class="fas fa-draw-polygon me-2"></i> New Property Mapping (GIS)</button>
                        <button class="btn btn-outline-secondary rounded-pill text-start ps-3" id="taxAuditBtn"><i class="fas fa-file-invoice-dollar me-2"></i> Tax Audit & Revenue Inspection</button>
                        <button class="btn btn-outline-secondary rounded-pill text-start ps-3" id="pendingArrearsBtn"><i class="fas fa-exclamation-triangle me-2"></i> View High Pending Tax List</button>
                        <hr class="my-2">
                        <div class="alert alert-warning py-2 small mb-0">
                            <i class="fas fa-lightbulb"></i> <strong>SRIS Insight:</strong> 23 properties have unassessed built-up area. Click <a href="#" id="suggestSurveyLink" class="alert-link">suggest field visit</a>.
                        </div>
                    </div>
                    <div class="mt-3 p-2 bg-light rounded-3">
                        <div class="fw-semibold small"><i class="fas fa-chart-line me-1"></i> Revenue trend (last 6 months)</div>
                        <div class="progress mt-1" style="height: 8px;">
                            <div class="progress-bar bg-warning" style="width: 72%" role="progressbar">72%</div>
                        </div>
                        <div class="d-flex justify-content-between small mt-1">
                            <span>Tax collection target achieved</span>
                            <span>₹3.08 Cr / ₹4.28 Cr</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Property Tax Table (All tax related content) -->
        <div class="d-flex justify-content-between align-items-center mt-3 mb-2">
            <h5 class="section-title"><i class="fas fa-table-list me-2"></i> Property Tax Register · Surveyor Assessment View</h5>
            <span class="badge bg-secondary px-3 py-2 rounded-pill"><i class="fas fa-database"></i> Live from SRIS GIS Hub</span>
        </div>

        <div class="table-responsive-custom rounded-4 overflow-hidden">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>Property ID</th><th>Owner Name</th><th>Zone/Ward</th><th>Property Type</th><th>Built-up Area (sqft)</th><th>Annual Property Tax</th><th>Water Tax</th><th>Status</th><th>Survey Action</th>
                    </tr>
                </thead>
                <tbody id="propertyTaxTableBody">
                    <!-- dynamic rows via JS -->
                </tbody>
            </table>
        </div>
        <div class="mt-3 text-end">
            <button class="btn btn-link text-decoration-none small" id="viewAllPropertiesBtn"><i class="fas fa-arrow-right"></i> View all 2,486 properties on GIS map</button>
        </div>
    </div>

    <footer>
        <i class="fas fa-copyright"></i> Spatial Revenue Intelligent System (SRIS) - TN Municipal | GIS enabled property tax management | Surveyor dashboard realtime
    </footer>

    <div id="toastContainer" class="toast-container-bottom"></div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ---------- MOCK TAX DATA (full surveyor context) ----------
        // Extended properties dataset matching tax & revenue content
        let propertiesData = [
            { id: "TN-MC-1024", owner: "Sundararajan M", ward: "Ward 12 - Anna Nagar", type: "Residential", area: 1450, propTax: 12450, waterTax: 2400, status: "paid", lat: 13.0827, lng: 80.2707 },
            { id: "TN-MC-1089", owner: "Meena Kumari", ward: "Ward 08 - Periyar Nagar", type: "Commercial", area: 2870, propTax: 45800, waterTax: 5800, status: "pending", lat: 13.0912, lng: 80.2789 },
            { id: "TN-MC-1156", owner: "Ganesh P", ward: "Ward 03 - Gandhi Nagar", type: "Mixed Use", area: 3200, propTax: 67200, waterTax: 7200, status: "overdue", lat: 13.0765, lng: 80.2854 },
            { id: "TN-MC-1202", owner: "Lakshmi Constructions", ward: "Ward 15 - Thiruvalluvar Nagar", type: "Institutional", area: 5400, propTax: 112000, waterTax: 12500, status: "paid", lat: 13.1023, lng: 80.2598 },
            { id: "TN-MC-1321", owner: "Arumugam Textiles", ward: "Ward 22 - New Market", type: "Commercial", area: 4100, propTax: 84500, waterTax: 9400, status: "pending", lat: 13.0685, lng: 80.2921 },
            { id: "TN-MC-1450", owner: "Thangam Hospital", ward: "Ward 05 - Health District", type: "Healthcare", area: 7200, propTax: 156000, waterTax: 18000, status: "paid", lat: 13.0956, lng: 80.2633 },
            { id: "TN-MC-1509", owner: "Ravi Colony Apartments", ward: "Ward 18 - Lake Area", type: "Residential", area: 3850, propTax: 31200, waterTax: 3900, status: "pending", lat: 13.1117, lng: 80.2755 },
            { id: "TN-MC-1678", owner: "Senthil Enterprises", ward: "Ward 30 - Industrial Estate", type: "Industrial", area: 8900, propTax: 204700, waterTax: 22600, status: "overdue", lat: 13.0502, lng: 80.2888 }
        ];

        // Tax totals calculations
        function computeTotals() {
            let totalTaxSum = propertiesData.reduce((acc, p) => acc + p.propTax, 0);
            let pendingCount = propertiesData.filter(p => p.status === 'pending' || p.status === 'overdue').length;
            let waterArrearsSum = propertiesData.filter(p => p.status !== 'paid').reduce((acc, p) => acc + p.waterTax, 0);
            document.getElementById('totalProperties').innerText = propertiesData.length + 2478; // mock total real count
            document.getElementById('totalTaxCollected').innerText = '₹' + (totalTaxSum / 10000000).toFixed(2) + ' Cr';
            document.getElementById('pendingAssessments').innerText = pendingCount + 118;
            document.getElementById('waterArrears').innerText = '₹' + ((waterArrearsSum + 286000) / 100000).toFixed(1) + ' L';
        }

        // Render property table with tax fields
        function renderPropertyTable() {
            const tbody = document.getElementById('propertyTaxTableBody');
            tbody.innerHTML = '';
            propertiesData.forEach(prop => {
                let statusBadge = '';
                if (prop.status === 'paid') statusBadge = '<span class="badge-tax-paid rounded-pill"><i class="fas fa-check-circle"></i> Paid</span>';
                else if (prop.status === 'pending') statusBadge = '<span class="badge bg-warning text-dark rounded-pill"><i class="fas fa-clock"></i> Pending</span>';
                else statusBadge = '<span class="badge-tax-pending rounded-pill"><i class="fas fa-exclamation-circle"></i> Overdue</span>';

                const row = `<tr>
                    <td class="fw-semibold">${prop.id}</td>
                    <td>${prop.owner}</td>
                    <td>${prop.ward}</td>
                    <td>${prop.type}</td>
                    <td>${prop.area.toLocaleString()}</td>
                    <td>₹${prop.propTax.toLocaleString()}</td>
                    <td>₹${prop.waterTax.toLocaleString()}</td>
                    <td>${statusBadge}</td>
                    <td><button class="btn-survey-action surveyDetailBtn" data-id="${prop.id}"><i class="fas fa-clipboard"></i> Survey</button></td>
                </tr>`;
                tbody.insertAdjacentHTML('beforeend', row);
            });
            // attach survey detail events
            document.querySelectorAll('.surveyDetailBtn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const propId = btn.getAttribute('data-id');
                    showToast('info', 'Surveyor Field Action', `Open SRIS assessment form for Property ${propId}. Update tax evaluation, built-up area and GIS geometry.`, 3500);
                });
            });
        }

        // Toast reusable
        function showToast(type, title, message, duration = 4000) {
            const container = document.getElementById('toastContainer');
            const toastDiv = document.createElement('div');
            toastDiv.className = `toast align-items-center text-bg-${type === 'error' ? 'danger' : (type === 'success' ? 'success' : 'warning')} border-0 mb-2`;
            toastDiv.setAttribute('role', 'alert');
            toastDiv.setAttribute('aria-live', 'assertive');
            toastDiv.setAttribute('aria-atomic', 'true');
            const icon = type === 'success' ? 'fa-circle-check' : (type === 'error' ? 'fa-circle-exclamation' : 'fa-bell');
            toastDiv.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body"><i class="fas ${icon} me-2"></i><strong>${title}</strong> - ${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            container.appendChild(toastDiv);
            const bsToast = new bootstrap.Toast(toastDiv, { delay: duration, autohide: true });
            bsToast.show();
            toastDiv.addEventListener('hidden.bs.toast', () => toastDiv.remove());
        }

        // Initialize Leaflet Map with property markers (tax GIS demo)
        let mapInstance;
        function initGISMap() {
            mapInstance = L.map('gisMap').setView([13.0827, 80.277], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> & CartoDB',
                subdomains: 'abcd',
                maxZoom: 19
            }).addTo(mapInstance);

            // Add property markers (taxable assets)
            propertiesData.forEach(prop => {
                const markerColor = prop.status === 'paid' ? 'green' : (prop.status === 'pending' ? 'orange' : 'red');
                const customIcon = L.divIcon({
                    html: `<i class="fas fa-building" style="color:${markerColor}; font-size:22px; text-shadow:0 0 3px white"></i>`,
                    iconSize: [24, 24],
                    className: 'custom-marker-icon'
                });
                const marker = L.marker([prop.lat, prop.lng], { icon: customIcon }).addTo(mapInstance);
                marker.bindPopup(`
                    <b>${prop.id}</b><br>Owner: ${prop.owner}<br>Tax: ₹${prop.propTax.toLocaleString()} (${prop.status})<br>
                    <i class="fas fa-map"></i> Ward: ${prop.ward}<br>
                    <button class="btn btn-sm btn-warning mt-1 surveyPopBtn" data-id="${prop.id}">Survey & Tax Assessment</button>
                `);
                marker.on('popupopen', () => {
                    document.querySelectorAll('.surveyPopBtn').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            const pid = btn.getAttribute('data-id');
                            showToast('success', 'SRIS GIS Action', `Surveyor scheduled field inspection for PID ${pid}. Update floor area, tax revision.`, 4000);
                        });
                    });
                });
            });
            // additional zone polygon representation
            const zonePoly = L.polygon([[13.065, 80.260], [13.100, 80.255], [13.112, 80.285], [13.076, 80.300]], { color: "#e67e22", weight: 2, fillOpacity: 0.1 }).addTo(mapInstance);
            zonePoly.bindPopup("Revenue Zone A - High Priority Tax Region");
        }

        // Simulate refresh / GIS sync
        document.getElementById('refreshDataBtn')?.addEventListener('click', () => {
            showToast('success', 'SRIS Sync Completed', 'Live property tax & GIS layers updated. Pending assessments: 142', 3000);
            computeTotals();
        });

        // Quick task handlers
        document.getElementById('newPropertySurveyBtn')?.addEventListener('click', () => {
            showToast('info', 'New Property Mapping', 'Launch GIS digitization tool to add unassessed land/building records & calculate tax liability.', 3800);
        });
        document.getElementById('taxAuditBtn')?.addEventListener('click', () => {
            showToast('warning', 'Tax Audit Module', 'Revenue inspection checklist: 34 properties selected for variance between GIS area and tax records.', 4000);
        });
        document.getElementById('pendingArrearsBtn')?.addEventListener('click', () => {
            showToast('error', 'High Priority Arrears', 'Top 12 defaulters: Total overdue property tax + water tax = ₹18.7 Lakhs. Initiate notice.', 4500);
        });
        document.getElementById('suggestSurveyLink')?.addEventListener('click', (e) => {
            e.preventDefault();
            showToast('warning', 'SRIS Field Visit Suggestion', '23 unassessed built-up + 7 underreported commercial units → schedule site survey.', 4000);
        });
        document.getElementById('viewAllPropertiesBtn')?.addEventListener('click', () => {
            if (mapInstance) {
                mapInstance.setView([13.0827, 80.277], 12);
                showToast('success', 'GIS Focus', 'Zoom to master property layer showing 2.4k+ tax units', 3000);
            }
        });
        document.getElementById('logoutSimulateBtn')?.addEventListener('click', () => {
            showToast('info', 'Logout', 'Surveyor session ended. Redirect to login portal.', 2000);
            setTimeout(() => { window.location.href = '#'; alert('Demo logout - SRIS Surveyor Portal'); }, 1500);
        });

        // additional dynamic simulation for tax dashboard
        function simulateTaxUpdates() {
            setInterval(() => {
                // just for dynamic feel: update stat value mini changing revenue
                let newCollected = (Math.random() * 0.15 + 4.2).toFixed(2);
                if (document.getElementById('totalTaxCollected')) {
                    // optional subtle update to show live financial monitoring
                }
            }, 30000);
        }

        // final initializers
        window.addEventListener('load', () => {
            renderPropertyTable();
            computeTotals();
            initGISMap();
            simulateTaxUpdates();

            // optional: open default welcome toast for surveyor
            setTimeout(() => {
                showToast('success', 'SRIS Surveyor Dashboard', 'Welcome Kumaravel! Today’s tasks: 12 pending assessments, 5 overdue tax audits. GIS mapping active.', 5000);
            }, 800);
        });
    </script>
</body>
</html>
