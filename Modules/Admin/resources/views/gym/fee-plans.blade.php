@extends('admin::components.layouts.master')

@section('title', 'Gym Fee Plans | FitPaxPro')

@push('styles')
<style>
    .table-wrap {
        overflow: auto;
        border-radius: 14px;
        border: 1px solid rgba(17, 24, 39, 0.12);
        background: #fff;
    }

    .gym-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 1024px;
    }

    .gym-table th,
    .gym-table td {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(17, 24, 39, 0.08);
        text-align: left;
        vertical-align: top;
    }

    .gym-table th {
        font-size: 0.84rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--brand-muted);
        background: #f8fafc;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        font-size: 0.78rem;
        font-weight: 700;
        text-transform: capitalize;
        background: rgba(17, 24, 39, .08);
        color: #111827;
    }

    .status-pill.approved {
        background: rgba(16, 185, 129, .14);
        color: #047857;
    }

    .status-pill.pending {
        background: rgba(245, 158, 11, .18);
        color: #b45309;
    }

    .status-pill.disapproved {
        background: rgba(239, 68, 68, .14);
        color: #b91c1c;
    }

    .action-row {
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .btn-mini {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        border: 1px solid rgba(17, 24, 39, 0.16);
        background: #fff;
        color: #111827;
        font-weight: 600;
        font-size: 0.82rem;
        padding: 7px 10px;
        cursor: pointer;
        text-decoration: none;
    }

    .alert {
        border-radius: 10px;
        border: 1px solid;
        padding: 12px 14px;
        font-size: 0.92rem;
        margin-bottom: 14px;
    }

    .alert-success {
        border-color: rgba(5, 150, 105, .26);
        background: rgba(236, 253, 245, .86);
        color: #065f46;
    }

    .alert-error {
        border-color: rgba(185, 28, 28, .24);
        background: rgba(254, 242, 242, .88);
        color: #b91c1c;
    }
</style>
@endpush

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">Gym Operations</span>
            <h1 class="hero-title">Gym Fee Plans</h1>
        </article>

        <div class="side-stack">
            <article class="surface-card">
                <div class="section-title">Summary</div>
                <div class="insight-list">
                    <div class="chat-item">
                        <strong>{{ $feePlans->total() }}</strong>
                        <div class="muted">Total fee plans</div>
                    </div>
                    <div class="chat-item">
                        <strong>{{ collect($approvalStates)->where('state', 'approved')->count() }}</strong>
                        <div class="muted">Approved on this page</div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="panel-card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        <div class="table-wrap">
            <table class="gym-table">
                <thead>
                    <tr>
                        @foreach ($feePlanColumns as $column)
                            <th>{{ str_replace('_', ' ', $column) }}</th>
                        @endforeach
                        <th>Approval</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feePlans as $feePlan)
                        @php
                            $approval = $approvalStates[$feePlan->getKey()] ?? ['state' => 'pending', 'label' => 'Pending'];
                        @endphp
                        <tr>
                            @foreach ($feePlanColumns as $column)
                                @php
                                    $value = data_get($feePlan, $column);
                                    $rendered = match (true) {
                                        is_null($value) => 'N/A',
                                        is_bool($value) => $value ? 'Yes' : 'No',
                                        $value instanceof \Illuminate\Support\Carbon => $value->format('M d, Y h:i A'),
                                        default => (string) $value,
                                    };
                                @endphp
                                <td title="{{ $rendered }}">{{ \Illuminate\Support\Str::limit($rendered, 80) }}</td>
                            @endforeach
                            <td><span class="status-pill {{ $approval['state'] }}">{{ $approval['label'] }}</span></td>
                            <td>
                                <div class="action-row">
                                    <a class="btn-mini" href="{{ route('admin.gym.fee-plans.view', $feePlan) }}">View</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($feePlanColumns) + 2 }}" class="muted" style="text-align:center; padding: 22px;">No fee plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 14px;">
            {{ $feePlans->links() }}
        </div>
    </section>
    </div>
</div>
@endsection

