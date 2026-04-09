@extends('admin::components.layouts.master')

@section('title', 'Gallery Media Details | FitPaxPro')

@push('styles')
<style>
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
        gap: 10px;
        align-items: center;
        margin-bottom: 14px;
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
</style>
@endpush

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">Gym Operations</span>
            <h1 class="hero-title">Gallery Media Details</h1>
        </article>
    </section>

    <section class="panel-card">
        <div class="action-row">
            <a class="btn-mini" href="{{ route('admin.gym.gallery-media') }}">Back to Gallery Media</a>
        </div>

        <table class="detail-table">
            <tbody>
                @foreach ($galleryColumns as $column)
                    @php
                        $value = data_get($galleryMedia, $column);
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
