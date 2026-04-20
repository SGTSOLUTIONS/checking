@extends('layouts.admin-layout')

@section('title', 'Dashboard')
@section('content')
<div class="dashboard-grid">
    <div class="stat-card primary">
        <div class="stat-icon primary">
            <i class="fas fa-map-marker-alt"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">2,847</div>
            <div class="stat-label">Geographic Locations</div>
            <div class="stat-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>18% increase</span>
            </div>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-icon success">
            <i class="fas fa-layer-group"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">42</div>
            <div class="stat-label">Active Data Layers</div>
            <div class="stat-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>5 new layers</span>
            </div>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-icon warning">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">1,284</div>
            <div class="stat-label">Active Users</div>
            <div class="stat-trend trend-up">
                <i class="fas fa-arrow-up"></i>
                <span>12% growth</span>
            </div>
        </div>
    </div>

    <div class="stat-card danger">
        <div class="stat-icon danger">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-info">
            <div class="stat-value">8</div>
            <div class="stat-label">System Alerts</div>
            <div class="stat-trend trend-down">
                <i class="fas fa-arrow-down"></i>
                <span>3 resolved</span>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-section">
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">Data Usage Analytics</h3>
            <div class="chart-actions">
                <button class="chart-btn">
                    <i class="fas fa-download"></i>
                    Export
                </button>
                <button class="chart-btn">
                    <i class="fas fa-filter"></i>
                    Filter
                </button>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="usageChart"></canvas>
        </div>
    </div>

    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">User Activity</h3>
            <div class="chart-actions">
                <button class="chart-btn">
                    <i class="fas fa-sync-alt"></i>
                    Refresh
                </button>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="activityChart"></canvas>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-plus"></i>
        </div>
        <h3 class="action-title">Add New Layer</h3>
        <p class="action-desc">Upload and configure new geographic data layers</p>
    </div>

    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <h3 class="action-title">Generate Report</h3>
        <p class="action-desc">Create custom analytics and visualization reports</p>
    </div>

    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-user-plus"></i>
        </div>
        <h3 class="action-title">Manage Users</h3>
        <p class="action-desc">Add or modify user accounts and permissions</p>
    </div>

    <div class="action-card">
        <div class="action-icon">
            <i class="fas fa-cogs"></i>
        </div>
        <h3 class="action-title">System Settings</h3>
        <p class="action-desc">Configure application and map settings</p>
    </div>
</div>

<!-- Map Section -->
<div class="map-section">
    <div class="map-container">
        <div class="chart-header">
            <h3 class="chart-title">Geographic Data Explorer</h3>
            <div class="chart-actions">
                <button class="chart-btn">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                <button class="chart-btn">
                    <i class="fas fa-download"></i>
                    Export
                </button>
            </div>
        </div>
        <div class="map-wrapper">
            <div id="map"></div>
            <div class="map-controls">
                <button class="control-btn" title="Zoom In" id="zoom-in">
                    <i class="fas fa-plus"></i>
                </button>
                <button class="control-btn" title="Zoom Out" id="zoom-out">
                    <i class="fas fa-minus"></i>
                </button>
                <button class="control-btn" title="Reset View" id="reset-view">
                    <i class="fas fa-crosshairs"></i>
                </button>
                <button class="control-btn" title="Full Screen" id="fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
            <div class="layer-controls">
                <div class="layer-title">Map Layers</div>
                <div class="layer-item">
                    <input type="checkbox" class="layer-checkbox" id="base-layer" checked>
                    <label for="base-layer">Base Map</label>
                </div>
                <div class="layer-item">
                    <input type="checkbox" class="layer-checkbox" id="marker-layer" checked>
                    <label for="marker-layer">Location Markers</label>
                </div>
                <div class="layer-item">
                    <input type="checkbox" class="layer-checkbox" id="heatmap-layer">
                    <label for="heatmap-layer">Heatmap</label>
                </div>
                <div class="layer-item">
                    <input type="checkbox" class="layer-checkbox" id="boundary-layer">
                    <label for="boundary-layer">Boundaries</label>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="activity-container">
    <div class="activity-header">
        <h3 class="activity-title">Recent Activity</h3>
        <div class="chart-actions">
            <button class="chart-btn">
                <i class="fas fa-sync-alt"></i>
                Refresh
            </button>
        </div>
    </div>
    <div class="activity-list">
        <div class="activity-item">
            <div class="activity-icon primary">
                <i class="fas fa-user-plus"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title-text">New User Registration</div>
                <div class="activity-desc">Dr. Sarah Johnson has been added to the platform with editor privileges</div>
                <div class="activity-time">
                    <i class="far fa-clock"></i>
                    15 minutes ago
                </div>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-icon success">
                <i class="fas fa-map-pin"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title-text">Data Layer Updated</div>
                <div class="activity-desc">Transportation network layer has been updated with 47 new points</div>
                <div class="activity-time">
                    <i class="far fa-clock"></i>
                    2 hours ago
                </div>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-icon warning">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title-text">System Alert</div>
                <div class="activity-desc">High memory usage detected on geospatial processing server</div>
                <div class="activity-time">
                    <i class="far fa-clock"></i>
                    5 hours ago
                </div>
            </div>
        </div>

        <div class="activity-item">
            <div class="activity-icon danger">
                <i class="fas fa-database"></i>
            </div>
            <div class="activity-content">
                <div class="activity-title-text">Data Export Completed</div>
                <div class="activity-desc">Regional demographic data export finished successfully (2.4 GB)</div>
                <div class="activity-time">
                    <i class="far fa-clock"></i>
                    Yesterday
                </div>
            </div>
        </div>
    </div>
</div>
@endsection