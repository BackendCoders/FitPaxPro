<x-app-layout title="Edit Master Plan | FitPaxPro">
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

        .cc-wrapper { max-width: 1000px; margin: 0 auto; padding: 20px; }
        
        .dash-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .dash-title h4 { font-weight: 800; letter-spacing: -1.5px; margin: 0; color: #fff; }
        .dash-title span { color: var(--cc-muted); font-size: 0.85rem; }

        .cc-card { 
            background: var(--cc-card); border: 1px solid var(--cc-border); 
            border-radius: 16px; margin-bottom: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .cc-card-header { 
            padding: 20px 25px; border-bottom: 1px solid var(--cc-border); 
            display: flex; align-items: center; gap: 12px;
        }
        .cc-card-header h6 { margin: 0; font-weight: 800; font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1px; color: #fff; }
        .cc-card-header iconify-icon { color: var(--cc-accent); font-size: 1.4rem; }
        
        .cc-card-body { padding: 30px; }

        .form-label { font-size: 0.75rem; font-weight: 800; color: var(--cc-muted); margin-bottom: 8px; display: block; text-transform: uppercase; letter-spacing: 0.5px; }
        
        .input-group-custom { position: relative; }
        .input-group-custom iconify-icon { 
            position: absolute; left: 15px; top: 50%; transform: translateY(-50%); 
            color: var(--cc-muted); font-size: 1.2rem; transition: color 0.3s;
        }
        .form-control-cc { 
            background: var(--cc-input); border: 1px solid var(--cc-border); 
            color: #fff; border-radius: 12px; font-size: 0.95rem; padding: 12px 15px 12px 45px; height: 52px;
            transition: all 0.2s; width: 100%;
        }
        .form-control-cc:focus { 
            background: #23282e; border-color: var(--cc-accent); box-shadow: 0 0 0 4px rgba(225, 18, 24, 0.1); 
            color: #fff; outline: none;
        }
        .form-control-cc:focus + iconify-icon { color: var(--cc-accent); }

        .feature-toggle-card { 
            background: rgba(255,255,255,0.02); border: 1px solid var(--cc-border); 
            border-radius: 16px; padding: 20px; transition: all 0.3s; 
            display: flex; align-items: center; justify-content: space-between;
        }
        .feature-toggle-card:hover { border-color: rgba(255,255,255,0.1); background: rgba(255,255,255,0.03); }
        .feature-toggle-card h6 { margin: 0; font-size: 0.9rem; font-weight: 700; color: #fff; }
        .feature-toggle-card p { margin: 0; font-size: 0.75rem; color: var(--cc-muted); }

        .btn-update { 
            background: var(--cc-accent); border: none; color: white; font-weight: 800; 
            padding: 14px 40px; border-radius: 12px; font-size: 1rem; width: 100%;
            box-shadow: 0 8px 20px rgba(225,18,24,0.3); transition: all 0.3s;
        }
        .btn-update:hover { background: #ff1a22; transform: translateY(-2px); box-shadow: 0 12px 30px rgba(225,18,24,0.5); }

        .exit-link { color: var(--cc-muted); font-weight: 700; font-size: 0.85rem; text-decoration: none; transition: 0.2s; }
        .exit-link:hover { color: #fff; }

        .neon-switch { width: 44px; height: 22px; cursor: pointer; }
    </style>
    @endpush

    <div class="cc-wrapper">
        <div class="dash-header">
            <div class="dash-title">
                <h4>Edit Master Subscription</h4>
                <span>Refining parameters for <strong>{{ $plan->name }}</strong></span>
            </div>
            <a href="{{ route('admin.platform-plans.index') }}" class="exit-link">
                <iconify-icon icon="tabler:arrow-left" class="align-middle me-1"></iconify-icon> EXIT EDIT MODE
            </a>
        </div>

        <form action="{{ route('admin.platform-plans.update', $plan->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row g-4">
                <div class="col-lg-12">
                    <!-- Identity -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:package"></iconify-icon>
                            <h6>Subscription Branding</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-4">
                                <div class="col-md-12">
                                    <label class="form-label">Internal Tier Name</label>
                                    <div class="input-group-custom">
                                        <input type="text" name="name" class="form-control-cc" value="{{ $plan->name }}" placeholder="e.g. Enterprise Elite Node" required>
                                        <iconify-icon icon="tabler:crown"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Economics -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:cash"></iconify-icon>
                            <h6>Commercial Parameters</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Monthly Retainer (₹)</label>
                                    <div class="input-group-custom">
                                        <input type="number" step="0.01" name="monthly_price" class="form-control-cc" value="{{ $plan->monthly_price }}" placeholder="0.00" required>
                                        <iconify-icon icon="tabler:calendar-month"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Annual Commitment (₹)</label>
                                    <div class="input-group-custom">
                                        <input type="number" step="0.01" name="yearly_price" class="form-control-cc" value="{{ $plan->yearly_price }}" placeholder="0.00">
                                        <iconify-icon icon="tabler:calendar-stats"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Limits -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:settings-automation"></iconify-icon>
                            <h6>Operational Constraints</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label">Max Gym Locations</label>
                                    <div class="input-group-custom">
                                        <input type="number" name="max_gyms" class="form-control-cc" value="{{ $plan->max_gyms }}" required>
                                        <iconify-icon icon="tabler:building-skyscraper"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Global Member Capacity</label>
                                    <div class="input-group-custom">
                                        <input type="number" name="max_members" class="form-control-cc" value="{{ $plan->max_members }}" placeholder="Unlimited">
                                        <iconify-icon icon="tabler:users-group"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Privileges -->
                    <div class="cc-card">
                        <div class="cc-card-header">
                            <iconify-icon icon="tabler:sparkles"></iconify-icon>
                            <h6>Elite Privileges</h6>
                        </div>
                        <div class="cc-card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="feature-toggle-card">
                                        <div>
                                            <h6>Advanced Intelligence</h6>
                                            <p>Enable data analytics & heatmaps</p>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input neon-switch" type="checkbox" name="has_analytics" value="1" {{ $plan->has_analytics ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="feature-toggle-card">
                                        <div>
                                            <h6>Digital Identity</h6>
                                            <p>Enable white-label mobile app</p>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input neon-switch" type="checkbox" name="has_mobile_app" value="1" {{ $plan->has_mobile_app ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <button type="submit" class="btn-update">
                            <iconify-icon icon="tabler:refresh" class="me-2"></iconify-icon> UPDATE MASTER TIER
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
