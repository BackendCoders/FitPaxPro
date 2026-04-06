@extends('admin::components.layouts.master')

@section('title', 'Manage Gyms | FitPaxPro')

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

    .status-pill.active {
        background: rgba(16, 185, 129, .14);
        color: #047857;
    }

    .status-pill.pending {
        background: rgba(245, 158, 11, .18);
        color: #b45309;
    }

    .status-pill.rejected,
    .status-pill.suspended {
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
</style>
@endpush

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">Gym Operations</span>
            <h1 class="hero-title">Manage Gyms</h1>
            <p style="max-width: 720px; color: rgba(248, 250, 252, 0.84);">
                View, edit, and delete gym records from the existing database table.
            </p>

            <div class="hero-actions" style="margin-top: 20px;">
                <a class="primary-button" href="{{ route('admin.gym.create') }}">Create Gym</a>
            </div>
        </article>

        <div class="side-stack">
            <article class="surface-card">
                <div class="section-title">Summary</div>
                <div class="insight-list">
                    <div class="chat-item">
                        <strong>{{ $gyms->total() }}</strong>
                        <div class="muted">Total gyms</div>
                    </div>
                    <div class="chat-item">
                        <strong>{{ $gyms->where('status', 'active')->count() }}</strong>
                        <div class="muted">Active on this page</div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="panel-card">
        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-wrap">
            <table class="gym-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>City</th>
                        <th>Status</th>
                        <th>Sponsored</th>
                        <th>Verified</th>
                        <th>Rating</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($gyms as $gym)
                        <tr>
                            <td>
                                <strong>{{ $gym->name }}</strong>
                                <div class="muted" style="font-size: .84rem;">{{ $gym->slug }}</div>
                            </td>
                            <td>
                                <div>{{ optional($gym->owner)->name ?: 'N/A' }}</div>
                                <div class="muted" style="font-size: .82rem;">{{ optional($gym->owner)->email }}</div>
                            </td>
                            <td>{{ $gym->city ?: 'N/A' }}</td>
                            <td><span class="status-pill {{ $gym->status }}">{{ $gym->status }}</span></td>
                            <td>{{ $gym->is_sponsored ? 'Yes' : 'No' }}</td>
                            <td>{{ $gym->is_verified ? 'Yes' : 'No' }}</td>
                            <td>{{ number_format((float) $gym->rating_avg, 2) }}</td>
                            <td>{{ optional($gym->created_at)->format('M d, Y') ?: 'N/A' }}</td>
                            <td>
                                <div class="action-row">
                                    <a class="btn-mini" href="{{ route('admin.gym.edit-item', $gym) }}">Edit</a>
                                    <form method="POST" action="{{ route('admin.gym.destroy', $gym) }}" onsubmit="return confirm('Delete this gym?');">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn-mini danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="muted" style="text-align:center; padding: 22px;">No gyms found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 14px;">
            {{ $gyms->links() }}
        </div>
    </section>

    </div>
</div>
@endsection
