@extends('admin::components.layouts.master')

@section('title', 'View Fee Plan | FitPaxPro')

@push('styles')
<style>
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

    .detail-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-table th,
    .detail-table td {
        padding: 12px 14px;
        border-bottom: 1px solid rgba(17, 24, 39, 0.08);
        text-align: left;
        vertical-align: top;
    }

    .detail-table th {
        width: 280px;
        font-size: 0.84rem;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--brand-muted);
        background: #f8fafc;
    }

    .action-row {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
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
        padding: 8px 12px;
        cursor: pointer;
        text-decoration: none;
    }

    .btn-mini.success {
        color: #065f46;
        border-color: rgba(5, 150, 105, .28);
        background: rgba(236, 253, 245, .88);
    }

    .btn-mini.danger {
        color: #b91c1c;
        border-color: rgba(185, 28, 28, .24);
        background: rgba(254, 242, 242, .88);
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
            <h1 class="hero-title">Fee Plan Details</h1>
            <p style="max-width: 720px; color: rgba(248, 250, 252, 0.84); margin-top: 10px;">
                Gym: {{ optional($feePlan->gym)->name ?: 'N/A' }}
            </p>
        </article>

        <div class="side-stack">
            <article class="surface-card">
                <div class="section-title">Approval</div>
                <div class="insight-list">
                    <div class="chat-item">
                        <strong><span class="status-pill {{ $approvalState['state'] }}">{{ $approvalState['label'] }}</span></strong>
                        <div class="muted">Current approval state</div>
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

        <div class="action-row" style="margin-bottom: 14px;">
            <a class="btn-mini" href="{{ route('admin.gym.fee-plans') }}">Back to Fee Plans</a>

            @if ($canModerateFeePlan)
                <form method="POST" action="{{ route('admin.gym.fee-plans.approve', $feePlan) }}">
                    @csrf
                    <button class="btn-mini success" type="submit">Approve</button>
                </form>

                <form method="POST" action="{{ route('admin.gym.fee-plans.disapprove', $feePlan) }}">
                    @csrf
                    <button class="btn-mini danger" type="submit">Disapprove</button>
                </form>
            @endif
        </div>

        <table class="detail-table">
            <tbody>
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
                    <tr>
                        <th>{{ str_replace('_', ' ', $column) }}</th>
                        <td>{{ $rendered }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    </div>
</div>
@endsection
