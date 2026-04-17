<x-app-layout title="Plan Directory | FitPaxPro">
    @push('styles')
    <style>
        
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; margin-bottom: 2px; text-transform: uppercase; font-size: 1.4rem; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.4px; }

        /* Compact Tier Card */
        .tier-card { 
            background: #121418; border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 20px; padding: 22px; transition: 0.3s; position: relative; overflow: hidden; height: 100%;
            display: flex; flex-direction: column; justify-content: space-between;
        }
        .tier-card:hover { border-color: rgba(225,18,24,0.3); transform: translateY(-3px); }
        .tier-glow { position: absolute; top: -30px; right: -30px; width: 100px; height: 100px; background: radial-gradient(circle, rgba(225,18,24,0.08) 0%, transparent 70%); }
        
        .tier-badge { 
            background: rgba(225,18,24,0.1); color: #E11218; font-size: 0.6rem; font-weight: 900; 
            padding: 4px 10px; border-radius: 50px; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 15px; display: inline-block;
        }
        
        .tier-title { font-weight: 800; color: #fff; font-size: 1.1rem; margin-bottom: 5px; text-transform: uppercase; }
        .tier-price { font-weight: 900; color: #fff; font-size: 1.5rem; margin-bottom: 15px; display: flex; align-items: baseline; gap: 4px; }
        .tier-price small { font-size: 0.7rem; color: rgba(255,255,255,0.4); font-weight: 500; }

        .tier-feature { display: flex; align-items: center; gap: 10px; margin-bottom: 8px; font-size: 0.75rem; color: rgba(255,255,255,0.5); }
        .tier-feature iconify-icon { color: #E11218; font-size: 1rem; }

        /* Compact Controls */
        .tier-footer { 
            border-top: 1px solid rgba(255,255,255,0.03); padding-top: 15px; margin-top: 20px;
            display: flex; align-items: center; justify-content: space-between;
        }
        
        .btn-action { 
            width: 34px; height: 34px; border-radius: 10px; background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.05); color: #fff; display: flex; align-items: center; justify-content: center; transition: 0.2s;
        }
        .btn-action:hover { background: var(--rich-red); color: white; border-color: var(--rich-red); }
        .btn-action.btn-edit:hover { background: #3085d6; border-color: #3085d6; }

        /* Industrial Switch */
        .stealth-switch { width: 40px; height: 20px; cursor: pointer; background-color: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); }
        .stealth-switch:checked { background-color: #E11218; border-color: #E11218; box-shadow: 0 0 10px rgba(225,18,24,0.3); }
    </style>
    @endpush

    <div class="directory-wrapper">
        <div class="row align-items-center mb-4">
            <div class="col-8">
                <div class="page-header">
                    <h4>Membership Directory</h4>
                    <p>MANAGING GLOBAL PRICING TIERS & NODES</p>
                </div>
            </div>
            <div class="col-4 text-end">
                <a href="{{ route('gym.plans.create') }}" class="btn btn-primary px-3 py-2 rounded-2 shadow-none fw-bold fs-10 text-uppercase letter-spacing-1">
                    <iconify-icon icon="tabler:plus" class="me-1 align-middle"></iconify-icon> New Tier Architecture
                </a>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="card bg-transparent border-0 mb-4">
            <div class="row g-2">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-dark border-0 text-white-30"><iconify-icon icon="tabler:search"></iconify-icon></span>
                        <input type="text" class="form-control bg-dark border-0 text-white fs-12 py-2" placeholder="Search parameters...">
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($plans as $plan)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="tier-card">
                    <!-- <div class="tier-glow"></div> -->
                    <div>
                        <div class="d-flex justify-content-between align-items-start">
                            <span class="tier-badge">{{ $plan->tagline ?? 'Standard' }}</span>
                            <div class="form-check form-switch p-0 m-0">
                                <input class="form-check-input stealth-switch status-toggle" type="checkbox" 
                                    data-url="{{ route('gym.plans.toggle-status', $plan->id) }}" 
                                    {{ $plan->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                        
                        <h5 class="tier-title">{{ $plan->name }}</h5>
                        <div class="tier-price">
                            ₹{{ number_format($plan->offer_price ?? $plan->price, 0) }}
                            <small>/ {{ $plan->duration_months }} Months</small>
                        </div>

                        <div class="tier-features mt-3">
                            <div class="tier-feature">
                                <iconify-icon icon="tabler:calendar-stats"></iconify-icon>
                                <span>{{ $plan->duration_months }} Month Lifecycle</span>
                            </div>
                            @if($plan->includes_trainer)
                            <div class="tier-feature">
                                <iconify-icon icon="tabler:user-star"></iconify-icon>
                                <span>Personal Command Coach</span>
                            </div>
                            @endif
                            @if($plan->includes_diet_plan)
                            <div class="tier-feature">
                                <iconify-icon icon="tabler:apple"></iconify-icon>
                                <span>Nutrition Intelligence</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="tier-footer">
                        <div class="fs-10 text-white-30 fw-bold uppercase letter-spacing-1">
                            ID: {{ substr($plan->id, 0, 8) }}
                        </div>
                        <div class="d-flex gap-2">
                            <a href="{{ route('gym.plans.edit', $plan->id) }}" class="btn-action btn-edit" title="Modify Architecture">
                                <iconify-icon icon="tabler:edit-circle"></iconify-icon>
                            </a>
                            <form action="{{ route('gym.plans.destroy', $plan->id) }}" method="POST" class="delete-form">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action" title="Terminate Node">
                                    <iconify-icon icon="tabler:trash"></iconify-icon>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="text-center py-5 bg-dark rounded-4 opacity-50">
                    <iconify-icon icon="tabler:packages" class="display-3 mb-3"></iconify-icon>
                    <h5 class="text-uppercase letter-spacing-2">No Active Tiers</h5>
                    <p class="fs-12 uppercase letter-spacing-1">Bootstrap your economy by creating a new membership template.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            $('.status-toggle').on('change', function() {
                const $self = $(this);
                const url = $self.data('url');
                const isChecked = $self.is(':checked');
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    dataType: 'json',
                    success: function(data) {
                        if(data.success) {
                            if(data.is_active) {
                                toastr.success(data.message);
                            } else {
                                toastr.warning(data.message);
                            }
                        } else {
                            $self.prop('checked', !isChecked);
                            toastr.error(data.message || 'Mission Critical Error');
                        }
                    },
                    error: function(xhr) {
                        $self.prop('checked', !isChecked);
                        const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'System Link Failure';
                        toastr.error(errorMsg);
                        console.error('Bioluminescent Sync Error:', xhr);
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout>
