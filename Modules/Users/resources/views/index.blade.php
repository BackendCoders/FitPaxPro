<x-app-layout title="User Directory | FitPaxPro">
    @push('styles')
    <style>
        .user-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; transition: 0.3s; height: 100%; display: flex; flex-direction: column; }
        .user-card:hover { transform: translateY(-5px); border-color: #E11218; box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .user-avatar { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid #E11218; padding: 2px; }
        .user-info h6 { font-weight: 800; color: #fff; margin-bottom: 2px; letter-spacing: -0.5px; }
        .user-info p { color: rgba(255,255,255,0.4); font-size: 0.75rem; margin-bottom: 0; }
        .user-badge { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 4px 10px; border-radius: 100px; }
        .status-active { background: rgba(0,255,100,0.1); color: #00ff64; }
        .status-inactive { background: rgba(255,0,0,0.1); color: #ff0000; }
        
        .user-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px; padding-top: 15px; border-top: 1px solid rgba(255,255,255,0.05); }
        .meta-item { display: flex; align-items: center; gap: 8px; font-size: 0.7rem; color: rgba(255,255,255,0.5); }
        .meta-item iconify-icon { color: #E11218; font-size: 14px; }

        .search-area { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 15px; margin-bottom: 30px; }
    </style>
    @endpush

    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Human Resources</h4>
            <p class="text-white-50 fs-14 mb-0">Managing <strong>{{ $users->total() }}</strong> registered operatives.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <!-- Optional: Create user button can go here -->
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="search-area shadow-lg">
        <form action="{{ route('users.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-lg-5">
                <label class="form-label fs-11 fw-bold text-white-50 mb-1">IDENTIFIER SEARCH</label>
                <div class="input-group">
                    <span class="input-group-text bg-dark border-0 text-white-50"><iconify-icon icon="tabler:search"></iconify-icon></span>
                    <input type="text" name="search" class="form-control bg-dark border-0 text-white shadow-none fs-13" placeholder="Search by name, email or node ID..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-lg-3">
                <label class="form-label fs-11 fw-bold text-white-50 mb-1">OPERATIONAL STATUS</label>
                <select name="status" class="form-select bg-dark border-0 text-white shadow-none fs-13">
                    <option value="">Full Cluster</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Online Nodes</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Offline Nodes</option>
                </select>
            </div>
            <div class="col-lg-4">
                <button type="submit" class="btn btn-primary w-100 fs-13 fw-bold py-2 shadow-none">EXECUTE SCAN</button>
            </div>
        </form>
    </div>

    <div class="row g-4">
        @forelse($users as $user)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="user-card shadow-lg">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E11218&color=fff" class="user-avatar" alt="{{ $user->name }}">
                    <span class="user-badge {{ $user->status ? 'status-active' : 'status-inactive' }}">
                        {{ $user->status ? 'Online' : 'Offline' }}
                    </span>
                </div>
                
                <div class="user-info">
                    <h6>{{ $user->name }}</h6>
                    <p>{{ $user->email }}</p>
                    <div class="mt-2 d-flex flex-wrap gap-1">
                        @foreach($user->roles as $role)
                            <span class="badge bg-dark border border-secondary border-opacity-10 text-white-50 fs-10 fw-bold">{{ strtoupper($role->name) }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="user-meta">
                    <div class="meta-item">
                        <iconify-icon icon="tabler:phone"></iconify-icon>
                        <span>{{ $user->phone ?? 'NO_LINK' }}</span>
                    </div>
                    <div class="meta-item">
                        <iconify-icon icon="tabler:calendar-time"></iconify-icon>
                        <span>{{ $user->created_at ? $user->created_at->format('M Y') : 'N/A' }}</span>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top border-secondary border-opacity-10 d-flex gap-2">
                    <a href="{{ route('users.show', $user->id) }}" class="btn btn-dark w-50 py-2 fs-11 fw-bold border-0" style="background: rgba(255,255,255,0.05);">DASHBOARD</a>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary w-50 py-2 fs-11 fw-bold border-0">CALIBRATE</a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <iconify-icon icon="tabler:users-off" class="display-1 text-white-50 mb-3"></iconify-icon>
            <h5 class="text-white">No nodes detected in current cluster.</h5>
        </div>
        @endforelse
    </div>

    <div class="mt-5">
        {{ $users->links() }}
    </div>
</x-app-layout>
