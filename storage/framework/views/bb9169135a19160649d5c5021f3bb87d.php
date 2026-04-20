<?php $__env->startSection('css'); ?>
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --success-gradient: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    body {
        background: #f0f2f5;
    }

    /* Modern Card Styles */
    .modern-card {
        background: white;
        border-radius: 20px;
        border: none;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
    }

    /* Stat Cards */
    .stat-card {
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        border: none;
        transition: all 0.3s ease;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
        pointer-events: none;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon-wrapper {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        width: 60px;
        height: 60px;
        background: rgba(255,255,255,0.2);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .stat-icon-wrapper i {
        font-size: 30px;
        color: white;
    }

    /* Progress Bar Styles */
    .progress-modern {
        height: 8px;
        border-radius: 10px;
        background: rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .progress-bar-modern {
        background: var(--primary-gradient);
        border-radius: 10px;
        transition: width 1s ease;
    }

    /* Table Styles */
    .data-table {
        border-collapse: separate;
        border-spacing: 0 8px;
    }

    .data-table thead th {
        background: #f8f9fa;
        border: none;
        padding: 15px;
        font-weight: 600;
        color: #495057;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .data-table tbody tr {
        background: white;
        border-radius: 12px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .data-table tbody tr:hover {
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .data-table tbody td {
        border: none;
        padding: 15px;
        vertical-align: middle;
    }

    /* Badge Styles */
    .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.75rem;
    }

    .badge-completed {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .badge-pending {
        background: linear-gradient(135deg, #ffc107, #fd7e14);
        color: white;
    }

    /* Chart Container */
    .chart-container {
        background: white;
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 25px;
    }

    /* Header Section */
    .page-header {
        background: white;
        border-radius: 20px;
        padding: 25px 30px;
        margin-bottom: 30px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }

    /* Card Header */
    .card-header-modern {
        background: white;
        border-bottom: 2px solid #f0f2f5;
        padding: 20px 25px;
    }

    .card-header-modern h5 {
        margin: 0;
        font-weight: 600;
        color: #2c3e50;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stat-icon-wrapper {
            width: 45px;
            height: 45px;
        }
        .stat-icon-wrapper i {
            font-size: 22px;
        }
        .data-table tbody td {
            padding: 10px;
        }
    }

    /* Animation */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease forwards;
    }

    .delay-1 { animation-delay: 0.1s; }
    .delay-2 { animation-delay: 0.2s; }
    .delay-3 { animation-delay: 0.3s; }
    .delay-4 { animation-delay: 0.4s; }
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="page-header animate-fadeInUp">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-6 fw-bold mb-2" style="background: var(--primary-gradient); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                    <i class="fas fa-chart-line me-2"></i>Survey Progress Dashboard
                </h1>
                <p class="text-muted mb-0">
                    <i class="fas fa-map-marker-alt me-2"></i>Ward <?php echo e($ward->ward_no); ?> |
                    <i class="fas fa-user me-2 ms-2"></i><?php echo e($workerName); ?> |
                    <i class="fas fa-calendar me-2 ms-2"></i><?php echo e(now()->format('d-m-Y H:i:s')); ?>

                </p>
            </div>
            <div class="col-md-4 text-end">
                <div class="d-inline-block px-4 py-2 bg-light rounded-3">
                    <small class="text-muted">Overall Completion</small>
                    <h3 class="mb-0 fw-bold text-primary"><?php echo e($overallProgress['mis_completion_percentage'] ?? 0); ?>%</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3 animate-fadeInUp delay-1">
            <div class="stat-card card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small mb-2 opacity-75">Buildings Surveyed</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($overallProgress['surveyed_buildings'] ?? 0); ?></h2>
                    <small class="opacity-75">Total: <?php echo e($overallProgress['total_mis_records'] ?? 0); ?></small>
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-building"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 animate-fadeInUp delay-2">
            <div class="stat-card card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small mb-2 opacity-75">Bills Surveyed</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($overallProgress['surveyed_points'] ?? 0); ?></h2>
                    <small class="opacity-75">Completion: <?php echo e($overallProgress['point_completion_percentage'] ?? 0); ?>%</small>
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 animate-fadeInUp delay-3">
            <div class="stat-card card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small mb-2 opacity-75">Expected Bills</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($overallProgress['total_expected_bills'] ?? 0); ?></h2>
                    <small class="opacity-75">Total to collect</small>
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-tasks"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3 animate-fadeInUp delay-4">
            <div class="stat-card card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title text-uppercase small mb-2 opacity-75">Progress Rate</h6>
                    <h2 class="mb-0 fw-bold"><?php echo e($overallProgress['mis_completion_percentage'] ?? 0); ?>%</h2>
                    <div class="progress-modern mt-2">
                        <div class="progress-bar-modern" style="width: <?php echo e($overallProgress['mis_completion_percentage'] ?? 0); ?>%; height: 8px;"></div>
                    </div>
                    <div class="stat-icon-wrapper">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Road-wise Progress Section -->
    <div class="modern-card mb-4 animate-fadeInUp">
        <div class="card-header-modern">
            <h5 class="mb-0">
                <i class="fas fa-road me-2 text-primary"></i>
                Surveyed Roads Progress
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if(isset($roadWiseStats) && count($roadWiseStats) > 0): ?>
            <div class="table-responsive">
                <table class="data-table table">
                    <thead>
                        <tr>
                            <th>Road Name</th>
                            <th>Properties Surveyed</th>
                            <th>Bills Progress</th>
                            <th>Completion</th>
                            <th>Status</th>
                         </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $roadWiseStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="fw-semibold">
                                <i class="fas fa-map-pin me-2 text-primary"></i>
                                <?php echo e($stat->road_name); ?>

                            </td>
                            <td>
                                <span class="fw-bold"><?php echo e($stat->surveyed_buildings); ?></span>
                                <small class="text-muted">/ <?php echo e($stat->total_properties); ?></small>
                            </td>
                            <td>
                                <div class="d-flex justify-content-between mb-1">
                                    <small>Bills: <?php echo e($stat->points_surveyed ?? 0); ?>/<?php echo e($stat->expected_points ?? 0); ?></small>
                                    <small class="text-muted"><?php echo e($stat->completion_percentage); ?>%</small>
                                </div>
                                <div class="progress-modern">
                                    <div class="progress-bar-modern bg-success" style="width: <?php echo e($stat->completion_percentage); ?>%"></div>
                                </div>
                            </td>
                            <td>
                                <h5 class="mb-0 fw-bold text-success"><?php echo e($stat->completion_percentage); ?>%</h5>
                            </td>
                            <td>
                                <?php if($stat->completion_percentage == 100): ?>
                                    <span class="badge-modern badge-completed"><i class="fas fa-check-circle me-1"></i>Complete</span>
                                <?php elseif($stat->completion_percentage >= 50): ?>
                                    <span class="badge-modern" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white;"><i class="fas fa-chart-line me-1"></i>Partial</span>
                                <?php else: ?>
                                    <span class="badge-modern badge-pending"><i class="fas fa-clock me-1"></i>Started</span>
                                <?php endif; ?>
                            </td>
                         </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-road fa-3x text-muted mb-3"></i>
                <p class="text-muted">No roads surveyed yet. Start surveying to see progress!</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Two Column Layout for Charts -->
    <div class="row mb-4">
        <div class="col-md-6 animate-fadeInUp delay-1">
            <div class="modern-card h-100">
                <div class="card-header-modern">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie me-2 text-info"></i>
                        Building Usage Distribution
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="usageChart" height="250"></canvas>
                    <div class="mt-3">
                        <?php $__currentLoopData = $buildingUsageStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-muted"><?php echo e($stat->building_usage); ?></span>
                            <div class="d-flex align-items-center gap-3">
                                <div class="progress-modern" style="width: 150px;">
                                    <div class="progress-bar-modern bg-primary" style="width: <?php echo e(($stat->count / ($buildingUsageStats->sum('count') ?? 1)) * 100); ?>%"></div>
                                </div>
                                <span class="fw-bold"><?php echo e($stat->count); ?></span>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6 animate-fadeInUp delay-2">
            <div class="modern-card h-100">
                <div class="card-header-modern">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2 text-success"></i>
                        Construction Type Analysis
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="constructionChart" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Amenities Grid -->
    <div class="modern-card mb-4 animate-fadeInUp">
        <div class="card-header-modern">
            <h5 class="mb-0">
                <i class="fas fa-home me-2 text-secondary"></i>
                Building Amenities Overview
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php $__currentLoopData = $amenitiesStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity => $stats): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <i class="fas <?php echo e($amenity == 'liftroom' ? 'fa-arrow-up' : ($amenity == 'parking' ? 'fa-parking' : 'fa-check')); ?> fa-2x text-primary mb-2"></i>
                            <h6 class="text-uppercase small mb-2"><?php echo e(str_replace('_', ' ', $amenity)); ?></h6>
                            <div class="d-flex justify-content-around">
                                <div>
                                    <div class="h5 mb-0 text-success"><?php echo e($stats['yes']); ?></div>
                                    <small class="text-muted">Yes</small>
                                </div>
                                <div>
                                    <div class="h5 mb-0 text-danger"><?php echo e($stats['no']); ?></div>
                                    <small class="text-muted">No</small>
                                </div>
                            </div>
                            <div class="progress-modern mt-2">
                                <div class="progress-bar-modern bg-success" style="width: <?php echo e($stats['yes'] > 0 ? ($stats['yes'] / ($stats['yes'] + $stats['no'])) * 100 : 0); ?>%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- UGD Statistics -->
    <div class="modern-card mb-4 animate-fadeInUp">
        <div class="card-header-modern">
            <h5 class="mb-0">
                <i class="fas fa-tint me-2 text-dark"></i>
                UGD Connection Status
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php $__currentLoopData = $ugdStats ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-3 col-sm-6 mb-2">
                    <div class="alert alert-info border-0 rounded-3 mb-0">
                        <i class="fas fa-plug me-2"></i>
                        <strong><?php echo e($stat->ugd); ?></strong>
                        <span class="badge bg-light text-dark float-end"><?php echo e($stat->count); ?></span>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Daily Progress Chart -->
    <div class="modern-card mb-4 animate-fadeInUp">
        <div class="card-header-modern">
            <h5 class="mb-0">
                <i class="fas fa-chart-line me-2 text-primary"></i>
                Daily Survey Progress (Last 30 Days)
            </h5>
        </div>
        <div class="card-body">
            <canvas id="dailyProgressChart" height="120"></canvas>
        </div>
    </div>

    <!-- Recent Surveys Table -->
    <div class="modern-card animate-fadeInUp">
        <div class="card-header-modern">
            <h5 class="mb-0">
                <i class="fas fa-history me-2 text-success"></i>
                Recent Survey Activity
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="data-table table">
                    <thead>
                        <tr>
                            <th>GIS ID</th>
                            <th>Building Name</th>
                            <th>Road Name</th>
                            <th>Type</th>
                            <th>Progress</th>
                            <th>Surveyed At</th>
                         </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentSurveys ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $survey): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><code class="bg-light px-2 py-1 rounded"><?php echo e($survey->gisid); ?></code></td>
                            <td class="fw-semibold"><?php echo e($survey->building_name ?? 'N/A'); ?></td>
                            <td><?php echo e($survey->road_name ?? 'N/A'); ?></td>
                            <td><span class="badge-modern bg-secondary bg-opacity-10 text-dark"><?php echo e($survey->building_type ?? 'N/A'); ?></span></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress-modern flex-grow-1" style="width: 100px;">
                                        <div class="progress-bar-modern bg-primary" style="width: <?php echo e($survey->percentage ?? 0); ?>%"></div>
                                    </div>
                                    <small class="fw-bold"><?php echo e($survey->percentage ?? 0); ?>%</small>
                                </div>
                            </td>
                            <td><i class="far fa-clock me-1 text-muted"></i><?php echo e(\Carbon\Carbon::parse($survey->updated_at)->format('d-m-Y H:i')); ?></td>
                         </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-database fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No survey records found</p>
                            </td>
                         </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    $(document).ready(function() {
        // Building Usage Chart
        const usageLabels = <?php echo json_encode($buildingUsageStats->pluck('building_usage'), 15, 512) ?>;
        const usageCounts = <?php echo json_encode($buildingUsageStats->pluck('count'), 15, 512) ?>;

        if (usageLabels.length > 0) {
            new Chart(document.getElementById('usageChart'), {
                type: 'doughnut',
                data: {
                    labels: usageLabels,
                    datasets: [{
                        data: usageCounts,
                        backgroundColor: ['#667eea', '#764ba2', '#28a745', '#ffc107', '#dc3545', '#17a2b8', '#6c757d'],
                        borderWidth: 0,
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    },
                    cutout: '60%'
                }
            });
        }

        // Construction Type Chart
        const constructionLabels = <?php echo json_encode($constructionTypeStats->pluck('construction_type'), 15, 512) ?>;
        const constructionCounts = <?php echo json_encode($constructionTypeStats->pluck('count'), 15, 512) ?>;

        if (constructionLabels.length > 0) {
            new Chart(document.getElementById('constructionChart'), {
                type: 'bar',
                data: {
                    labels: constructionLabels,
                    datasets: [{
                        label: 'Number of Buildings',
                        data: constructionCounts,
                        backgroundColor: 'rgba(40, 167, 69, 0.8)',
                        borderRadius: 8,
                        barPercentage: 0.7,
                        categoryPercentage: 0.8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `Buildings: ${context.parsed.y}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5] },
                            title: { display: true, text: 'Number of Buildings', font: { size: 12 } }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: 'Construction Type', font: { size: 12 } }
                        }
                    }
                }
            });
        }

        // Daily Progress Chart
        const dailyData = <?php echo json_encode($dailyProgress ?? [], 15, 512) ?>;
        if (dailyData.length > 0) {
            const dates = dailyData.map(item => item.date).reverse();
            const buildings = dailyData.map(item => item.buildings_surveyed).reverse();
            const points = dailyData.map(item => item.points_surveyed).reverse();

            new Chart(document.getElementById('dailyProgressChart'), {
                type: 'line',
                data: {
                    labels: dates,
                    datasets: [
                        {
                            label: 'Buildings Surveyed',
                            data: buildings,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#667eea'
                        },
                        {
                            label: 'Bills Surveyed',
                            data: points,
                            borderColor: '#28a745',
                            backgroundColor: 'rgba(40, 167, 69, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            pointBackgroundColor: '#28a745'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { usePointStyle: true, padding: 15 }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.parsed.y}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [5, 5] },
                            title: { display: true, text: 'Number Surveyed', font: { size: 12 } }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: 'Date', font: { size: 12 } }
                        }
                    }
                }
            });
        }
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.surveyor-layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\testinggayu\resources\views/surveyor/progress.blade.php ENDPATH**/ ?>