<x-app-layout title="Gym Management | FitPaxPro">
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-dark fw-bold">Gym Management</h4>
            <p class="text-muted fs-14 mb-0">Oversee and manage your fitness network locations.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.create') }}" class="btn btn-primary px-4 py-2 rounded-3 shadow-none fw-semibold">
                <iconify-icon icon="tabler:plus" class="me-1 align-middle"></iconify-icon> Add New Gym
            </a>
        </div>
    </div>

    <!-- Stats Summary (Compact CC Area) -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-primary-subtle p-2 rounded-3 me-3">
                        <iconify-icon icon="tabler:building-community" class="text-primary fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $gyms->count() }}</h5>
                        <small class="text-muted uppercase fw-semibold fs-11">Total Gyms</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-success-subtle p-2 rounded-3 me-3">
                        <iconify-icon icon="tabler:circle-check" class="text-success fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $gyms->where('is_verified', true)->count() }}</h5>
                        <small class="text-muted uppercase fw-semibold fs-11">Verified</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-warning-subtle p-2 rounded-3 me-3">
                        <iconify-icon icon="tabler:star" class="text-warning fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $gyms->where('is_sponsored', true)->count() }}</h5>
                        <small class="text-muted uppercase fw-semibold fs-11">Sponsored</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0 bg-info-subtle p-2 rounded-3 me-3">
                        <iconify-icon icon="tabler:users" class="text-info fs-24"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold">{{ $gyms->sum('member_count_limit') }}</h5>
                        <small class="text-muted uppercase fw-semibold fs-11">Capacity</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gym Directory Table -->
    <div class="card border-0 shadow-sm rounded-4 table-card">
        <div class="card-header bg-transparent border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">Gym Directory</h5>
                <div class="d-flex gap-2">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <span class="input-group-text bg-light border-0"><iconify-icon icon="tabler:search"></iconify-icon></span>
                        <input type="text" class="form-control bg-light border-0" placeholder="Search gyms...">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light-subtle">
                        <tr class="text-muted fs-11 text-uppercase">
                            <th class="ps-4">Gym Branding & Info</th>
                            <th>Contact Details</th>
                            <th>Metrics</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($gyms as $gym)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 bg-light p-2 rounded-3 me-3">
                                        <iconify-icon icon="tabler:gym" class="text-dark fs-20"></iconify-icon>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $gym->name }}</h6>
                                        <small class="text-muted d-block text-truncate" style="max-width: 200px;">
                                            <iconify-icon icon="tabler:map-pin" class="me-1"></iconify-icon>{{ $gym->address }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fs-13">
                                    <p class="mb-1"><iconify-icon icon="tabler:mail" class="me-1"></iconify-icon>{{ $gym->email }}</p>
                                    <p class="mb-0 text-muted"><iconify-icon icon="tabler:phone" class="me-1"></iconify-icon>{{ $gym->phone }}</p>
                                </div>
                            </td>
                            <td>
                                <div class="fs-13">
                                    <p class="mb-1 fw-semibold"><iconify-icon icon="tabler:users" class="me-1"></iconify-icon>{{ $gym->member_count_limit ?? 'Unlimited' }}</p>
                                    <div class="progress" style="height: 4px; width: 80px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: 65%"></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($gym->owner->name ?? 'None') }}&background=f3f4f6&color=666" class="rounded-circle me-2" style="width: 24px; height: 24px;">
                                    <span class="fs-13">{{ $gym->owner->name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td>
                                @if($gym->is_verified)
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">Verified</span>
                                @else
                                    <span class="badge bg-light text-muted rounded-pill px-3">Pending</span>
                                @endif
                                
                                @if($gym->is_sponsored)
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3"><iconify-icon icon="tabler:star-filled" class="fs-10 me-1"></iconify-icon>Sponsored</span>
                                @endif
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
                                        <iconify-icon icon="tabler:dots-vertical"></iconify-icon>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item" href="{{ route('gym.edit', $gym->uuid) }}"><iconify-icon icon="tabler:edit" class="me-2"></iconify-icon> Edit Gym</a></li>
                                        <li><a class="dropdown-item" href="#"><iconify-icon icon="tabler:chart-bar" class="me-2"></iconify-icon> View Analytics</a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <form action="{{ route('gym.destroy', $gym->uuid) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                                @csrf @method('DELETE')
                                                <button class="dropdown-item text-danger"><iconify-icon icon="tabler:trash" class="me-2"></iconify-icon> Delete Location</button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <iconify-icon icon="tabler:building-off" class="display-4 text-muted mb-3 d-block"></iconify-icon>
                                <h6 class="text-muted">No gyms found in the network.</h6>
                                <a href="{{ route('gym.create') }}" class="btn btn-link text-primary mt-2">Add your first gym location</a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
