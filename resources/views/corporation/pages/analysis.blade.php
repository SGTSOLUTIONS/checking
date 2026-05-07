@extends('layouts.commissioner')

@section('title', 'Analysis - Municipal Corporation')

@section('content')

<div class="animate__animated animate__fadeInUp">
    <h3 class="fw-bold mb-4" style="color:#ffffff;">
        <i class="fas fa-chart-line me-2" style="color:#1679AB;"></i> Analytical Insights
    </h3>

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="stat-card p-4">
                <h5 class="fw-bold mb-3">Monthly Tax Collection Trend</h5>
                <canvas id="taxChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="stat-card p-4">
                <h5 class="fw-bold mb-3">Ward-wise Grievance Resolution</h5>
                <canvas id="grievanceChart" height="250"></canvas>
            </div>
        </div>
        <div class="col-12">
            <div class="stat-card p-4">
                <h5 class="fw-bold mb-3">Corporation Budget Allocation</h5>
                <canvas id="budgetChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<script>
    // Tax Chart
    const taxCtx = document.getElementById('taxChart')?.getContext('2d');
    if (taxCtx) {
        new Chart(taxCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Tax Collection (₹ Crores)',
                    data: [6.2, 7.1, 8.4, 9.2, 10.1, 11.5],
                    borderColor: '#1679AB',
                    backgroundColor: 'rgba(22, 121, 171, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: { responsive: true, maintainAspectRatio: true }
        });
    }

    // Grievance Chart
    const grievanceCtx = document.getElementById('grievanceChart')?.getContext('2d');
    if (grievanceCtx) {
        new Chart(grievanceCtx, {
            type: 'bar',
            data: {
                labels: ['Ward 1-20', 'Ward 21-40', 'Ward 41-60', 'Ward 61-80', 'Ward 81-100'],
                datasets: [{
                    label: 'Resolved Grievances (%)',
                    data: [92, 88, 94, 79, 96],
                    backgroundColor: '#FFB1B1',
                    borderRadius: 8,
                    borderColor: '#1679AB',
                    borderWidth: 1
                }]
            },
            options: { responsive: true, scales: { y: { max: 100 } } }
        });
    }

    // Budget Chart
    const budgetCtx = document.getElementById('budgetChart')?.getContext('2d');
    if (budgetCtx) {
        new Chart(budgetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Infrastructure', 'Sanitation', 'Water Supply', 'Street Lighting', 'Admin'],
                datasets: [{
                    data: [38, 25, 18, 12, 7],
                    backgroundColor: ['#1679AB', '#102C57', '#FFB1B1', '#FFCBCB', '#5A6E7A'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
        });
    }
</script>

@endsection
