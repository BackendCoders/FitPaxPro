@extends('admin::components.layouts.master')

@section('title', 'Edit Gym | FitPaxPro')

@push('styles')
<style>
    .gym-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .form-field {
        display: grid;
        gap: 8px;
    }

    .form-field label {
        font-weight: 600;
        color: var(--brand-slate);
    }

    .required {
        color: #b91c1c;
    }

    .form-field input,
    .form-field select,
    .form-field textarea {
        width: 100%;
        border: 1px solid rgba(17, 24, 39, 0.16);
        border-radius: 10px;
        padding: 10px 12px;
        background: #fff;
        color: var(--brand-ink);
    }

    .media-source-options {
        display: flex;
        gap: 16px;
        flex-wrap: wrap;
        font-weight: 500;
        color: var(--brand-slate);
    }

    .media-source-options label {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .full-width {
        grid-column: 1 / -1;
    }

    .alert {
        border-radius: 10px;
        border: 1px solid;
        padding: 12px 14px;
        font-size: 0.92rem;
    }

    .alert-danger {
        border-color: rgba(185, 28, 28, .24);
        background: rgba(254, 242, 242, .88);
        color: #991b1b;
    }

    @media (max-width: 900px) {
        .gym-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">Gym Operations</span>
            <h1 class="hero-title">Edit Gym</h1>
            <p style="max-width: 700px; color: rgba(248, 250, 252, 0.84);">
                Update gym details safely using the same existing schema fields and validation rules.
            </p>
        </article>

        <div class="side-stack">
            <article class="surface-card">
                <div class="section-title">Editing</div>
                <div class="insight-list">
                    <div class="chat-item"><strong>Gym ID:</strong> {{ $gym->id }}</div>
                    <div class="chat-item"><strong>Created:</strong> {{ optional($gym->created_at)->format('M d, Y h:i A') ?: 'N/A' }}</div>
                    <div class="chat-item"><strong>Status:</strong> {{ ucfirst((string) $gym->status) }}</div>
                </div>
            </article>
        </div>
    </section>

    @include('admin::gym.partials.form')

    </div>
</div>
@endsection
