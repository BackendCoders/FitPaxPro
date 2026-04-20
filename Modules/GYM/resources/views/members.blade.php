<x-app-layout title="Member Manifest | FitPaxPro">
    @push('styles')
    <style>
        .member-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 15px; margin-bottom: 10px; transition: 0.3s; }
        .member-card:hover { border-color: #E11218; background: rgba(225,18,24,0.02); }
        .member-avatar { width: 45px; height: 45px; border-radius: 10px; object-fit: cover; }
        
        .plan-tag { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; color: #E11218; border: 1px solid rgba(225,18,24,0.3); padding: 2px 8px; border-radius: 4px; }
        .status-pill { width: 8px; height: 8px; border-radius: 50%; display: inline-block; margin-right: 5px; }

        .search-tactical { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 20px; }
        .table-tactical { border-collapse: separate; border-spacing: 0 8px; }
        .table-tactical tr { background: #16191d; transition: 0.2s; }
        .table-tactical tr:hover { background: #1c2126; }
        .table-tactical td { border: none; padding: 15px; vertical-align: middle; }
        .table-tactical td:first-child { border-radius: 12px 0 0 12px; }
        .table-tactical td:last-child { border-radius: 0 12px 12px 0; }
        .table-tactical th { border: none; text-transform: uppercase; font-size: 10px; letter-spacing: 1.5px; color: rgba(255,255,255,0.4); padding: 10px 15px; }
    </style>
    @endpush

    <div class="row align-items-center mb-5">
        <div class="col-sm-6">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-primary bg-opacity-10 rounded-3">
                    <iconify-icon icon="tabler:users-group" class="fs-32 text-primary"></iconify-icon>
                </div>
                <div>
                    <h4 class="mb-1 text-white fw-bold">Member Manifest</h4>
                    <p class="text-white-50 fs-14 mb-0">Subscribed operatives for <strong>{{ $gym->name }}</strong></p>
                </div>
            </div>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.index') }}" class="btn btn-dark px-4 py-2 border-0 shadow-lg" style="background: #16191d;">
                <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> EXIT MANIFEST
            </a>
        </div>
    </div>

    <!-- Analytics Pulse -->
    <div class="row g-3 mb-5">
        <div class="col-md-3">
            <div class="search-tactical text-center">
                <h2 class="text-white fw-900 mb-0">{{ $members->total() }}</h2>
                <p class="text-white-50 fs-10 mb-0 uppercase letter-spacing-1">TOTAL POPULATION</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="search-tactical text-center border-success border-opacity-20">
                <h2 class="text-success fw-900 mb-0">{{ $members->where('status', 'active')->count() }}</h2>
                <p class="text-white-50 fs-10 mb-0 uppercase letter-spacing-1">ACTIVE SIGNALS</p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="search-tactical h-100 d-flex align-items-center">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-0 text-white-30 ps-3"><iconify-icon icon="tabler:search"></iconify-icon></span>
                    <input type="text" class="form-control bg-dark border-0 text-white shadow-none fs-13 py-2" placeholder="Scan by name, email or operative ID...">
                    <button class="btn btn-primary px-4 fw-bold fs-12">EXECUTE SCAN</button>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-tactical">
            <thead>
                <tr>
                    <th>OPERATIVE</th>
                    <th>CLEARANCE (PLAN)</th>
                    <th>ENCRYPTION (EMAIL/PHONE)</th>
                    <th>LOGISTICS (DATES)</th>
                    <th>STATUS</th>
                    <th class="text-end">PROTOCOLS</th>
                </tr>
            </thead>
            <tbody>
                @forelse($members as $member)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($member->user->name) }}&background=E11218&color=fff" class="member-avatar shadow">
                            <div>
                                <h6 class="text-white mb-0 fs-14 fw-bold">{{ $member->user->name }}</h6>
                                <code class="fs-10 text-white-30">{{ substr($member->user->id, 0, 8) }}..</code>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="plan-tag">{{ $member->feePlan->name ?? 'UNKNOWN_TIER' }}</span>
                        <div class="mt-1 fs-10 text-white-50">₹{{ number_format($member->amount_paid) }} Collected</div>
                    </td>
                    <td>
                        <div class="fs-12 text-white-80">{{ $member->user->email }}</div>
                        <div class="fs-11 text-white-30">{{ $member->user->phone ?? 'NO_COMMS' }}</div>
                    </td>
                    <td>
                        <div class="fs-11 text-white">
                            <iconify-icon icon="tabler:calendar-up" class="me-1 text-primary"></iconify-icon>
                            {{ $member->start_date->format('d M y') }}
                        </div>
                        <div class="fs-11 text-white-30">
                            <iconify-icon icon="tabler:calendar-down" class="me-1 text-danger"></iconify-icon>
                            {{ $member->end_date->format('d M y') }}
                        </div>
                    </td>
                    <td>
                        @if($member->status == 'active')
                            <span class="badge bg-success bg-opacity-10 text-success fs-10 px-2 border border-success border-opacity-10">ONLINE</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger fs-10 px-2 border border-danger border-opacity-10">OFFLINE</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <div class="dropdown">
                            <button class="btn btn-dark p-1 px-2 border-0" data-bs-toggle="dropdown" style="background: rgba(255,255,255,0.05);">
                                <iconify-icon icon="tabler:dots" class="align-middle"></iconify-icon>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end bg-dark border-secondary border-opacity-20 shadow-2xl">
                                <li><a class="dropdown-item text-white fs-12 py-2" href="{{ route('users.show', $member->user->id) }}">Full Profile Intelligence</a></li>
                                <li><a class="dropdown-item text-white fs-12 py-2" href="#">Log Attendance Activity</a></li>
                                <li><hr class="dropdown-divider border-secondary border-opacity-10"></li>
                                <li><a class="dropdown-item text-danger fs-12 py-2" href="#">Terminate Protocol</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5">
                        <iconify-icon icon="tabler:ship" class="display-3 text-white-20 mb-3"></iconify-icon>
                        <h6 class="text-white-50">Empty manifest. No operatives found in this sector.</h6>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $members->links() }}
    </div>
</x-app-layout>
