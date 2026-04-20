<x-app-layout title="Modify Field | FitPaxPro">
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

            .form-card {
                background: #121418;
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 24px;
                padding: 40px;
                position: relative;
                overflow: hidden;
                box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
            }

            .form-label {
                font-size: 0.75rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 1.2px;
                color: rgba(255, 255, 255, 0.5);
                margin-bottom: 12px;
                display: block;
            }

            .stealth-input {
                background: #08090b !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 16px !important;
                color: #fff !important;
                padding: 14px 18px !important;
                font-size: 0.95rem !important;
                transition: 0.3s;
            }

            .stealth-input:focus {
                border-color: #E11218 !important;
                box-shadow: 0 0 20px rgba(225, 18, 24, 0.15) !important;
                background: #000 !important;
            }

            .stealth-select {
                background: #08090b !important;
                border: 1px solid rgba(255, 255, 255, 0.08) !important;
                border-radius: 16px !important;
                color: #fff !important;
                padding: 14px 18px !important;
                font-size: 0.95rem !important;
                height: 56px !important;
            }

            .section-title {
                font-size: 0.85rem;
                font-weight: 900;
                color: #E11218;
                text-transform: uppercase;
                letter-spacing: 2px;
                margin-bottom: 30px;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .section-title::after {
                content: '';
                flex: 1;
                height: 1px;
                background: linear-gradient(to right, rgba(225, 18, 24, 0.2), transparent);
            }

            .stealth-switch {
                width: 44px;
                height: 22px;
                cursor: pointer;
                background-color: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
            }

            .stealth-switch:checked {
                background-color: #E11218;
                border-color: #E11218;
            }

            .type-option {
                background: rgba(255, 255, 255, 0.02);
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 16px;
                padding: 15px;
                cursor: pointer;
                transition: 0.3s;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 8px;
                text-align: center;
                height: 100px;
            }

            .type-option:hover {
                background: rgba(225, 18, 24, 0.05);
                border-color: rgba(225, 18, 24, 0.3);
            }

            .type-option.active {
                background: rgba(225, 18, 24, 0.1);
                border-color: #E11218;
                box-shadow: 0 0 20px rgba(225, 18, 24, 0.1);
            }

            .type-option iconify-icon {
                font-size: 1.5rem;
                color: rgba(255, 255, 255, 0.4);
            }

            .type-option.active iconify-icon {
                color: #E11218;
            }

            .type-option span {
                font-size: 0.7rem;
                font-weight: 800;
                text-transform: uppercase;
                letter-spacing: 1px;
                color: rgba(255, 255, 255, 0.5);
            }

            .type-option.active span {
                color: #fff;
            }

            .btn-update {
                background: linear-gradient(to right, #3085d6, #1b6ca8);
                border: none;
                color: #fff;
                font-weight: 800;
                padding: 16px 40px;
                border-radius: 100px;
                text-transform: uppercase;
                letter-spacing: 1.5px;
                font-size: 0.9rem;
                box-shadow: 0 10px 40px rgba(48, 133, 214, 0.3);
                transition: 0.4s;
            }

            .btn-update:hover {
                transform: translateY(-3px);
                box-shadow: 0 20px 50px rgba(48, 133, 214, 0.5);
            }
        </style>
    @endpush

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="row align-items-center mb-5">
                <div class="col-8">
                    <div class="page-header">
                        <h4>Field Calibration</h4>
                        <p>MODIFYING DATA NODE: {{ $customField->label }}</p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <a href="{{ route('admin.custom-fields.index') }}"
                        class="btn btn-dark px-4 py-2 rounded-pill fs-12 border-secondary border-opacity-10 shadow-none uppercase letter-spacing-1">
                        <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> EXIT
                        CALIBRATION
                    </a>
                </div>
            </div>

            <div class="form-card">
                @if ($errors->any())
                    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger rounded-4 mb-4">
                        <iconify-icon icon="tabler:exclamation-circle" class="me-2"></iconify-icon>
                        <strong>Mission Failure:</strong> Calibration parameters are invalid.
                    </div>
                @endif

                <form action="{{ route('admin.custom-fields.update', $customField->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="section-title"><iconify-icon icon="tabler:atom-2"></iconify-icon> ENTITY MAPPING &
                        IDENTITY</div>

                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label text-white">Display Label</label>
                            <input type="text" name="label" id="fieldLabel"
                                class="form-control stealth-input @error('label') is-invalid @enderror"
                                value="{{ old('label', $customField->label) }}" required autocomplete="off">
                            @error('label') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                            <small class="text-white-30 mt-2 d-block">The user-friendly name displayed in forms.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">System Key (Read Only)</label>
                            <div class="input-group">
                                <span
                                    class="input-group-text bg-transparent border-0 text-white-30 fs-12 uppercase letter-spacing-1 ps-0">KEY_</span>
                                <input type="text" class="form-control stealth-input bg-dark bg-opacity-50" readonly
                                    value="{{ $customField->name }}">
                            </div>
                            <small class="text-white-30 mt-2 d-block">Internal identifier locked after
                                initialization.</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form -label">Target Entity</label>
                            <select name="model_type"
                                class="form-select stealth-select @error('model_type') is-invalid @enderror" required>
                                @foreach($models as $class => $label)
                                    <option value="{{ $class }}" {{ old('model_type', $customField->model_type) == $class ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('model_type') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sequence Order</label>
                            <input type="number" name="order" class="form-control stealth-input"
                                value="{{ old('order', $customField->order) }}" required>
                        </div>
                        <div class="col-md-3 d-flex align-items-end px-4">
                            <div class="form-check form-switch p-0">
                                <label class="form-label d-block mb-2">Required</label>
                                <input class="form-check-input stealth-switch" type="checkbox" name="is_required" {{ old('is_required', $customField->is_required) ? 'checked' : '' }}>
                                <span class="ms-2 fs-10 fw-bold text-white-50 uppercase letter-spacing-1">Mandatory
                                    Node</span>
                            </div>
                        </div>
                    </div>

                    <div class="section-title"><iconify-icon icon="tabler:layers-intersect"></iconify-icon> SELECT TYPE
                        ARCHITECTURE</div>

                    <div class="row g-3 mb-5">
                        @php
                            $typeIcons = [
                                'text' => 'tabler:input-search',
                                'textarea' => 'tabler:blockquote',
                                'number' => 'tabler:hash',
                                'select' => 'tabler:select',
                                'checkbox' => 'tabler:checkbox',
                                'radio' => 'tabler:circle-dot',
                                'date' => 'tabler:calendar-time',
                                'email' => 'tabler:mail',
                            ];
                            $selectedType = old('type', $customField->type);
                        @endphp
                        @foreach($fieldTypes as $val => $text)
                            <div class="col-md-3 col-6">
                                <div class="type-option {{ $selectedType == $val ? 'active' : '' }}"
                                    data-value="{{ $val }}">
                                    <iconify-icon icon="{{ $typeIcons[$val] ?? 'tabler:box' }}"></iconify-icon>
                                    <span>{{ $text }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="type" id="selectedType" value="{{ $selectedType }}">

                    <div id="optionsSection"
                        style="{{ in_array($selectedType, ['select', 'checkbox', 'radio']) ? '' : 'display: none;' }}">
                        <div class="section-title"><iconify-icon icon="tabler:list-details"></iconify-icon> DATA NODE
                            OPTIONS</div>
                        <div class="row mb-5">
                            <div class="col-12">
                                <label class="form-label">Available Choices (Comma Separated)</label>
                                <textarea name="options"
                                    class="form-control stealth-input @error('options') is-invalid @enderror" rows="2"
                                    placeholder="Option 1, Option 2, Option 3">{{ old('options', $customField->options ? implode(', ', $customField->options) : '') }}</textarea>
                                @error('options') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="section-title"><iconify-icon icon="tabler:shield-lock"></iconify-icon> PROTOCOLS & USER
                        INTERFACE</div>
                    <div class="row g-4 mb-5">
                        <div class="col-md-6">
                            <label class="form-label">Validation Logic (Laravel)</label>
                            <input type="text" name="validation_rules" class="form-control stealth-input"
                                value="{{ old('validation_rules', $customField->validation_rules) }}"
                                placeholder="e.g. numeric|min:18">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Default Value</label>
                            <input type="text" name="default_value" class="form-control stealth-input"
                                value="{{ old('default_value', $customField->default_value) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">UI Placeholder</label>
                            <input type="text" name="placeholder" class="form-control stealth-input"
                                value="{{ old('placeholder', $customField->placeholder) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Information Node (Help Text)</label>
                            <input type="text" name="help_text" class="form-control stealth-input"
                                value="{{ old('help_text', $customField->help_text) }}">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center border-top border-white-10 pt-5">
                        <div class="form-check form-switch p-0">
                            <input class="form-check-input stealth-switch" type="checkbox" name="is_active" {{ old('is_active', $customField->is_active) ? 'checked' : '' }} id="statusSwitch">
                            <label
                                class="ms-3 fs-11 fw-900 text-white uppercase letter-spacing-1 cursor-pointer {{ !old('is_active', $customField->is_active) ? 'text-danger' : '' }}"
                                for="statusSwitch">
                                ARCHITECT STATUS: {{ old('is_active', $customField->is_active) ? 'ONLINE' : 'OFFLINE' }}
                            </label>
                        </div>
                        <button type="submit" class="btn-update">
                            SAVE CALIBRATION <iconify-icon icon="tabler:refresh"
                                class="ms-2 align-middle"></iconify-icon>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                // Type Selection
                $('.type-option').on('click', function () {
                    $('.type-option').removeClass('active');
                    $(this).addClass('active');

                    const val = $(this).data('value');
                    $('#selectedType').val(val);

                    if (['select', 'checkbox', 'radio'].includes(val)) {
                        $('#optionsSection').slideDown();
                    } else {
                        $('#optionsSection').slideUp();
                    }
                });

                // Status Text Toggle
                $('#statusSwitch').on('change', function () {
                    const label = $(this).next('label');
                    label.text($(this).is(':checked') ? 'ARCHITECT STATUS: ONLINE' : 'ARCHITECT STATUS: OFFLINE');
                    label.toggleClass('text-danger', !$(this).is(':checked'));
                });
            });
        </script>
    @endpush
</x-app-layout>