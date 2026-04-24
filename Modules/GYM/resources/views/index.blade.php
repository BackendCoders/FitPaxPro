<x-app-layout title="Gym Directory | FitPaxPro">
    @push('styles')
    <style>
        .gym-card { 
            background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; 
            overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%;
            display: flex; flex-direction: column; cursor: pointer;
        }
        .gym-card:hover { transform: translateY(-5px); border-color: #E11218; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        
        .gym-cover { position: relative; height: 180px; width: 100%; overflow: hidden; }
        .gym-cover img { width: 100%; height: 100%; object-fit: cover; transition: 0.5s; }
        .gym-card:hover .gym-cover img { transform: scale(1.1); }
        
        .gym-overlay { 
            position: absolute; top: 12px; left: 12px; right: 12px; 
            display: flex; justify-content: space-between; align-items: center; 
        }
        .gym-badge { 
            background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); padding: 4px 12px; 
            border-radius: 10px; font-size: 0.7rem; font-weight: 700; color: #fff; border: 1px solid rgba(255,255,255,0.1);
        }

        .gym-content { padding: 20px; flex-grow: 1; }
        .gym-name { font-weight: 800; font-size: 1.1rem; color: #fff; margin-bottom: 4px; letter-spacing: -0.5px; }
        .gym-loc { color: rgba(255,255,255,0.5); font-size: 0.8rem; margin-bottom: 12px; }
        
        .gym-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: auto; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); }
        .meta-item { display: flex; align-items: center; gap: 8px; font-size: 0.75rem; color: rgba(255,255,255,0.6); }
        .meta-item iconify-icon { color: #E11218; font-size: 1rem; }

        .gym-footer { padding: 15px 20px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }

        .grayscale { filter: grayscale(1); }
        .opacity-50 { opacity: 0.5; }

        /* Command Modal Styles */
        .command-modal .modal-content { background: #0f1115; border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; overflow: hidden; }
        .command-header { background: linear-gradient(135deg, #E11218 0%, #9a0d11 100%); padding: 30px; color: #fff; }
        .command-node-info { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; opacity: 0.6; }
        
        .action-button { 
            background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; 
            padding: 20px; transition: 0.3s; text-align: left; height: 100%; display: block;
            text-decoration: none !important;
        }
        .action-button:hover { background: rgba(255,255,255,0.02); border-color: #E11218; transform: scale(1.02); }
        .action-button iconify-icon { font-size: 24px; color: #E11218; margin-bottom: 12px; display: block; }
        .action-button h6 { color: #fff; font-weight: 700; margin-bottom: 4px; font-size: 14px; }
        .action-button p { color: rgba(255,255,255,0.4); font-size: 11px; margin-bottom: 0; }

        .btn-command-trigger { 
            background: #E11218; color: #fff; width: 32px; height: 32px; 
            border-radius: 8px; display: flex; align-items: center; justify-content: center;
            border: none; transition: 0.3s;
        }
        .btn-command-trigger:hover { background: #fff; color: #E11218; transform: rotate(90deg); }

        /* Progress Bar Styles */
        .progress-container { margin-bottom: 18px; }
        .progress-label { display: flex; justify-content: space-between; font-size: 9px; font-weight: 800; text-transform: uppercase; margin-bottom: 6px; color: rgba(255,255,255,0.4); letter-spacing: 0.5px; }
        .cc-progress { height: 4px; background: rgba(255,255,255,0.05); border-radius: 2px; overflow: hidden; }
        .cc-progress-bar { height: 100%; background: linear-gradient(to right, #E11218, #ff4d52); transition: 1s ease-in-out; }
    </style>
    @endpush

    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Gym Infrastructure</h4>
            <p class="text-white-50 fs-14 mb-0">Managing {{ $gyms->count() }} centralized fitness nodes.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-none fw-bold">
                <iconify-icon icon="tabler:plus" class="me-1 align-middle"></iconify-icon> NEW LOCATION
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="cc-card p-3 mb-4" style="background: #16191d; border-radius: 16px; border: 1px solid rgba(255,255,255,0.05);">
        <form action="{{ route('gym.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-lg-4">
                <label class="form-label fs-10 fw-bold text-muted mb-1">GLOBAL SEARCH</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-0 text-white-50"><iconify-icon icon="tabler:search"></iconify-icon></span>
                    <input type="text" name="search" class="form-control bg-dark border-0 text-white shadow-none fs-13" placeholder="Identify by name, email or code..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-2">
                <label class="form-label fs-10 fw-bold text-muted mb-1">STATUS</label>
                <select name="status" class="form-select bg-dark border-0 text-white shadow-none fs-13">
                    <option value="">All Systems</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active Nodes</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Offline/Inactive</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Protocol</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified Nodes</option>
                </select>
            </div>
            <div class="col-lg-2">
                <label class="form-label fs-10 fw-bold text-muted mb-1">PLATFORM TIER</label>
                <select name="plan_id" class="form-select bg-dark border-0 text-white shadow-none fs-13">
                    <option value="">All Tiers</option>
                    @foreach($platformPlans as $plan)
                        <option value="{{ $plan->id }}" {{ request('plan_id') == $plan->id ? 'selected' : '' }}>{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-lg-2">
                <div class="form-check form-switch pt-2">
                    <input class="form-check-input neon-switch" type="checkbox" name="sponsored" value="1" id="sw-promoted" {{ request('sponsored') ? 'checked' : '' }}>
                    <label class="form-check-label fs-11 fw-bold text-white-50 ms-2" for="sw-promoted">PROMOTED ONLY</label>
                </div>
            </div>
            <div class="col-lg-2">
                <button type="submit" class="btn btn-primary w-100 fs-13 fw-bold py-2 shadow-none">APPLY FILTERS</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        @forelse($gyms as $gym)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="gym-card" onclick="openCommandCenter('{{ $gym->id }}', '{{ $gym->name }}', '{{ $gym->address }}', '{{ $gym->status }}')">
                <div class="gym-cover {{ $gym->status == 'inactive' ? 'opacity-50 grayscale' : '' }}">
                    <img src="{{ $gym->image ? asset('storage/' . $gym->image) : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200' }}" alt="{{ $gym->name }}">
                    <div class="gym-overlay">
                        <div class="d-flex gap-1">
                            @if($gym->is_verified)
                                <span class="gym-badge text-success"><iconify-icon icon="tabler:circle-check-filled" class="me-1"></iconify-icon>VERIFIED</span>
                            @endif
                            @if($gym->is_sponsored)
                                <span class="gym-badge text-warning"><iconify-icon icon="tabler:star-filled" class="me-1"></iconify-icon>PROMOTED</span>
                            @endif
                            @if($gym->status == 'inactive')
                                <span class="gym-badge text-danger"><iconify-icon icon="tabler:plug-off" class="me-1"></iconify-icon>OFFLINE</span>
                            @endif
                        </div>
                        <button class="btn-command-trigger" onclick="event.stopPropagation(); openCommandCenter('{{ $gym->id }}', '{{ $gym->name }}', '{{ $gym->address }}', '{{ $gym->status }}')">
                            <iconify-icon icon="tabler:bolt"></iconify-icon>
                        </button>
                    </div>
                </div>
                
                <div class="gym-content">
                    <h5 class="gym-name">{{ $gym->name }}</h5>
                    <p class="gym-loc"><iconify-icon icon="tabler:map-pin" class="me-1"></iconify-icon>{{ Str::limit($gym->address, 35) }}</p>
                    
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Intelligence completion</span>
                            <span>{{ $gym->completion_progress['steps_completed'] }}/{{ $gym->completion_progress['total_steps'] }} Steps</span>
                        </div>
                        <div class="cc-progress">
                            <div class="cc-progress-bar" style="width: {{ $gym->completion_progress['percentage'] }}%"></div>
                        </div>
                    </div>

                    <div class="gym-meta">
                        <div class="meta-item">
                            <iconify-icon icon="tabler:users"></iconify-icon>
                            <span>{{ $gym->member_count_limit ?? '∞' }} Active</span>
                        </div>
                        <div class="meta-item">
                            <iconify-icon icon="tabler:crown"></iconify-icon>
                            <span>{{ $gym->platformPlan->name ?? 'Basic' }}</span>
                        </div>
                        <div class="meta-item">
                            <iconify-icon icon="tabler:mail"></iconify-icon>
                            <span>{{ Str::limit($gym->email, 15) }}</span>
                        </div>
                        <div class="meta-item">
                            <iconify-icon icon="tabler:phone"></iconify-icon>
                            <span>{{ $gym->phone }}</span>
                        </div>
                    </div>
                </div>

                <div class="gym-footer">
                    <div class="d-flex align-items-center">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($gym->owner->name ?? 'N') }}&background=E11218&color=fff" class="rounded-circle me-2" style="width: 20px; height: 20px;">
                        <span class="fs-11 text-white-50">{{ Str::words($gym->owner->name ?? 'Admin', 1, '') }}</span>
                    </div>
                    <span class="fs-10 fw-bold text-white-30 uppercase letter-spacing-1">Node Status: Active</span>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="p-5 border border-secondary border-opacity-10 border-dashed rounded-4">
                <iconify-icon icon="tabler:building-off" class="display-1 text-white-50 mb-3"></iconify-icon>
                <h5 class="text-white">Empty Network</h5>
                <p class="text-white-50 mb-4">No fitness locations have been provisioned yet.</p>
                <a href="{{ route('gym.create') }}" class="btn btn-primary px-5">Launch First Gym</a>
            </div>
        </div>
        @endforelse
    </div>

    <div class="mt-5 d-flex justify-content-center">
        {{ $gyms->links() }}
    </div>

    <!-- Command Center Modal -->
    <div class="modal fade command-modal" id="gymCommandCenter" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow-2xl">
                <div class="command-header">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div>
                            <p class="command-node-info mb-1" id="m-gym-node">Node Serial: a19048d0</p>
                            <h3 class="fw-900 text-white mb-1" id="m-gym-name">Iron Force Elite</h3>
                            <p class="text-white-50 fs-13 mb-0" id="m-gym-address">123 Tactical Street, Gym District</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="d-flex gap-2">
                        <span class="badge bg-white bg-opacity-20 rounded-pill px-3 py-2 fs-10 fw-bold uppercase">Operational Status: Online</span>
                        <span class="badge bg-white bg-opacity-20 rounded-pill px-3 py-2 fs-10 fw-bold uppercase" id="m-gym-tag">Verified node</span>
                    </div>
                </div>
                <div class="modal-body p-4 p-lg-5">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="#" id="link-edit" class="action-button">
                                <iconify-icon icon="tabler:adjustments-horizontal"></iconify-icon>
                                <h6>Configuration Edit</h6>
                                <p>Tune location parameters, metadata, and commercial details.</p>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" id="link-members" class="action-button">
                                <iconify-icon icon="tabler:users-group"></iconify-icon>
                                <h6>Member Manifest</h6>
                                <p>Access active operative directory and subscriber intelligence.</p>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" id="link-gallery" class="action-button">
                                <iconify-icon icon="tabler:photo-up"></iconify-icon>
                                <h6>Media Assets Hub</h6>
                                <p>Manage location portfolio, gallery, and primary visual assets.</p>
                            </a>
                        </div>
                        <div class="col-md-6">
                            <a href="#" id="link-analytics" class="action-button">
                                <iconify-icon icon="tabler:brand-google-analytics"></iconify-icon>
                                <h6>Tactical Analytics</h6>
                                <p>Visual performance metrics, revenue trends, and growth logs.</p>
                            </a>
                        </div>
                        <div class="col-12 mt-4 pt-4 border-top border-secondary border-opacity-10">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white mb-1">Operational Transmission</h6>
                                    <p class="text-white-50 fs-11 mb-0">Toggle visibility and signal availability for this node.</p>
                                </div>
                                <form action="#" id="form-toggle-status" method="POST">
                                    @csrf
                                    <button class="btn btn-outline-warning px-4 py-2 fs-11 fw-bold rounded-3" id="btn-status-toggle">TOGGLE SIGNAL</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openCommandCenter(id, name, address, currentStatus) {
            const modal = new bootstrap.Modal(document.getElementById('gymCommandCenter'));
            
            // Populate Modal Data
            document.getElementById('m-gym-node').innerText = 'Node Identifier: ' + id.substring(0, 8);
            document.getElementById('m-gym-name').innerText = name;
            document.getElementById('m-gym-address').innerText = address;
            
            // Update Links
            document.getElementById('link-edit').href = `/gym/${id}/edit`;
            document.getElementById('link-members').href = `/gym/${id}/members`;
            document.getElementById('link-gallery').href = `/gym/${id}/media`;
            document.getElementById('link-analytics').href = `/gym/${id}/analytics`;
            
            // Update Toggle Form
            document.getElementById('form-toggle-status').action = `/gym/${id}/toggle-status`;
            const btnToggle = document.getElementById('btn-status-toggle');
            if (currentStatus === 'inactive') {
                btnToggle.innerText = 'INITIALIZE SIGNAL (ACTIVATE)';
                btnToggle.className = 'btn btn-outline-success px-4 py-2 fs-11 fw-bold rounded-3';
            } else {
                btnToggle.innerText = 'TERMINATE SIGNAL (INACTIVATE)';
                btnToggle.className = 'btn btn-outline-danger px-4 py-2 fs-11 fw-bold rounded-3';
            }
            
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>
