<x-app-layout title="Membership Plan Architect | FitPaxPro">
    @push('styles')
    <style>
        .architect-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; padding: 40px; }
        .input-dark { background: #1c2126 !important; border: 1px solid rgba(255,255,255,0.05) !important; color: #fff !important; border-radius: 12px !important; padding: 12px 15px !important; }
        .input-dark:focus { border-color: #E11218 !important; box-shadow: 0 0 0 4px rgba(225,18,24,0.1) !important; }
        .form-label-tactical { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); margin-bottom: 8px; }
        
        .benefit-box { 
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; padding: 15px 20px; transition: 0.3s;
        }
        .benefit-box:hover { background: rgba(255,255,255,0.04); border-color: rgba(255,255,255,0.1); }
        
        .side-info { background: linear-gradient(135deg, #E11218 0%, #9a0d11 100%); border-radius: 20px; padding: 40px; color: #fff; height: 100%; display: flex; flex-direction: column; justify-content: space-between; }
        
        .neon-switch .form-check-input { width: 3em; height: 1.5em; background-color: rgba(255,255,255,0.1); border-color: transparent; }
        .neon-switch .form-check-input:checked { background-color: #E11218; border-color: #E11218; }
    </style>
    @endpush

    <div class="row align-items-center mb-5">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Plan Architect</h4>
            <p class="text-white-50 fs-14 mb-0">Precision tuning for <strong>{{ $plan->name }}</strong> membership tier.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.plans.index') }}" class="btn btn-dark px-4 py-2 border-0 shadow-lg" style="background: #16191d;">
                <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> Back to Directory
            </a>
        </div>
    </div>

    <div class="row g-4 justify-content-center">
        <div class="col-xl-11">
            <div class="architect-card shadow-2xl">
                <div class="row g-5">
                    <div class="col-lg-7">
                        <form action="{{ route('gym.plans.update', $plan->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-5">
                                <h6 class="text-white fw-bold mb-4 d-flex align-items-center">
                                    <span class="badge bg-primary me-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span> 
                                    IDENTIFICATION & IDENTITY
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-5">
                                        <label class="form-label-tactical">OFFICIAL PLAN NAME</label>
                                        <input type="text" name="name" class="form-control input-dark @error('name') is-invalid @enderror" placeholder="e.g. ELITE PERFORMANCE" value="{{ old('name', $plan->name) }}">
                                        @error('name') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-tactical">MARKETING TAG</label>
                                        <input type="text" name="tagline" class="form-control input-dark" placeholder="e.g. MOST POPULAR" value="{{ old('tagline', $plan->tagline) }}">
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-tactical">PLAN PREVIEW IMAGE</label>
                                        <input type="file" name="image" class="form-control input-dark" accept="image/*">
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label-tactical">PLAN SCOPE / DESCRIPTION</label>
                                        <textarea name="description" class="form-control input-dark" rows="2" placeholder="Brief system overview of this membership tier...">{{ old('description', $plan->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h6 class="text-white fw-bold mb-4 d-flex align-items-center">
                                    <span class="badge bg-success me-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span> 
                                    COMMERCIAL PARAMETERS
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label-tactical">BASE VALUATION (₹)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-0 text-white-50">₹</span>
                                            <input type="number" name="price" step="0.01" class="form-control input-dark @error('price') is-invalid @enderror" value="{{ old('price', $plan->price) }}">
                                        </div>
                                        @error('price') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-tactical">CAMPAIGN PRICE (OPTIONAL)</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-dark border-0 text-white-50">₹</span>
                                            <input type="number" name="offer_price" step="0.01" class="form-control input-dark" value="{{ old('offer_price', $plan->offer_price) }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-tactical">CYCLE DURATION (MO)</label>
                                        <input type="number" name="duration_months" class="form-control input-dark @error('duration_months') is-invalid @enderror" value="{{ old('duration_months', $plan->duration_months) }}">
                                        @error('duration_months') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-12 mt-3">
                                        <label class="form-label-tactical">FEATURE MANIFESTO (ONE PER LINE)</label>
                                        <textarea name="features_list" class="form-control input-dark" rows="4" placeholder="List the tactical advantages included in this plan...">{{ old('features_list', $plan->features ? implode("\n", $plan->features) : '') }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="form-label-tactical d-block mb-3">TACTICAL ADD-ONS</label>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="benefit-box d-flex align-items-center neon-switch">
                                            <div class="flex-grow-1">
                                                <h6 class="text-white fs-14 mb-1">Human Asset Support</h6>
                                                <p class="text-white-50 fs-11 mb-0">Assign a dedicated personal trainer</p>
                                            </div>
                                            <input class="form-check-input shadow-none" type="checkbox" name="includes_trainer" value="1" id="tr-sw" {{ old('includes_trainer', $plan->includes_trainer) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="benefit-box d-flex align-items-center neon-switch">
                                            <div class="flex-grow-1">
                                                <h6 class="text-white fs-14 mb-1">Fueling Protocol</h6>
                                                <p class="text-white-50 fs-11 mb-0">Include custom dietary management</p>
                                            </div>
                                            <input class="form-check-input shadow-none" type="checkbox" name="includes_diet_plan" value="1" id="dt-sw" {{ old('includes_diet_plan', $plan->includes_diet_plan) ? 'checked' : '' }}>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <x-dynamic-fields model-type="App\Models\MembershipPlanTemplate" :model="$plan" />
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-none fw-bold uppercase letter-spacing-1">
                                    RECONFIGURE PLAN ARCHITECTURE
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-5">
                        <div class="side-info">
                            <div>
                                <div class="d-flex align-items-center mb-4">
                                    <div class="p-3 bg-white bg-opacity-10 rounded-circle me-3">
                                        <iconify-icon icon="tabler:shield-check" class="fs-32"></iconify-icon>
                                    </div>
                                    <h5 class="fw-bold mb-0">System Integrity</h5>
                                </div>
                                <p class="fs-14 text-white-80">Modifying this plan will update the protocol for all future signups. Current active subscriptions will maintain their original valuation until their next billing cycle.</p>
                                
                                <ul class="list-unstyled mt-5">
                                    <li class="mb-4 d-flex align-items-start">
                                        <iconify-icon icon="tabler:circle-dot" class="mt-1 me-3 fs-18"></iconify-icon>
                                        <div>
                                            <h6 class="fs-14 fw-bold">Live Valuation</h6>
                                            <p class="fs-12 text-white-50">Updates take effect immediately across the gym's public portal.</p>
                                        </div>
                                    </li>
                                    <li class="mb-4 d-flex align-items-start">
                                        <iconify-icon icon="tabler:circle-dot" class="mt-1 me-3 fs-18"></iconify-icon>
                                        <div>
                                            <h6 class="fs-14 fw-bold">Benefit Sync</h6>
                                            <p class="fs-12 text-white-50">Trainer and Diet switches synchronize with the member app dashboard.</p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            
                            <div class="text-center p-3 bg-black bg-opacity-20 rounded-3">
                                <p class="mb-0 fs-11 text-uppercase fw-bold letter-spacing-1 opacity-50">Node Identifier</p>
                                <code class="text-white fs-14">{{ $plan->id }}</code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
