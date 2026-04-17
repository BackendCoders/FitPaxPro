<x-app-layout title="Gym Directory | FitPaxPro">
    @push('styles')
    <style>
        .gym-card { 
            background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; 
            overflow: hidden; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); height: 100%;
            display: flex; flex-direction: column;
        }
        .gym-card:hover { transform: translateY(-5px); border-color: var(--rich-red); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        
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
        .meta-item iconify-icon { color: var(--rich-red); font-size: 1rem; }

        .gym-footer { padding: 15px 20px; background: rgba(0,0,0,0.2); display: flex; justify-content: space-between; align-items: center; }
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

    <!-- Filter Bar (CC Dark Style) -->
    <div class="cc-card p-3 mb-4">
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
                    <option value="">Full Network</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified Nodes</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approval</option>
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
            <div class="gym-card">
                <div class="gym-cover">
                    <img src="{{ $gym->image ? asset('storage/' . $gym->image) : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200' }}" alt="{{ $gym->name }}">
                    <div class="gym-overlay">
                        <div class="d-flex gap-1">
                            @if($gym->is_verified)
                                <span class="gym-badge text-success"><iconify-icon icon="tabler:circle-check-filled" class="me-1"></iconify-icon>VERIFIED</span>
                            @endif
                            @if($gym->is_sponsored)
                                <span class="gym-badge text-warning"><iconify-icon icon="tabler:star-filled" class="me-1"></iconify-icon>PROMOTED</span>
                            @endif
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-dark p-1 rounded-circle border-0 shadow-lg" data-bs-toggle="dropdown" style="background: rgba(0,0,0,0.5); width: 28px; height: 28px; display: flex; align-items: center; justify-content: center;">
                                <iconify-icon icon="tabler:dots-vertical" class="text-white fs-14"></iconify-icon>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark border-0 shadow-lg">
                                <li><a class="dropdown-item" href="{{ route('gym.edit', $gym->id) }}"><iconify-icon icon="tabler:edit" class="me-2"></iconify-icon>Edit Node</a></li>
                                <li><a class="dropdown-item" href="{{ route('gym.media', $gym->id) }}"><iconify-icon icon="tabler:photo" class="me-2"></iconify-icon>Gallery Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('gym.destroy', $gym->id) }}" method="POST" onsubmit="return confirm('Immediately deactivate this location?')">
                                        @csrf @method('DELETE')
                                        <button class="dropdown-item text-danger"><iconify-icon icon="tabler:trash" class="me-2"></iconify-icon>Delete Location</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="gym-content">
                    <h5 class="gym-name">{{ $gym->name }}</h5>
                    <p class="gym-loc"><iconify-icon icon="tabler:map-pin" class="me-1"></iconify-icon>{{ Str::limit($gym->address, 35) }}</p>
                    
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
                    <a href="#" class="btn btn-link p-0 text-white-50 text-decoration-none fs-11 fw-bold">ANALYTICS <iconify-icon icon="tabler:chevron-right" class="ms-1 align-middle"></iconify-icon></a>
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
</x-app-layout>
