@extends('admin::components.layouts.master')

@section('title', 'Edit Enquiry | FitPaxPro')

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
    .form-field textarea,
    .form-field select {
        width: 100%;
        border: 1px solid rgba(15, 23, 42, 0.18);
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.92rem;
        background: #fff;
        color: #0f172a;
    }

    .form-field textarea {
        min-height: 110px;
        resize: vertical;
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
            <h1 class="hero-title">Edit Enquiry</h1>
        </article>
    </section>

    <section class="panel-card">
        <form method="POST" action="{{ $formAction }}">
            @csrf
            @method('PUT')

            <div class="form-grid">
                @foreach ($editableColumns as $column)
                    @php
                        $type = $columnTypes[$column] ?? 'string';
                        $isLongText = in_array($type, ['text'], true);
                        $inputType = match ($type) {
                            'boolean' => 'boolean',
                            'date' => 'date',
                            'datetime', 'timestamp' => 'datetime-local',
                            'integer', 'bigint', 'smallint', 'tinyint', 'mediumint', 'decimal', 'float', 'double' => 'number',
                            default => 'text',
                        };

                        $value = old($column, data_get($enquiry, $column));

                        if (in_array($type, ['datetime', 'timestamp'], true) && $value instanceof \Illuminate\Support\Carbon) {
                            $value = $value->format('Y-m-d\TH:i');
                        }
                    @endphp
                    <div class="form-field">
                        <label for="{{ $column }}">{{ str_replace('_', ' ', $column) }}</label>

                        @if ($inputType === 'boolean')
                            <select id="{{ $column }}" name="{{ $column }}">
                                <option value="">Select</option>
                                <option value="1" @selected((string) $value === '1')>Yes</option>
                                <option value="0" @selected((string) $value === '0')>No</option>
                            </select>
                        @elseif ($isLongText)
                            <textarea id="{{ $column }}" name="{{ $column }}">{{ $value }}</textarea>
                        @else
                            <input
                                id="{{ $column }}"
                                name="{{ $column }}"
                                type="{{ $inputType }}"
                                @if ($inputType === 'number')
                                    step="{{ in_array($type, ['decimal', 'float', 'double'], true) ? '0.01' : '1' }}"
                                @endif
                                value="{{ $value }}"
                            >
                        @endif

                        @error($column)
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                @endforeach
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-mini primary">Update Enquiry</button>
                <a href="{{ route('admin.gym.enquiry') }}" class="btn-mini">Cancel</a>
            </div>
        </form>
    </section>

    </div>
</div>
@endsection
