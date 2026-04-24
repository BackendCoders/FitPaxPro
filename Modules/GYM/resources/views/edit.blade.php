<x-app-layout title="Edit Gym Portfolio | FitPaxPro">
    @push('styles')
    <style>
        :root {
            --cc-bg: #0f1115;
            --cc-card: #16191d;
            --cc-border: rgba(255, 255, 255, 0.05);
            --cc-accent: #E11218;
            --cc-text: #ffffff;
            --cc-muted: #8a8d91;
            --cc-input: #1d2126;
        }

        body { background: var(--cc-bg); color: var(--cc-text); }
        .cc-wrapper { max-width: 1200px; margin: 0 auto; padding: 20px; }
        
        .dash-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .dash-title h4 { font-weight: 800; letter-spacing: -1px; margin: 0; }
        .dash-title span { color: var(--cc-muted); font-size: 0.85rem; }

        .cc-card { 
            background: var(--cc-card); border: 1px solid var(--cc-border); 
            border-radius: 12px; margin-bottom: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .cc-card-header { 
            padding: 15px 20px; border-bottom: 1px solid var(--cc-border); 
            display: flex; align-items: center; gap: 10px;
        }
        .cc-card-header h6 { margin: 0; font-weight: 700; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .cc-card-header iconify-icon { color: var(--cc-accent); font-size: 1.2rem; }
        .cc-card-body { padding: 20px; }

        .form-label { font-size: 0.7rem; font-weight: 700; color: var(--cc-muted); margin-bottom: 6px; display: block; }
        .form-control, .form-select { 
            background: var(--cc-input); border: 1px solid var(--cc-border); 
            color: #fff; border-radius: 8px; font-size: 0.85rem; padding: 10px 15px; height: 42px;
            transition: all 0.2s;
        }

        .media-upload-box {
            border: 2px dashed var(--cc-border); border-radius: 12px; padding: 30px 20px;
            text-align: center; cursor: pointer; transition: 0.3s; background: rgba(255,255,255,0.01);
            position: relative; overflow: hidden;
        }
        .media-upload-box:hover { border-color: var(--cc-accent); background: rgba(225, 18, 24, 0.03); }
        .media-upload-box iconify-icon { color: var(--cc-accent); }
        
        .upload-preview { 
            position: absolute; top:0; left:0; width:100%; height:100%; 
            object-fit: cover; z-index: 5; background: #000;
        }
        .upload-preview img { width: 100%; height: 100%; object-fit: cover; }

        .btn-publish { background: var(--cc-accent); border: none; color: white; font-weight: 700; padding: 10px 30px; border-radius: 8px; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(225,18,24,0.3); }
        .btn-cancel { background: transparent; border: 1px solid var(--cc-border); color: var(--cc-muted); font-weight: 600; padding: 10px 20px; border-radius: 8px; font-size: 0.9rem; }
        
        .neon-switch { width: 34px; height: 18px; }

        /* Compact Selection Boxes */
        .compact-plan { 
            background: var(--cc-input); border: 1px solid var(--cc-border); 
            border-radius: 10px; padding: 12px 15px; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 12px;
        }
        .compact-plan:hover { border-color: rgba(255,255,255,0.15); }
        .compact-plan.selected { border-color: var(--cc-accent); background: rgba(225, 18, 24, 0.05); }
        .compact-plan h6 { font-size: 0.85rem; margin: 0; font-weight: 600; }
        .compact-plan small { font-size: 0.75rem; color: var(--cc-muted); }
    </style>
    @endpush

    <div class="cc-wrapper">
        <div class="dash-header">
            <div class="dash-title">
                <h4>Edit Gym Node</h4>
                <span>Refining parameters for <strong>{{ $gym->name }}</strong></span>
            </div>
            <a href="{{ route('gym.index') }}" class="btn-cancel text-decoration-none">Exit Editor</a>
        </div>

        <form action="{{ route('gym.update', $gym->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:info-circle"></iconify-icon>
                            <h6>Commercial Identity</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Commercial Name</label>
                                    <input type="text" name="name" class="form-control" value="{{ $gym->name }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">System Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ $gym->email }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ $gym->phone }}" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Physical Address</label>
                                    <textarea name="address" class="form-control" rows="2" required>{{ $gym->address }}</textarea>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">Service Categories</label>
                                    <div class="row g-2">
                                        @php $selectedCategories = $gym->categories->pluck('id')->toArray(); @endphp
                                        @foreach($categories as $category)
                                        <div class="col-6 col-md-4">
                                            <div class="compact-plan {{ in_array($category->id, $selectedCategories) ? 'selected' : '' }}" onclick="toggleCategory('{{ $category->id }}')">
                                                <input class="form-check-input neon-switch" type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat_{{ $category->id }}" {{ in_array($category->id, $selectedCategories) ? 'checked' : '' }}>
                                                <div>
                                                    <h6 class="fs-11">{{ $category->name }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:photo-up"></iconify-icon>
                            <h6>Media Intelligence Hub</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Current display cover (Upload new to replace)</label>
                                    <div class="media-upload-box" onclick="document.getElementById('img-main').click()">
                                        <div id="preview-main" class="upload-preview {{ $gym->image ? '' : 'd-none' }}">
                                            @if($gym->image) <img src="{{ asset('storage/' . $gym->image) }}" /> @endif
                                        </div>
                                        <iconify-icon icon="tabler:cloud-upload" class="fs-24 mb-2"></iconify-icon>
                                        <h6 class="fs-12 fw-bold text-white mb-1">REPLACE COVER IMAGE</h6>
                                        <input type="file" name="image" id="img-main" class="d-none" accept="image/*" onchange="updatePreview(this, 'preview-main')">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-dynamic-fields model-type="App\Models\Gym" :model="$gym" />
                </div>

                <div class="col-lg-5">
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:crown"></iconify-icon>
                            <h6>Platform Infrastructure</h6>
                        </div>
                        <div class="cc-card-body">
                            <label class="form-label">Current Tier</label>
                            <select name="platform_plan_id" class="form-select" required disabled>
                                <option value="">{{ $gym->platformPlan->name ?? 'Enterprise Basic' }}</option>
                            </select>
                            <small class="text-white-50 mt-2 d-block">Infrastructure upgrades must be handled via direct billing requests.</small>
                        </div>
                    </div>

                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:settings-bolt"></iconify-icon>
                            <h6>Advanced Settings</h6>
                        </div>
                        <div class="cc-card-body">
                           <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Occupancy Limit</label>
                                    <input type="number" name="member_count_limit" class="form-control" value="{{ $gym->member_count_limit }}">
                                </div>
                                <div class="col-12 border-top border-secondary-subtle pt-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fs-12 fw-bold">Verified Account Status</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input neon-switch" type="checkbox" name="is_verified" value="1" {{ $gym->is_verified ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fs-12 fw-bold">Priority Promotion</span>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input neon-switch" type="checkbox" name="is_sponsored" value="1" {{ $gym->is_sponsored ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                           </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn-publish w-100 py-3">
                            <iconify-icon icon="tabler:device-floppy" class="me-2"></iconify-icon> UPDATE NODE CONFIGURATION
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        function updatePreview(input, previewId) {
            const preview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" />`;
                    preview.classList.remove('d-none');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function toggleCategory(id) {
            const cb = document.getElementById('cat_' + id);
            if (event.target !== cb) cb.checked = !cb.checked;
            cb.closest('.compact-plan').classList.toggle('selected', cb.checked);
        }
    </script>
    @endpush
</x-app-layout>
