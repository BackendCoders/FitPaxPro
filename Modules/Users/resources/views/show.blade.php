<x-app-layout title="User Intelligence Report | FitPaxPro">
    @push('styles')
    <style>
        .profile-header { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; padding: 40px; margin-bottom: 30px; position: relative; overflow: hidden; }
        .profile-header::after { content: ''; position: absolute; top:0; right:0; width: 300px; height: 100%; background: linear-gradient(to left, rgba(225,18,24,0.05), transparent); pointer-events: none; }
        .hero-avatar { width: 120px; height: 120px; border-radius: 30px; object-fit: cover; border: 4px solid #E11218; box-shadow: 0 15px 40px rgba(225,18,24,0.2); }
        
        .stat-box { background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; height: 100%; }
        .stat-value { font-size: 24px; font-weight: 900; color: #fff; margin-bottom: 2px; }
        .stat-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); }

        .activity-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 25px; margin-bottom: 20px; }
        .activity-item { display: flex; align-items: start; gap: 15px; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.03); margin-bottom: 20px; }
        .activity-item:last-child { border: 0; padding: 0; margin: 0; }
        .activity-icon { width: 40px; height: 40px; border-radius: 12px; background: rgba(225,18,24,0.1); color: #E11218; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    </style>
    @endpush

    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Intelligence Report</h4>
            <p class="text-white-50 fs-14 mb-0">Operational profile analysis: <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('users.index') }}" class="btn btn-dark px-4 py-2 border-0 shadow-lg" style="background: #16191d;">
                <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> EXIT ENCRYPTED VIEW
            </a>
        </div>
    </div>

    <div class="profile-header shadow-2xl">
        <div class="row align-items-center">
            <div class="col-lg-auto text-center text-lg-start mb-4 mb-lg-0">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=E11218&color=fff&size=200" class="hero-avatar">
            </div>
            <div class="col-lg ps-lg-5">
                <div class="d-flex flex-wrap align-items-center gap-3 mb-2 justify-content-center justify-content-lg-start">
                    <h2 class="text-white fw-900 mb-0">{{ $user->name }}</h2>
                    @foreach($user->roles as $role)
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-10 fw-bold uppercase letter-spacing-1">{{ $role->name }}</span>
                    @endforeach
                </div>
                <p class="text-white-50 mb-4 text-center text-lg-start">{{ $user->email }} | Deployment ID: <code>{{ $user->id }}</code></p>
                
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="stat-box text-center text-lg-start">
                            <div class="stat-value text-primary">{{ $user->gymSubscriptions->count() }}</div>
                            <div class="stat-label">Active Protocols</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-box text-center text-lg-start">
                            <div class="stat-value text-success">{{ $user->attendanceLogs->count() }}</div>
                            <div class="stat-label">Verified Check-ins</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-box text-center text-lg-start">
                            <div class="stat-value text-warning">{{ $user->ownedGyms->count() }}</div>
                            <div class="stat-label">Controlled Nodes</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-box text-center text-lg-start">
                            <div class="stat-value text-info">{{ $user->created_at->diffInDays(now()) }}d</div>
                            <div class="stat-label">Service Life</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-auto mt-4 mt-lg-0">
                <a href="{{ route('users.edit', $user->id) }}" class="btn btn-primary px-5 py-3 rounded-4 fw-bold shadow-lg">CALIBRATE PARAMETERS</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="activity-card shadow-lg">
                <h6 class="text-white fw-bold mb-4 uppercase fs-12 letter-spacing-2">Subscription History</h6>
                @forelse($user->gymSubscriptions as $sub)
                <div class="activity-item">
                    <div class="activity-icon">
                        <iconify-icon icon="tabler:credit-card"></iconify-icon>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-white mb-0 fs-15">{{ $sub->feePlan->name ?? 'SYSTEM_PROTOCOL' }}</h6>
                            <span class="badge bg-dark border border-secondary border-opacity-10 text-success fs-10">₹{{ number_format($sub->amount_paid) }}</span>
                        </div>
                        <p class="text-white-50 fs-12 mb-0">Active from {{ $sub->start_date->format('d M Y') }} to {{ $sub->end_date->format('d M Y') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-white-50 fs-13 mb-0">No active subscriptions detected.</p>
                @endforelse
            </div>

            <div class="activity-card shadow-lg">
                <h6 class="text-white fw-bold mb-4 uppercase fs-12 letter-spacing-2">Recent Logistics Logs</h6>
                @forelse($user->attendanceLogs->take(5) as $log)
                <div class="activity-item">
                    <div class="activity-icon bg-info bg-opacity-10 text-info">
                        <iconify-icon icon="tabler:map-pin"></iconify-icon>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="text-white mb-0 fs-15">Check-in at Node {{ substr($log->gym_id, 0, 8) }}</h6>
                            <span class="text-white-30 fs-11">{{ $log->check_in_time->diffForHumans() }}</span>
                        </div>
                        <p class="text-white-50 fs-12 mb-0">System verification: <strong>{{ $log->status }}</strong> | Duration: {{ $log->duration_minutes ?? 'LIVE' }}m</p>
                    </div>
                </div>
                @empty
                <p class="text-white-50 fs-13 mb-0">No recent activity recorded.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-4">
            <div class="activity-card shadow-lg">
                <h6 class="text-white fw-bold mb-4 uppercase fs-12 letter-spacing-2">Controlled Assets</h6>
                @forelse($user->ownedGyms as $gym)
                <div class="mb-3 p-3 bg-dark bg-opacity-30 rounded-3 border border-secondary border-opacity-10">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $gym->image ? asset('storage/'.$gym->image) : 'https://images.unsplash.com/photo-1540497077202-7c8a3999166f?w=100' }}" class="rounded-2" style="width: 45px; height: 45px; object-fit: cover;">
                        <div>
                            <h6 class="text-white mb-0 fs-14">{{ $gym->name }}</h6>
                            <p class="text-white-50 fs-11 mb-0">{{ $gym->address }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <p class="text-white-50 fs-13 mb-0">No administrative nodes owned.</p>
                @endforelse
            </div>

            <div class="activity-card shadow-lg bg-primary bg-opacity-10 border-primary border-opacity-10">
                <h6 class="text-primary fw-bold mb-3 uppercase fs-11 letter-spacing-1">Warning Protocol</h6>
                <p class="text-white-50 fs-12 mb-4">Immediate decommissioning will revoke all encrypted access keys.</p>
                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Initiate node decommissioning protocol?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100 py-2 fs-11 fw-bold">DECOMMISSION NODE</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
