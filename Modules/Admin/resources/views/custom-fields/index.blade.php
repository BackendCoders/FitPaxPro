<x-app-layout title="Dynamic Fields | FitPaxPro">
    @push('styles')
    <style>
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; margin-bottom: 2px; text-transform: uppercase; font-size: 1.4rem; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.4px; }

        .field-card { 
            background: #121418; border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 20px; padding: 22px; transition: 0.3s; position: relative; overflow: hidden; height: 100%;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .field-card:hover { border-color: rgba(225,18,24,0.3); transform: translateY(-3px); }
        
        .field-badge { 
            background: rgba(225,18,24,0.1); color: #E11218; font-size: 0.6rem; font-weight: 900; 
            padding: 4px 10px; border-radius: 50px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; display: inline-block;
        }
        
        .field-title { font-weight: 800; color: #fff; font-size: 1.1rem; margin-bottom: 5px; text-transform: uppercase; }
        .field-model { color: rgba(255,255,255,0.4); font-size: 0.7rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }

        .field-meta { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 0.75rem; color: rgba(255,255,255,0.5); }
        .field-meta iconify-icon { color: #E11218; font-size: 1rem; }

        .field-footer { 
            border-top: 1px solid rgba(255,255,255,0.03); padding-top: 15px; margin-top: 20px;
            display: flex; align-items: center; justify-content: space-between;
        }
        
        .btn-action { 
            width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.05); color: #fff; display: flex; align-items: center; justify-content: center; transition: 0.2s;
        }
        .btn-action:hover { background: #E11218; color: white; border-color: #E11218; }
        .btn-action.btn-edit:hover { background: #3085d6; border-color: #3085d6; }

        .stealth-switch { width: 40px; height: 20px; cursor: pointer; background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
        .stealth-switch:checked { background-color: #E11218; border-color: #E11218; box-shadow: 0 0 10px rgba(225,18,24,0.3); }
    </style>
    @endpush

    <div class="directory-wrapper">
        <div class="row align-items-center mb-4">
            <div class="col-8">
                <div class="page-header">
                    <h4>Dynamic Field Architect</h4>
                    <p>INJECTING CUSTOM DATA NODES INTO CORE ENTITIES</p>
                </div>
            </div>
            <div class="col-4 text-end">
                <a href="{{ route('admin.custom-fields.create') }}" class="btn btn-primary px-3 py-2 rounded-2 shadow-none fw-bold fs-10 text-uppercase letter-spacing-1">
                    <iconify-icon icon="tabler:plus" class="me-1 align-middle"></iconify-icon> Initialize New Field
                </a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($customFields as $field)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="field-card">
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="field-badge">{{ $field->type }}</span>
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input stealth-switch status-toggle" type="checkbox" 
                                    data-url="{{ route('admin.custom-fields.toggle-status', $field->id) }}"
                                    {{ $field->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        
                        <h5 class="field-title">{{ $field->label }}</h5>
                        <div class="field-model">
                            Target: {{ class_basename($field->model_type) }}
                        </div>

                        <div class="field-meta mt-3">
                            <div class="field-meta">
                                <iconify-icon icon="tabler:code"></iconify-icon>
                                <span>Key: <code>{{ $field->name }}</code></span>
                            </div>
                            <div class="field-meta">
                                <iconify-icon icon="tabler:shield-check"></iconify-icon>
                                <span>{{ $field->is_required ? 'Mandatory' : 'Optional' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="field-footer">
                        <div class="fs-10 text-white-30 fw-bold uppercase letter-spacing-1">
                            Order: {{ $field->order }}
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.custom-fields.edit', $field->id) }}" class="btn-action btn-edit" title="Modify Field">
                                <iconify-icon icon="tabler:edit-circle"></iconify-icon>
                            </a>
                            <form action="{{ route('admin.custom-fields.destroy', $field->id) }}" method="POST" class="delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action" title="Purge Field">
                                    <iconify-icon icon="tabler:trash"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 bg-dark rounded-4 opacity-50">
                    <iconify-icon icon="tabler:layers-off" class="display-3 mb-3"></iconify-icon>
                    <h5 class="text-uppercase letter-spacing-2">No Dynamic Fields</h5>
                    <p class="fs-12 uppercase letter-spacing-1">Start by adding custom data points to your gyms or membership plans.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.status-toggle').on('change', function() {
                const $self = $(this);
                const url = $self.data('url');
                const isChecked = $self.is(':checked');
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data.success) {
                            if(data.is_active) {
                                toastr.success(data.message);
                            } else {
                                toastr.warning(data.message);
                            }
                        } else {
                            $self.prop('checked', !isChecked);
                            toastr.error(data.message || 'Mission Critical Error');
                        }
                    },
                    error: function(xhr) {
                        $self.prop('checked', !isChecked);
                        toastr.error(xhr.responseJSON?.message || 'System Link Failure');
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
