<x-app-layout title="Gym Analytics Command Center | FitPaxPro">
    @push('styles')
    <style>
        .analytics-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 25px; height: 100%; transition: 0.3s; }
        .analytics-card:hover { border-color: rgba(225,18,24,0.3); }
        .stat-value { font-size: 28px; font-weight: 800; color: #fff; line-height: 1.2; }
        .stat-label { font-size: 11px; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); font-weight: 600; }
        .stat-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; }
        
        .bg-glass-red { background: rgba(225,18,24,0.1); color: #E11218; }
        .bg-glass-blue { background: rgba(18,124,225,0.1); color: #127ce1; }
        .bg-glass-green { background: rgba(18,225,104,0.1); color: #12e168; }
        .bg-glass-yellow { background: rgba(225,188,18,0.1); color: #e1bc12; }

        .chart-container { position: relative; height: 250px; width: 100%; }
        
        .trend-badge { font-size: 11px; padding: 4px 8px; border-radius: 6px; font-weight: 600; }
        .trend-up { background: rgba(18,225,104,0.1); color: #12e168; }
    </style>
    @endpush

    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Command Center: Analytics</h4>
            <p class="text-white-50 fs-14 mb-0">Performance Overview for <strong>{{ $gym->name }}</strong></p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <div class="btn-group">
                <button class="btn btn-dark border-0 px-3 py-2"><iconify-icon icon="tabler:calendar" class="me-1"></iconify-icon> LAST 30 DAYS</button>
                <button class="btn btn-primary border-0 px-3 py-2"><iconify-icon icon="tabler:download" class="me-1"></iconify-icon> EXPORT REPORT</button>
            </div>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="analytics-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon bg-glass-blue me-3">
                        <iconify-icon icon="tabler:users"></iconify-icon>
                    </div>
                    <div>
                        <div class="stat-label">Active Members</div>
                        <div class="stat-value">{{ number_format($stats['active_members']) }}</div>
                    </div>
                </div>
                <div class="progress bg-dark" style="height: 4px;">
                    <div class="progress-bar bg-primary" style="width: 75%"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="analytics-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon bg-glass-green me-3">
                        <iconify-icon icon="tabler:currency-dollar"></iconify-icon>
                    </div>
                    <div>
                        <div class="stat-label">Total Revenue</div>
                        <div class="stat-value">₹{{ number_format($stats['total_revenue']) }}</div>
                    </div>
                </div>
                <div class="fs-12 text-muted"><span class="trend-badge trend-up">+12.5%</span> vs last month</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="analytics-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon bg-glass-red me-3">
                        <iconify-icon icon="tabler:user-check"></iconify-icon>
                    </div>
                    <div>
                        <div class="stat-label">Daily Attendance</div>
                        <div class="stat-value">{{ $stats['avg_attendance_weekly'] }}</div>
                    </div>
                </div>
                <div class="stat-label fs-10">Average check-ins per day</div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="analytics-card">
                <div class="d-flex align-items-center mb-3">
                    <div class="stat-icon bg-glass-yellow me-3">
                        <iconify-icon icon="tabler:message-report"></iconify-icon>
                    </div>
                    <div>
                        <div class="stat-label">New Enquiries</div>
                        <div class="stat-value">{{ $stats['pending_enquiries'] }}</div>
                    </div>
                </div>
                <div class="stat-label fs-10">Total leads generated</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="analytics-card">
                <h6 class="text-white fw-bold mb-4 uppercase fs-12 letter-spacing-1">Revenue Trajectory</h6>
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="analytics-card">
                <h6 class="text-white fw-bold mb-4 uppercase fs-12 letter-spacing-1">Attendance Trend</h6>
                <div class="chart-container">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: {!! json_encode($revenueTrend->pluck('date')) !!},
                datasets: [{
                    label: 'Revenue (₹)',
                    data: {!! json_encode($revenueTrend->pluck('total')) !!},
                    borderColor: '#E11218',
                    backgroundColor: 'rgba(225, 18, 24, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)' } },
                    x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.4)' } }
                }
            }
        });

        const ctxAttendance = document.getElementById('attendanceChart').getContext('2d');
        new Chart(ctxAttendance, {
            type: 'bar',
            data: {
                labels: {!! json_encode($attendanceTrend->pluck('date')) !!},
                datasets: [{
                    label: 'Check-ins',
                    data: {!! json_encode($attendanceTrend->pluck('count')) !!},
                    backgroundColor: '#12e168',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: 'rgba(255,255,255,0.4)' } },
                    x: { grid: { display: false }, ticks: { color: 'rgba(255,255,255,0.4)' } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
