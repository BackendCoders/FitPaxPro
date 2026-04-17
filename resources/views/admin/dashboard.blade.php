<x-app-layout title="FitPaxPro Dashboard | Command Center">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts/dist/apexcharts.css">
        <style>
            :root {
                --cc-bg: #111317;
                --cc-border: rgba(255, 255, 255, 0.08);
                --cc-primary: #E11218;
                --cc-success: #10b981;
                --cc-danger: #ef4444;
                --cc-surface: rgba(255, 255, 255, 0.03);
                --cc-text: #ffffff;
                --cc-text-muted: rgba(255, 255, 255, 0.5);
            }
            .content-page { padding: 20px !important; }
            .matrix-card {
                background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
                border: 1px solid var(--cc-border);
                border-radius: 12px;
                padding: 16px;
                transition: transform 0.2s, box-shadow 0.2s;
                position: relative;
                overflow: hidden;
            }
            .matrix-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                border-color: rgba(225, 18, 24, 0.3);
            }
            .matrix-card h6 { 
                font-size: 0.65rem; 
                text-transform: uppercase; 
                letter-spacing: 1px; 
                color: var(--cc-text-muted); 
                margin-bottom: 8px; 
                font-weight: 600; 
            }
            .matrix-card h3 { 
                font-size: 1.5rem; 
                font-weight: 700; 
                color: var(--cc-text); 
                margin-bottom: 0; 
                letter-spacing: -0.5px;
            }
            .matrix-card .trend { 
                font-size: 0.7rem; 
                font-weight: 700; 
                padding: 2px 6px;
                border-radius: 4px;
                background: rgba(255,255,255,0.05);
            }
            
            .pulse-container {
                max-height: 400px;
                overflow-y: auto;
                scrollbar-width: thin;
            }
            .pulse-item {
                padding: 12px 16px;
                border-bottom: 1px solid var(--cc-border);
                transition: background 0.2s;
            }
            .pulse-item:hover { background: rgba(255,255,255,0.02); }
            .pulse-time { color: var(--cc-text-muted); font-size: 0.7rem; font-family: monospace; }
            
            .nav-tabs-custom { border-bottom: 1px solid var(--cc-border) !important; padding: 0 10px; }
            .nav-tabs-custom .nav-link { 
                font-size: 0.75rem; font-weight: 500; color: var(--cc-text-muted); border: none; padding: 12px 16px; 
                transition: all 0.3s;
            }
            .nav-tabs-custom .nav-link.active { 
                color: var(--cc-primary); background: transparent; 
                position: relative;
            }
            .nav-tabs-custom .nav-link.active::after {
                content: ""; position: absolute; bottom: 0; left: 0; width: 100%; height: 2px; background: var(--cc-primary);
            }
            .table-dense th { 
                font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.5px;
                color: var(--cc-text-muted); padding: 12px 16px !important;
                background: rgba(255,255,255,0.02);
            }
            .table-dense td { font-size: 0.8rem; padding: 12px 16px !important; color: rgba(255,255,255,0.8); }
            
            .text-muted { color: var(--cc-text-muted) !important; }
            .premium-status {
                display: inline-flex;
                align-items: center;
                padding: 2px 8px;
                border-radius: 20px;
                font-size: 0.7rem;
                background: rgba(16, 185, 129, 0.1);
                color: var(--cc-success);
            }
        </style>
    @endpush

    <!-- Top Matrix Strip -->
    <div class="row g-2 mb-3">
        <div class="col-md-2">
            <div class="matrix-card">
                <h6>Total Members</h6>
                <div class="d-flex justify-content-between align-items-end">
                    <h3>{{ number_format($stats['total_members']) }}</h3>
                    <span class="trend text-success">+12%</span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="matrix-card">
                <h6>Active Gyms</h6>
                <div class="d-flex justify-content-between align-items-end">
                    <h3>{{ $stats['total_gyms'] }}</h3>
                    <span class="trend text-muted">Stable</span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="matrix-card">
                <h6>Live Sessions</h6>
                <div class="d-flex justify-content-between align-items-end">
                    <h3>{{ $stats['active_sessions'] }}</h3>
                    <span class="trend text-danger">-2%</span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="matrix-card">
                <h6>Revenue (MTD)</h6>
                <div class="d-flex justify-content-between align-items-end">
                    <h3>$14.2k</h3>
                    <span class="trend text-success">+8.4%</span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="matrix-card">
                <h6>Churn Rate</h6>
                <div class="d-flex justify-content-between align-items-end">
                    <h3>1.2%</h3>
                    <span class="trend text-success">-0.5%</span>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="matrix-card bg-primary text-white border-0 shadow-sm" style="background: var(--cc-primary) !important;">
                <h6 class="text-white-50">System Load</h6>
                <div class="d-flex justify-content-between align-items-end">
                    <h3 class="text-white">24%</h3>
                    <span class="trend text-white-50">Optimal</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Middle Pane: Analytics & Live Pulse -->
    <div class="row g-3 mb-3">
        <div class="col-xl-8">
            <div class="card border-1 shadow-none rounded-3" style="border: 1px solid var(--cc-border);">
                <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Analytics Hub <small class="text-muted ms-2">Real-time engagement trends</small></h6>
                    <div class="d-flex gap-2">
                        <button class="btn btn-xs btn-outline-secondary fs-11 px-2 py-1">Export PDF</button>
                    </div>
                </div>
                <div class="card-body">
                    <div id="command-center-chart" style="min-height: 320px;"></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-1 shadow-none rounded-3" style="border: 1px solid var(--cc-border);">
                <div class="card-header bg-transparent py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Live Pulse <span class="badge bg-success-subtle text-success ms-2 fs-10">Live</span></h6>
                </div>
                <div class="card-body p-0">
                    <div class="pulse-container" data-simplebar>
                        <div class="pulse-item">
                            <span class="pulse-time">17:45:02</span>
                            <div class="pulse-dot bg-success"></div>
                            <div class="pulse-content">User <strong>#1204</strong> checked in at <em>Downtown Elite</em></div>
                        </div>
                        <div class="pulse-item">
                            <span class="pulse-time">17:44:11</span>
                            <div class="pulse-dot bg-primary" style="background: var(--cc-primary);"></div>
                            <div class="pulse-content">New membership created: <strong>John Doe</strong></div>
                        </div>
                        <div class="pulse-item">
                            <span class="pulse-time">17:42:58</span>
                            <div class="pulse-dot bg-warning"></div>
                            <div class="pulse-content">Payment processed: <strong>$59.00</strong> (Invoice #882)</div>
                        </div>
                        <div class="pulse-item">
                            <span class="pulse-time">17:41:22</span>
                            <div class="pulse-dot bg-info"></div>
                            <div class="pulse-content">New enquiry recieved from <em>Brooklyn Annex</em></div>
                        </div>
                        @foreach($recentUsers as $user)
                        <div class="pulse-item">
                            <span class="pulse-time">{{ \Carbon\Carbon::parse($user['created_at'])->format('H:i:s') }}</span>
                            <div class="pulse-dot bg-success"></div>
                            <div class="pulse-content">Account created: <strong>{{ $user['name'] }}</strong></div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-light-subtle border-0 py-2 text-center">
                    <a href="#" class="fs-11 fw-bold text-decoration-none">Review Full Audit Logs</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Pane: Advanced Tables -->
    <div class="row g-3">
        <div class="col-12">
            <div class="card border-1 shadow-none rounded-3" style="border: 1px solid var(--cc-border);">
                <div class="card-header bg-transparent p-0 border-0">
                    <ul class="nav nav-tabs nav-tabs-custom border-bottom" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#members-tab" role="tab">Active Members</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#gyms-tab" role="tab">Gym Performance</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#alerts-tab" role="tab">Critical Alerts <span class="badge bg-danger rounded-pill ms-1">2</span></a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-0">
                    <div class="tab-content">
                        <!-- Members Tab -->
                        <div class="tab-pane active" id="members-tab" role="tabpanel">
                            <div class="table-responsive">
                                <table class="table table-dense table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Member Name</th>
                                            <th>Contact Info</th>
                                            <th>Status</th>
                                            <th>Activity</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentUsers as $user)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user['name']) }}&background=6366f1&color=fff" class="rounded-circle me-2" style="width: 24px;">
                                                    <strong>{{ $user['name'] }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $user['email'] }}</td>
                                            <td><span class="premium-status"><iconify-icon icon="tabler:circle-filled" class="me-1 fs-8"></iconify-icon> Online</span></td>
                                            <td><small class="text-muted">Last check-in: {{ \Carbon\Carbon::parse($user['created_at'])->format('H:i') }}</small></td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <button class="btn btn-light"><iconify-icon icon="tabler:settings"></iconify-icon></button>
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Gyms Tab -->
                        <div class="tab-pane" id="gyms-tab" role="tabpanel">
                            <div class="p-4 text-center text-muted">Gym aggregation analysis...</div>
                        </div>

                        <!-- Alerts Tab -->
                        <div class="tab-pane" id="alerts-tab" role="tabpanel">
                            <div class="pulse-container p-2">
                                <div class="alert alert-soft-danger border d-flex align-items-center mb-2 px-3 py-2 fs-12">
                                    <iconify-icon icon="tabler:alert-triangle" class="fs-18 me-2 text-danger"></iconify-icon>
                                    High capacity detected at <strong>Elite Fitness Annex</strong>.
                                </div>
                                <div class="alert alert-soft-warning border d-flex align-items-center px-3 py-2 fs-12">
                                    <iconify-icon icon="tabler:clock-exclamation" class="fs-18 me-2 text-warning"></iconify-icon>
                                    3 Trainer certifications expiring this week.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Health Strip -->
    <div class="d-flex justify-content-between align-items-center mt-3 px-1">
        <div class="d-flex gap-4">
            <div class="status-indicator">
                <span class="pulse-dot bg-success" style="width: 6px; height: 6px; margin-top: 0;"></span>
                <span class="text-muted">Database: Latency 14ms</span>
            </div>
            <div class="status-indicator">
                <span class="pulse-dot bg-success" style="width: 6px; height: 6px; margin-top: 0;"></span>
                <span class="text-muted">API: 99.9% Uptime</span>
            </div>
        </div>
        <div class="text-muted fs-11 fw-700 text-uppercase">
            FITPAXPRO NODE: #US-EAST-01
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
        <script>
            // Main Command Center Multi-Dataset Chart
            var options = {
                series: [{
                    name: 'Check-ins',
                    data: [31, 40, 28, 51, 42, 109, 100, 80, 95, 120]
                }, {
                    name: 'Subscriptions',
                    data: [11, 32, 45, 32, 34, 52, 41, 30, 48, 55]
                }],
                chart: { height: 320, type: 'area', toolbar: { show: false }, zoom: { enabled: false } },
                dataLabels: { enabled: false },
                stroke: { curve: 'smooth', width: 1.5 },
                colors: ['#E11218', '#10b981'],
                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.1, opacityTo: 0.01, stops: [0, 90, 100] } },
                xaxis: { 
                    categories: ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00", "15:00", "16:00", "17:00"],
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    labels: { style: { colors: 'rgba(255,255,255,0.4)', fontSize: '10px' } }
                },
                grid: { borderColor: 'rgba(255,255,255,0.03)', strokeDashArray: 4 },
                theme: { mode: 'dark' },
                tooltip: { theme: 'dark' }
            };
            var chart = new ApexCharts(document.querySelector("#command-center-chart"), options);
            chart.render();
        </script>
    @endpush
</x-app-layout>
