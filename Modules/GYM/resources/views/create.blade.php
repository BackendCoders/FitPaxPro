<x-app-layout title="Premium Gym Registration | FitPaxPro">
    @push('styles')
    <style>
        .form-section-title { font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #6366f1; margin-bottom: 20px; display: flex; align-items: center; gap: 8px; }
        .form-section-title iconify-icon { font-size: 1rem; }
        .card-form { border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); }
        .input-icon-group { position: relative; }
        .input-icon-group iconify-icon { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 1.1rem; }
        .input-icon-group .form-control { padding-left: 40px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.9rem; }
        .input-icon-group .form-control:focus { border-color: #6366f1; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
        
        .plan-repeater-item { background: #f8fafc; border-left: 4px solid #6366f1; border-radius: 8px; padding: 20px; margin-bottom: 15px; position: relative; transition: all 0.2s; }
        .plan-repeater-item:hover { background: #f1f5f9; }
        .remove-plan { position: absolute; top: 10px; right: 10px; color: #ef4444; cursor: pointer; opacity: 0.6; transition: 0.2s; }
        .remove-plan:hover { opacity: 1; transform: scale(1.1); }
        
        .form-label { font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 6px; }
        .btn-add-plan { background: white; border: 2px dashed #e2e8f0; color: #64748b; font-weight: 700; font-size: 0.85rem; padding: 15px; width: 100%; border-radius: 8px; transition: 0.2s; }
        .btn-add-plan:hover { border-color: #6366f1; color: #6366f1; background: #f5f3ff; }
        
        /* Custom Mini Switch */
        .neon-switch { width: 34px; height: 18px; margin-top: 0; }
        .neon-switch:checked { background-color: #6366f1; border-color: #6366f1; }
    </style>
    @endpush

    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-dark fw-bold">Gym Setup Wizard</h4>
            <p class="text-muted fs-14 mb-0">Follow the steps to register a new premium location.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.index') }}" class="btn btn-light px-3 py-2 fs-13 fw-bold rounded-pill text-muted">
                <iconify-icon icon="tabler:x" class="me-1"></iconify-icon> Cancel Setup
            </a>
        </div>
    </div>

    <form action="{{ route('gym.store') }}" method="POST" class="needs-validation" novalidate id="gym-setup-form">
        @csrf
        <div class="row g-4">
            
            <!-- Left Side: Basic & Communication -->
            <div class="col-xl-7">
                <div class="card mb-4 card-form border-0">
                    <div class="card-body p-4">
                        <div class="form-section-title"><iconify-icon icon="tabler:id"></iconify-icon> Brand Identity</div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label">Commercial Name</label>
                                <div class="input-icon-group">
                                    <iconify-icon icon="tabler:building"></iconify-icon>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Iron Forge Gym" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Member Capacity</label>
                                <div class="input-icon-group">
                                    <iconify-icon icon="tabler:users"></iconify-icon>
                                    <input type="number" name="member_count_limit" class="form-control" placeholder="Dynamic">
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title mt-5"><iconify-icon icon="tabler:headset"></iconify-icon> Communication Hub</div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Official Email</label>
                                <div class="input-icon-group">
                                    <iconify-icon icon="tabler:mail"></iconify-icon>
                                    <input type="email" name="email" class="form-control" placeholder="hq@gym.com" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone Hotline</label>
                                <div class="input-icon-group">
                                    <iconify-icon icon="tabler:phone"></iconify-icon>
                                    <input type="text" name="phone" class="form-control" placeholder="+1 (555) 000-0000" required>
                                </div>
                            </div>
                        </div>

                        <div class="form-section-title mt-5"><iconify-icon icon="tabler:map-pin"></iconify-icon> Location Intelligence</div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Full Surface Address</label>
                            <div class="input-icon-group">
                                <iconify-icon icon="tabler:map"></iconify-icon>
                                <textarea name="address" class="form-control" rows="3" placeholder="Enter physical street address..." required></textarea>
                            </div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Latitude</label>
                                <div class="input-icon-group">
                                    <iconify-icon icon="tabler:world-latitude"></iconify-icon>
                                    <input type="text" name="latitude" class="form-control" placeholder="e.g. 40.7128">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Longitude</label>
                                <div class="input-icon-group">
                                    <iconify-icon icon="tabler:world-longitude"></iconify-icon>
                                    <input type="text" name="longitude" class="form-control" placeholder="e.g. -74.0060">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Plans & Settings -->
            <div class="col-xl-5">
                <!-- Dynamic Plans Section -->
                <div class="card mb-4 card-form border-0 shadow-none" style="background: transparent;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="form-section-title mb-0"><iconify-icon icon="tabler:layers-intersect"></iconify-icon> Membership Plans</div>
                        <span class="badge bg-primary rounded-pill px-2 py-1 fs-10" id="plan-count-badge">0 Plans</span>
                    </div>
                    
                    <div id="plans-container">
                        <!-- Plans will be injected here -->
                    </div>

                    <button type="button" class="btn btn-add-plan" id="add-plan-btn">
                        <iconify-icon icon="tabler:plus" class="me-1"></iconify-icon> Create Dynamic Fee Plan
                    </button>
                </div>

                <!-- Global Settings card -->
                <div class="card border-0 card-form bg-white">
                    <div class="card-body p-4">
                        <div class="form-section-title"><iconify-icon icon="tabler:settings-automation"></iconify-icon> Global Settings</div>
                        
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fs-13 fw-bold">Verification Status</h6>
                                <p class="text-muted fs-11 mb-0">Add blue check trust badge to location</p>
                            </div>
                            <div class="form-check form-switch p-0">
                                <input class="form-check-input neon-switch" type="checkbox" name="is_verified" value="1">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0 fs-13 fw-bold">Sponsorship Program</h6>
                                <p class="text-muted fs-11 mb-0">Pin location to the top of discovery</p>
                            </div>
                            <div class="form-check form-switch p-0">
                                <input class="form-check-input neon-switch" type="checkbox" name="is_sponsored" value="1">
                            </div>
                        </div>

                        <hr class="my-4 op-1">
                        
                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold shadow-sm">
                            Publish Gym Location <iconify-icon icon="tabler:rocket" class="ms-1"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Plan Template (Hidden) -->
    <template id="plan-template">
        <div class="plan-repeater-item shadow-sm border">
            <div class="remove-plan"><iconify-icon icon="tabler:circle-x-filled"></iconify-icon></div>
            <div class="row g-2">
                <div class="col-12">
                    <label class="form-label">Plan Label</label>
                    <input type="text" name="plans[INDEX][name]" class="form-control form-control-sm bg-white" placeholder="e.g. Elite Annual" required>
                </div>
                <div class="col-6">
                    <label class="form-label">Base Price ($)</label>
                    <input type="number" name="plans[INDEX][price]" class="form-control form-control-sm bg-white" placeholder="0.00" required step="0.01">
                </div>
                <div class="col-6">
                    <label class="form-label">Offer Price ($)</label>
                    <input type="number" name="plans[INDEX][offer_price]" class="form-control form-control-sm bg-white" placeholder="Optional" step="0.01">
                </div>
                <div class="col-8">
                    <label class="form-label">Duration (Months)</label>
                    <select name="plans[INDEX][duration_months]" class="form-select form-select-sm bg-white">
                        <option value="1">1 Month</option>
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12" selected>12 Months (Annual)</option>
                    </select>
                </div>
                <div class="col-4 d-flex align-items-end justify-content-center pb-2">
                    <div class="form-check form-switch">
                        <input class="form-check-input neon-switch" type="checkbox" name="plans[INDEX][is_active]" value="1" checked>
                        <label class="fs-10 fw-bold text-muted ms-1">Active</label>
                    </div>
                </div>
                <div class="col-12 mt-2 d-flex gap-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="plans[INDEX][includes_trainer]" value="1" id="trainer_INDEX">
                        <label class="fs-11 text-muted" for="trainer_INDEX">Includes Trainer</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="plans[INDEX][includes_diet_plan]" value="1" id="diet_INDEX">
                        <label class="fs-11 text-muted" for="diet_INDEX">Includes Diet Plan</label>
                    </div>
                </div>
            </div>
        </div>
    </template>

    @push('scripts')
    <script>
        let planIndex = 0;
        const plansContainer = document.getElementById('plans-container');
        const planTemplate = document.getElementById('plan-template').innerHTML;
        const planCountBadge = document.getElementById('plan-count-badge');

        function updatePlanCount() {
            const count = plansContainer.children.length;
            planCountBadge.innerText = `${count} ${count === 1 ? 'Plan' : 'Plans'}`;
        }

        document.getElementById('add-plan-btn').addEventListener('click', () => {
            const html = planTemplate.replace(/INDEX/g, planIndex);
            const wrapper = document.createElement('div');
            wrapper.innerHTML = html;
            const newItem = wrapper.firstElementChild;
            
            plansContainer.appendChild(newItem);
            
            newItem.querySelector('.remove-plan').addEventListener('click', () => {
                newItem.remove();
                updatePlanCount();
            });
            
            planIndex++;
            updatePlanCount();
        });

        // Initialize with one plan
        document.getElementById('add-plan-btn').click();

        // Standard validation script
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms).forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }
                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
    @endpush
</x-app-layout>
