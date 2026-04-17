<div class="dynamic-fields-wrapper">
    @if($fields->count() > 0)
        <div class="section-title mt-4 mb-3" style="font-size: 0.8rem; font-weight: 900; color: #E11218; text-transform: uppercase; letter-spacing: 2px; display: flex; align-items: center; gap: 10px;">
            <iconify-icon icon="tabler:database-cog"></iconify-icon> Extended Data Nodes
        </div>
        
        <div class="row g-4 mb-4">
            @foreach($fields as $field)
                @php
                    $value = $model ? $model->getCustomFieldValue($field->name) : $field->default_value;
                    if (in_array($field->type, ['checkbox', 'select']) && is_string($value) && Str::startsWith($value, '[')) {
                        $value = json_decode($value, true);
                    }
                @endphp
                
                <div class="{{ in_array($field->type, ['textarea']) ? 'col-12' : 'col-md-6' }}">
                    <label class="form-label" style="font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.5); margin-bottom: 8px;">
                        {{ $field->label }}
                        @if($field->is_required) <span class="text-danger">*</span> @endif
                    </label>

                    @if($field->type == 'text' || $field->type == 'email' || $field->type == 'number' || $field->type == 'date')
                        <input type="{{ $field->type }}" 
                               name="custom_fields[{{ $field->name }}]" 
                               value="{{ $value }}"
                               class="form-control stealth-input" 
                               placeholder="{{ $field->placeholder }}" 
                               {{ $field->is_required ? 'required' : '' }}>

                    @elseif($field->type == 'textarea')
                        <textarea name="custom_fields[{{ $field->name }}]" 
                                  class="form-control stealth-input" 
                                  rows="3" 
                                  placeholder="{{ $field->placeholder }}" 
                                  {{ $field->is_required ? 'required' : '' }}>{{ $value }}</textarea>

                    @elseif($field->type == 'select')
                        <select name="custom_fields[{{ $field->name }}]" class="form-select stealth-select" {{ $field->is_required ? 'required' : '' }}>
                            <option value="" selected disabled>{{ $field->placeholder ?? 'Select Option' }}</option>
                            @foreach($field->options as $option)
                                <option value="{{ $option }}" {{ $value == $option ? 'selected' : '' }}>{{ $option }}</option>
                            @endforeach
                        </select>

                    @elseif($field->type == 'checkbox')
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            @foreach($field->options as $option)
                                <div class="form-check">
                                    <input class="form-check-input stealth-check" 
                                           type="checkbox" 
                                           name="custom_fields[{{ $field->name }}][]" 
                                           value="{{ $option }}" 
                                           id="check_{{ $field->name }}_{{ $loop->index }}"
                                           {{ is_array($value) && in_array($option, $value) ? 'checked' : '' }}>
                                    <label class="form-check-label fs-12 text-white-50" for="check_{{ $field->name }}_{{ $loop->index }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>

                    @elseif($field->type == 'radio')
                        <div class="d-flex flex-wrap gap-3 mt-1">
                            @foreach($field->options as $option)
                                <div class="form-check">
                                    <input class="form-check-input stealth-check" 
                                           type="radio" 
                                           name="custom_fields[{{ $field->name }}]" 
                                           value="{{ $option }}" 
                                           id="radio_{{ $field->name }}_{{ $loop->index }}"
                                           {{ $value == $option ? 'checked' : '' }}
                                           {{ $field->is_required ? 'required' : '' }}>
                                    <label class="form-check-label fs-12 text-white-50" for="radio_{{ $field->name }}_{{ $loop->index }}">
                                        {{ $option }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if($field->help_text)
                        <small class="text-white-30 d-block mt-1 fs-10 uppercase letter-spacing-1">{{ $field->help_text }}</small>
                    @endif
                </div>
            @endforeach
        </div>

        <style>
            .stealth-input { 
                background: rgba(255,255,255,0.03) !important; border: 1px solid rgba(255,255,255,0.08) !important;
                border-radius: 12px !important; color: #fff !important; padding: 10px 14px !important; font-size: 0.85rem !important; transition: 0.3s;
            }
            .stealth-input:focus { border-color: #E11218 !important; box-shadow: 0 0 15px rgba(225,18,24,0.1) !important; }
            
            .stealth-select { 
                background: rgba(255,255,255,0.03) !important; border: 1px solid rgba(255,255,255,0.08) !important;
                border-radius: 12px !important; color: #fff !important; padding: 10px 14px !important; font-size: 0.85rem !important;
            }
            .stealth-check { background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
            .stealth-check:checked { background-color: #E11218; border-color: #E11218; }
        </style>
    @endif
</div>