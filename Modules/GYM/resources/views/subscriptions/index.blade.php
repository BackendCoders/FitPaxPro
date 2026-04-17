<x-app-layout title="Subscription Hub | FitPaxPro">
    @push('styles')
        <style>
            .page-header h4 {
                font-weight: 900;
                letter-spacing: -1.2px;
                color: #fff;
                margin-bottom: 2px;
                text-transform: uppercase;
                font-size: 1.4rem;
            }

            .page-header p {
                color: rgba(255, 255, 255, 0.3);
                font-size: 0.75rem;
                letter-spacing: 0.4px;
            }

            /* Compact Metric Mesh */
            .metric-mesh {
                background: #121418;
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 16px;
                padding: 18px;
                transition: 0.2s;
                position: relative;
                overflow: hidden;
            }

            .metric-mesh h4 {
                font-weight: 900;
                letter-spacing: -1px;
                margin-top: 5px;
                color: #fff;
                font-size: 1.3rem;
                margin-bottom: 0px;
            }

            .metric-mesh label {
                font-size: 0.6rem;
                font-weight: 800;
                color: rgba(255, 255, 255, 0.3);
                text-transform: uppercase;
                letter-spacing: 1px;
            }

            /* Compact Hub Filter Node */
            .filter-node {
                background: #121418;
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 12px;
                padding: 15px;
                margin-bottom: 20px;
            }

            .hub-input {
                background: #08090b !important;
                border: 1px solid rgba(255, 255, 255, 0.05) !important;
                color: #fff !important;
                border-radius: 8px !important;
                font-size: 0.8rem;
                padding: 8px 12px !important;
                height: 38px !important;
            }

            /* Compact Data Node */
            .data-node {
                background: #121418;
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 20px;
                overflow: hidden;
            }

            .hub-table th {
                background: rgba(255, 255, 255, 0.01);
                color: rgba(255, 255, 255, 0.3);
                font-size: 0.6rem;
                font-weight: 900;
                text-transform: uppercase;
                letter-spacing: 1px;
                padding: 12px 15px;
                border: none;
            }

            .hub-table td {
                padding: 12px 15px;
                border-color: rgba(255, 255, 255, 0.02);
                vertical-align: middle;
            }

            .member-avatar {
                width: 34px;
                height: 34px;
                border-radius: 8px;
                border: 1px solid rgba(255, 255, 255, 0.05);
            }

            .status-badge {
                padding: 4px 8px;
                border-radius: 6px;
                font-size: 0.65rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 0.8px;
                display: inline-flex;
                align-items: center;
                gap: 4px;
            }

            .status-active {
                background: rgba(0, 255, 128, 0.08);
                color: #00ff80;
                border: 1px solid rgba(0, 255, 128, 0.1);
            }

            .status-expired {
                background: rgba(225, 18, 24, 0.08);
                color: #E11218;
                border: 1px solid rgba(225, 18, 24, 0.1);
            }

            .btn-action-hub {
                width: 30px;
                height: 30px;
                border-radius: 8px;
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.04);
                color: #fff;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: 0.2s;
                font-size: 1rem;
            }
        </style>
    @endpush

    <div class="hub-wrapper">
        <div class="row align-items-center mb-4">
            <div class="col-8">
                <div class="page-header">
                    <h4>Subscription Hub</h4>
                    <p>REAL-TIME REVENUE TELEMETRY MESH</p>
                </div>
            </div>
            <div class="col-4 text-end">
                <button
                    class="btn btn-primary px-3 py-2 rounded-2 shadow-none fw-bold fs-10 text-uppercase letter-spacing-1">
                    <iconify-icon icon="tabler:download" class="me-1 align-middle"></iconify-icon> Export Dataset
                </button>
            </div>
        </div>

        <!-- Metric Mesh Grid (Compact) -->
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <div class="metric-mesh">
                    <label>Gross Revenue</label>
                    <h4>₹{{ number_format($subscriptions->sum('amount_paid'), 0) }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-mesh">
                    <label>Active Nodes</label>
                    <h4 class="text-success">{{ $subscriptions->where('status', 'active')->count() }}</h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-mesh">
                    <label>Critical Expiry</label>
                    <h4 class="text-warning">
                        {{ $subscriptions->where('end_date', '<=', now()->addDays(7))->where('status', 'active')->count() }}
                    </h4>
                </div>
            </div>
            <div class="col-md-3">
                <div class="metric-mesh">
                    <label>Monthly Propagation</label>
                    <h4>{{ $subscriptions->where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                </div>
            </div>
        </div>

        <!-- Filter Node (Compact) -->
        <div class="filter-node">
            <form action="" method="GET" class="row gx-2 gy-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-0 pe-0 text-white-30"><iconify-icon
                                icon="tabler:user-search"></iconify-icon></span>
                        <input type="text" class="form-control hub-input" placeholder="Search Member ID...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select hub-input">
                        <option value="">Status: All Nodes</option>
                        <option value="active">Active Tiers</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select hub-input">
                        <option value="">Geographic: All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit"
                        class="btn btn-dark w-100 fw-bold border-secondary border-opacity-10 fs-10 py-2">APPLY</button>
                </div>
            </form>
        </div>

        <!-- Data Node (Compact) -->
        <div class="data-node">
            <div class="table-responsive">
                <table class="table hub-table mb-0">
                    <thead>
                        <tr>
                            <th>Identity</th>
                            <th>Deployment</th>
                            <th>Lifecycle</th>
                            <th>Financial Impact</th>
                            <th>Auth</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscriptions as $sub)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($sub->user->name) }}&background=E11218&color=fff&bold=true"
                                            class="member-avatar">
                                        <div class="ms-2">
                                            <h6 class="mb-0 text-white fw-bold fs-12">{{ $sub->user->name }}</h6>
                                            <div class="fs-9 text-danger fw-bold uppercase letter-spacing-1">
                                                {{ $sub->plan->name ?? 'CUSTOM' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="fs-11 fw-bold text-white-50 uppercase letter-spacing-1">
                                        {{ $sub->gym->name }}</div>
                                </td>
                                <td>
                                    <div class="fs-10 text-white-50">
                                        {{ $sub->start_date->format('d M') }} <span class="text-white-20 mx-1">/</span>
                                        {{ $sub->end_date->format('d M y') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="payment-pill fs-11 fw-bold text-white">
                                        ₹{{ number_format($sub->amount_paid, 0) }}
                                        <iconify-icon icon="tabler:circle-check-filled"
                                            class="{{ $sub->payment_status == 'paid' ? 'text-success' : 'text-white-20' }} fs-12 align-middle ms-1"></iconify-icon>
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'active' => 'status-active',
                                            'expired' => 'status-expired',
                                            'cancelled' => 'status-expired opacity-30'
                                        ][$sub->status] ?? 'status-pending';
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $sub->status }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="#" class="btn-action-hub" title="Telemetry"><iconify-icon
                                                icon="tabler:eye"></iconify-icon></a>
                                        <a href="#" class="btn-action-hub" title="Invoice"><iconify-icon
                                                icon="tabler:receipt"></iconify-icon></a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <iconify-icon icon="tabler:packages" class="fs-32 text-white-10 mb-2"></iconify-icon>
                                    <p class="text-white-30 fs-10 uppercase letter-spacing-1">EMPTY DATASET</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>