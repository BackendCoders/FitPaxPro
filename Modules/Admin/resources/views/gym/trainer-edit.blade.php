@extends('admin::components.layouts.master')

@section('title', 'Edit Trainer | FitPaxPro')

@push('styles')
<style>
    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 14px;
    }

    .form-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-field label {
        font-weight: 600;
        color: #0f172a;
        font-size: 0.9rem;
    }

    .form-field input,
    .form-field select {
        width: 100%;
        border: 1px solid rgba(15, 23, 42, 0.18);
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.92rem;
        background: #fff;
        color: #0f172a;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
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

    .btn-mini.primary {
        background: #111827;
        border-color: #111827;
        color: #fff;
    }

    .error-text {
        color: #b91c1c;
        font-size: 0.84rem;
    }
</style>
@endpush

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">Gym Operations</span>
            <h1 class="hero-title">Edit Trainer</h1>
        </article>
    </section>

    <section class="panel-card">
        <form method="POST" action="{{ $formAction }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="form-field">
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name', $trainer->name) }}" required>
                    @error('name')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $trainer->email) }}" required>
                    @error('email')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-field">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="1" @selected((string) old('status', (int) $trainer->status) === '1')>Active</option>
                        <option value="0" @selected((string) old('status', (int) $trainer->status) === '0')>Inactive</option>
                    </select>
                    @error('status')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-mini primary">Update Trainer</button>
                <a href="{{ route('admin.gym.trainers') }}" class="btn-mini">Cancel</a>
            </div>
        </form>
    </section>

    </div>
</div>
@endsection
