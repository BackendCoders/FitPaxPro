<x-app-layout title="Gym Registration | FitPaxPro">
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
        
        /* Compact Header */
        .dash-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .dash-title h4 { font-weight: 800; letter-spacing: -1px; margin: 0; }
        .dash-title span { color: var(--cc-muted); font-size: 0.85rem; }

        /* Compact Cards */
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

        /* Compact Inputs */
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
        
        #gallery-preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; }
        .gallery-preview-item { width: 60px; height: 60px; border-radius: 8px; overflow: hidden; border: 1px solid var(--cc-border); }
        .gallery-preview-item img { width: 100%; height: 100%; object-fit: cover; }
        .form-control:focus, .form-select:focus { 
            background: #23282e; border-color: var(--cc-accent); box-shadow: 0 0 0 3px rgba(225, 18, 24, 0.1); 
            color: #fff; outline: none;
        }
        textarea.form-control { height: auto; }

        /* Plan Selection Boxes */
        .compact-plan { 
            background: var(--cc-input); border: 1px solid var(--cc-border); 
            border-radius: 10px; padding: 12px 15px; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; gap: 12px;
        }
        .compact-plan:hover { border-color: rgba(255,255,255,0.15); }
        .compact-plan.selected { border-color: var(--cc-accent); background: rgba(225, 18, 24, 0.05); }
        .compact-plan.selected .select-indicator { display: block !important; }
        .compact-plan h6 { font-size: 0.85rem; margin: 0; font-weight: 600; }
        .compact-plan small { font-size: 0.75rem; color: var(--cc-muted); }

        /* Custom Plan Area */
        .custom-plan-row { 
            background: rgba(255,255,255,0.02); border: 1px dotted var(--cc-border);
            border-radius: 10px; padding: 12px; margin-bottom: 10px; position: relative;
        }
        .btn-remove { position: absolute; right: -8px; top: -8px; background: #E11218; color: white; border-radius: 50%; width: 18px; height: 18px; display: flex; align-items: center; justify-content: center; font-size: 10px; border: none; }

        /* Action Bar */
        .bottom-actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 20px; margin-bottom: 50px; }
        .btn-publish { background: var(--cc-accent); border: none; color: white; font-weight: 700; padding: 10px 30px; border-radius: 8px; font-size: 0.9rem; box-shadow: 0 4px 15px rgba(225,18,24,0.3); }
        .btn-publish:hover { background: #ff1a22; transform: translateY(-1px); }
        .btn-cancel { background: transparent; border: 1px solid var(--cc-border); color: var(--cc-muted); font-weight: 600; padding: 10px 20px; border-radius: 8px; font-size: 0.9rem; }
        .btn-cancel:hover { color: #fff; border-color: #fff; }

        .neon-switch { width: 34px; height: 18px; }
    </style>
    @endpush

    <div class="cc-wrapper">
        <div class="dash-header">
            <div class="dash-title">
                <h4>Gym Registration</h4>
                <span>Centralized Command Node Setup</span>
            </div>
            <a href="{{ route('gym.index') }}" class="btn-cancel text-decoration-none">Back to Directory</a>
        </div>

        <form action="{{ route('gym.store') }}" method="POST" id="gym-config-form" enctype="multipart/form-data">
            @csrf
            
            <div class="row g-4">
                <!-- Column 1: Core Details -->
                <div class="col-lg-7">
                    <!-- Basic Info -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:info-circle"></iconify-icon>
                            <h6>Commercial Identity</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Commercial Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Iron Force Elite" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">System Email</label>
                                    <input type="email" name="email" class="form-control" placeholder="hq@gym.com" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" name="phone" class="form-control" placeholder="+1 (234) 567-890" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Physical Address</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Street, Building, City..." required></textarea>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label">Service Categories</label>
                                    <div class="row g-2">
                                        @foreach($categories as $category)
                                        <div class="col-6 col-md-4">
                                            <div class="compact-plan" onclick="toggleCategory('{{ $category->id }}')">
                                                <input class="form-check-input neon-switch" type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="cat_{{ $category->id }}">
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

                    <!-- Media Hub (User-Friendly) -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:photo-up"></iconify-icon>
                            <h6>Media Intelligence Hub</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Primary Cover Asset</label>
                                    <div class="media-upload-box" onclick="document.getElementById('img-main').click()">
                                        <iconify-icon icon="tabler:cloud-upload" class="fs-24 mb-2"></iconify-icon>
                                        <h6 class="fs-12 fw-bold text-white mb-1">SELECT COVER IMAGE</h6>
                                        <p class="fs-10 text-white-50 mb-0 px-3">High-res outdoor or main hall shot recommended</p>
                                        <input type="file" name="image" id="img-main" class="d-none" accept="image/*" required onchange="updatePreview(this, 'preview-main')">
                                        <div id="preview-main" class="upload-preview d-none"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Location Gallery</label>
                                    <div class="media-upload-box" onclick="document.getElementById('img-gallery').click()">
                                        <iconify-icon icon="tabler:layers-plus" class="fs-24 mb-2"></iconify-icon>
                                        <h6 class="fs-12 fw-bold text-white mb-1">UPLOAD PORTFOLIO</h6>
                                        <p class="fs-10 text-white-50 mb-0 px-3">Select multiple shots to showcase interior atmosphere</p>
                                        <input type="file" name="gallery[]" id="img-gallery" class="d-none" accept="image/*" multiple onchange="updateGalleryPreview(this)">
                                    </div>
                                </div>
                                <div class="col-12" id="gallery-preview-container">
                                    <!-- Dynamic Gallery Previews -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Geo & Capacity -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:parameters"></iconify-icon>
                            <h6>Technical Parameters</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Occupancy Limit</label>
                                    <input type="number" name="member_count_limit" class="form-control" placeholder="Seats">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Latitude</label>
                                    <input type="text" name="latitude" class="form-control" placeholder="40.71">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Longitude</label>
                                    <input type="text" name="longitude" class="form-control" placeholder="-74.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <x-dynamic-fields model-type="App\Models\Gym" />
                </div>

                <!-- Column 2: Subscription & Plans -->
                <div class="col-lg-5">
                    <!-- Platform Tier -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:crown"></iconify-icon>
                            <h6>Platform Master Subscription</h6>
                        </div>
                        <div class="cc-card-body">
                            <input type="hidden" name="platform_plan_id" id="selected_platform_plan" required>
                            <div class="row g-2">
                                @foreach($platformPlans as $plan)
                                <div class="col-6">
                                    <div class="compact-plan platform-plan-card" onclick="selectPlatformPlan('{{ $plan->id }}')">
                                        <div class="flex-grow-1">
                                            <h6>{{ $plan->name }}</h6>
                                            <small>₹{{ number_format($plan->monthly_price, 0) }}/mo</small>
                                        </div>
                                        <iconify-icon icon="tabler:circle-check" class="select-indicator d-none text-success"></iconify-icon>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Membership Plans -->
                    <div class="cc-card">
                        <div class="cc-card-header d-flex justify-content-between">
                            <div class="d-flex align-items-center gap-2">
                                <iconify-icon icon="tabler:list-check"></iconify-icon>
                                <h6>Membership Strategy</h6>
                            </div>
                            <button type="button" class="btn btn-cc-primary btn-sm p-0 px-2" id="add-custom-plan-btn" style="height: 22px; font-size: 10px;">+ NEW CUSTOM</button>
                        </div>
                        <div class="cc-card-body p-0">
                            <!-- Master Presets -->
                            <div class="p-3 border-bottom border-secondary-subtle">
                                <label class="form-label mb-2">MASTER TEMPLATES</label>
                                <div class="vstack gap-2">
                                    @foreach($templates as $template)
                                    <div class="compact-plan" onclick="togglePlan('{{ $template->id }}')">
                                        <input class="form-check-input neon-switch" type="checkbox" name="template_ids[]" value="{{ $template->id }}" id="plan_{{ $template->id }}">
                                        <div>
                                            <h6>{{ $template->name }}</h6>
                                            <small>₹{{ number_format($template->price, 0) }}</small>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <!-- Custom Injected -->
                            <div class="p-3">
                                <label class="form-label mb-2">CUSTOM UNIQUE PLANS</label>
                                <div id="custom-plans-container">
                                    <!-- Nodes injected here -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Activation -->
                    <div class="cc-card">
                        <div class="cc-card-body">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fs-13 fw-bold">Verified Account Status</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input neon-switch" type="checkbox" name="is_verified" value="1">
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fs-13 fw-bold">Priority Promotion</span>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input neon-switch" type="checkbox" name="is_sponsored" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Global Footer Actions -->
            <div class="bottom-actions">
                <a href="{{ route('gym.index') }}" class="btn-cancel text-decoration-none">Discard Changes</a>
                <button type="submit" class="btn-publish">
                    <iconify-icon icon="tabler:rocket" class="me-1"></iconify-icon> PUBLISH GYM
                </button>
            </div>
        </form>
    </div>

    <!-- Compact Template -->
    <template id="custom-plan-template">
        <div class="custom-plan-row">
            <button type="button" class="btn-remove remove-plan" style="z-index: 10;"><iconify-icon icon="tabler:x"></iconify-icon></button>
            <div class="row g-2">
                <div class="col-8">
                    <input type="text" name="custom_plans[INDEX][name]" class="form-control form-control-sm" placeholder="Plan Name" required>
                </div>
                <div class="col-4">
                    <input type="number" name="custom_plans[INDEX][duration_months]" class="form-control form-control-sm" placeholder="Mo" value="1" required>
                </div>
                <div class="col-7">
                    <input type="text" name="custom_plans[INDEX][tagline]" class="form-control form-control-sm" placeholder="Badge/Sales/Offer Tag">
                </div>
                <div class="col-5">
                    <input type="file" name="custom_plans[INDEX][image]" class="form-control form-control-sm" accept="image/*">
                </div>
                <div class="col-6">
                    <input type="number" name="custom_plans[INDEX][price]" class="form-control form-control-sm" placeholder="Base Price" required>
                </div>
                <div class="col-6">
                    <input type="number" name="custom_plans[INDEX][offer_price]" class="form-control form-control-sm" placeholder="Offer Price">
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
        function togglePlan(id) {
            const cb = document.getElementById('plan_' + id);
            if (event.target !== cb) cb.checked = !cb.checked;
            cb.closest('.compact-plan').classList.toggle('selected', cb.checked);
        }

        function toggleCategory(id) {
            const cb = document.getElementById('cat_' + id);
            if (event.target !== cb) cb.checked = !cb.checked;
            cb.closest('.compact-plan').classList.toggle('selected', cb.checked);
        }

        function selectPlatformPlan(id) {
            document.querySelectorAll('.platform-plan-card').forEach(el => el.classList.remove('selected'));
            document.getElementById('selected_platform_plan').value = id;
            event.currentTarget.classList.add('selected');
        }

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

        function updateGalleryPreview(input) {
            const container = document.getElementById('gallery-preview-container');
            container.innerHTML = '';
            if (input.files) {
                Array.from(input.files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const div = document.createElement('div');
                        div.className = 'gallery-preview-item';
                        div.innerHTML = `<img src="${e.target.result}" />`;
                        container.appendChild(div);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }

        let planIndex = 0;
        const container = document.getElementById('custom-plans-container');
        const template = document.getElementById('custom-plan-template').innerHTML;

        document.getElementById('add-custom-plan-btn').addEventListener('click', () => {
            const html = template.replace(/INDEX/g, planIndex);
            const div = document.createElement('div');
            div.innerHTML = html;
            const node = div.firstElementChild;
            container.appendChild(node);
            node.querySelector('.remove-plan').addEventListener('click', () => node.remove());
            planIndex++;
        });
    </script>
    @endpush
</x-app-layout>
