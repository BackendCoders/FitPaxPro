<x-app-layout title="Global Logistics | FitPaxPro">
    @push('styles')
        <style>
            .hub-wrapper {
                padding: 25px 15px;
            }

            .tier-card {
                background: #16191d;
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 20px;
                padding: 30px;
                transition: 0.3s;
                position: relative;
                overflow: hidden;
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .tier-card:hover {
                border-color: var(--rich-red);
                transform: translateY(-5px);
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
            }

            .tier-glow {
                position: absolute;
                top: -50px;
                right: -50px;
                width: 150px;
                height: 150px;
                background: radial-gradient(circle, rgba(225, 18, 24, 0.1) 0%, transparent 70%);
            }

            .tier-price {
                font-size: 2.5rem;
                font-weight: 800;
                color: #fff;
                letter-spacing: -2px;
                margin: 15px 0;
            }

            .tier-price span {
                font-size: 0.9rem;
                color: rgba(255, 255, 255, 0.4);
                letter-spacing: 0;
            }

            .feat-pill {
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid rgba(255, 255, 255, 0.05);
                padding: 8px 12px;
                border-radius: 10px;
                font-size: 0.75rem;
                color: rgba(255, 255, 255, 0.6);
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .feat-pill.active {
                border-color: rgba(225, 18, 24, 0.3);
                color: #fff;
            }

            .feat-pill.active iconify-icon {
                color: var(--rich-red);
            }

            .btn-tier-action {
                background: rgba(255, 255, 255, 0.03);
                color: #fff;
                border: 1px solid rgba(255, 255, 255, 0.1);
                width: 100%;
                padding: 10px;
                border-radius: 12px;
                font-weight: 700;
                font-size: 0.85rem;
                margin-top: auto;
                transition: 0.3s;
            }

            .btn-tier-action:hover {
                background: var(--rich-red);
                border-color: var(--rich-red);
            }

            .filter-node {
                background: #16191d;
                border: 1px solid rgba(255, 255, 255, 0.05);
                border-radius: 14px;
                padding: 15px;
            }
        </style>
    @endpush

    <div class="creator-wrapper">
        <div class="row align-items-center mb-4">
            <div class="col-sm-6">
                <h4 class="mb-1 text-white fw-bold">Master Subscription Tiers</h4>
                <p class="text-white-50 fs-14 mb-0">Defining the platform's global economic architecture.</p>
            </div>
            <div class="col-sm-6 text-sm-end">
                <a href="{{ route('admin.platform-plans.create') }}"
                    class="btn btn-primary px-4 py-2 rounded-3 shadow-none fw-bold">
                    <iconify-icon icon="tabler:plus" class="me-1 align-middle"></iconify-icon> CONFIGURE NEW TIER
                </a>
            </div>
        </div>

        <!-- Tier Filters -->
        <div class="filter-node mb-4">
            <form action="{{ route('admin.platform-plans.index') }}" method="GET" class="row g-2">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-0 text-white-50"><iconify-icon
                                icon="tabler:search"></iconify-icon></span>
                        <input type="text" name="search" class="form-control bg-dark border-0 text-white shadow-none"
                            placeholder="Locate tiers by name or internal id..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 fw-bold shadow-none">APPLY SEARCH</button>
                </div>
            </form>
        </div>

        <div class="row g-4">
            @forelse($plans as $plan)
                <div class="col-xl-4 col-md-6">
                    <div class="tier-card">
                        <div class="tier-glow"></div>

                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="text-white-50 fw-bold uppercase fs-10 letter-spacing-2 mb-1">PLATFORM
                                    ARCHITECTURE</h6>
                                <h5 class="text-white fw-800 mb-0">{{ $plan->name }}</h5>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-link link-light p-0" data-bs-toggle="dropdown">
                                    <iconify-icon icon="tabler:dots-vertical" class="fs-18"></iconify-icon>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark">
                                    <li><a class="dropdown-item"
                                            href="{{ route('admin.platform-plans.edit', $plan->id) }}"><iconify-icon
                                                icon="tabler:edit" class="me-2"></iconify-icon> Edit Parameters</a></li>
                                    <li>
                                        <hr class="dropdown-divider">
                                    </li>
                                    <li>
                                        <form action="{{ route('admin.platform-plans.destroy', $plan->id) }}" method="POST"
                                            onsubmit="return confirm('Immediately terminate this global tier?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">Decommission
                                                Tier</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="tier-price">
                            ₹{{ number_format($plan->monthly_price, 0) }}<span>/mo. retention</span>
                        </div>

                        <div class="vstack gap-2 mb-4">
                            <div class="feat-pill active">
                                <iconify-icon icon="tabler:building-skyscraper"></iconify-icon>
                                <span>Limit: <strong>{{ $plan->max_gyms }} Locations</strong></span>
                            </div>
                            <div class="feat-pill active">
                                <iconify-icon icon="tabler:users-group"></iconify-icon>
                                <span>Capacity: <strong>{{ $plan->max_members ?? 'Unlimited' }} members/node</strong></span>
                            </div>
                            @if($plan->has_analytics)
                                <div class="feat-pill active">
                                    <iconify-icon icon="tabler:brand-google-analytics"></iconify-icon>
                                    <span>Data Intelligence Suite</span>
                                </div>
                            @endif
                            @if($plan->has_mobile_app)
                                <div class="feat-pill active">
                                    <iconify-icon icon="tabler:device-mobile"></iconify-icon>
                                    <span>Branded Digital Asset</span>
                                </div>
                            @endif
                        </div>

                        <a href="{{ route('admin.platform-plans.edit', $plan->id) }}"
                            class="btn-tier-action text-decoration-none text-center">
                            ANALYZE PARAMENTERS <iconify-icon icon="tabler:chevron-right"
                                class="ms-1 align-middle"></iconify-icon>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <iconify-icon icon="tabler:layers-off" class="display-1 text-white-50 mb-3"></iconify-icon>
                    <h5 class="text-white">Tier Architect Empty</h5>
                    <p class="text-white-50">No platform subscription models have been established yet.</p>
                </div>
            @endforelse
        </div>
    </div>

</x-app-layout>