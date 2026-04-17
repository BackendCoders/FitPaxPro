<x-app-layout title="Edit Membership Plan | FitPaxPro">
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-dark fw-bold">Edit Membership Plan</h4>
            <p class="text-muted fs-14 mb-0">Modify the settings for your existing membership tier.</p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.plans.index') }}" class="btn btn-light px-4 py-2 rounded-3 shadow-none fw-semibold">
                <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> Back to List
            </a>
        </div>
    </div>

    <div class="row justify-content-center mt-2">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-7">
                        <div class="card-body p-4 p-xl-5">
                            <form action="{{ route('gym.plans.update', $plan->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="mb-4">
                                    <label class="form-label text-dark fw-bold mb-2">Primary Details & Branding</label>
                                    <div class="row g-3">
                                        <div class="col-md-7">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Plan Display Name</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-0 text-primary"><iconify-icon icon="tabler:tag"></iconify-icon></span>
                                                    <input type="text" name="name" class="form-control bg-light border-0 shadow-none @error('name') is-invalid @enderror" placeholder="e.g. Premium Annual Membership" value="{{ old('name', $plan->name) }}">
                                                </div>
                                                @error('name') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Badge/Tagline</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-0 text-success"><iconify-icon icon="tabler:award"></iconify-icon></span>
                                                    <input type="text" name="tagline" class="form-control bg-light border-0 shadow-none" placeholder="e.g. Best Seller" value="{{ old('tagline', $plan->tagline) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Short Description</label>
                                                <textarea name="description" class="form-control bg-light border-0 shadow-none" rows="2" placeholder="Briefly describe what makes this plan unique...">{{ old('description', $plan->description) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4 pt-2">
                                    <label class="form-label text-dark fw-bold mb-2">Pricing & Features</label>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Base Price (₹)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-0 text-dark fw-bold">₹</span>
                                                    <input type="number" name="price" step="0.01" class="form-control bg-light border-0 shadow-none @error('price') is-invalid @enderror" placeholder="0.00" value="{{ old('price', $plan->price) }}">
                                                </div>
                                                @error('price') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Offer Price (Optional)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-0 text-muted">₹</span>
                                                    <input type="number" name="offer_price" step="0.01" class="form-control bg-light border-0 shadow-none" placeholder="0.00" value="{{ old('offer_price', $plan->offer_price) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Duration (Months)</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light border-0 text-primary"><iconify-icon icon="tabler:calendar-time"></iconify-icon></span>
                                                    <input type="number" name="duration_months" class="form-control bg-light border-0 shadow-none @error('duration_months') is-invalid @enderror" placeholder="e.g. 12" value="{{ old('duration_months', $plan->duration_months) }}">
                                                </div>
                                                @error('duration_months') <div class="text-danger fs-12 mt-1">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="form-label fs-13 text-muted">Highlights / Key Features (One per line)</label>
                                                <textarea name="features_list" class="form-control bg-light border-0 shadow-none" rows="3" placeholder="Access to all machines&#10;Free lockers&#10;Steam bath usage">{{ old('features_list', $plan->features ? implode("\n", $plan->features) : '') }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-5 pt-2">
                                    <label class="form-label text-dark fw-bold mb-3 d-block">Additional Benefits</label>
                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <div class="p-3 border rounded-3 d-flex align-items-center">
                                                <div class="form-check form-switch m-0 flex-grow-1">
                                                    <label class="form-check-label h6 mb-0 ms-2 cursor-pointer" for="trainerSwitch">Personal Trainer</label>
                                                    <input class="form-check-input ms-0" type="checkbox" name="includes_trainer" value="1" id="trainerSwitch" {{ old('includes_trainer', $plan->includes_trainer) ? 'checked' : '' }}>
                                                </div>
                                                <iconify-icon icon="tabler:user-star" class="text-primary fs-20"></iconify-icon>
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="p-3 border rounded-3 d-flex align-items-center">
                                                <div class="form-check form-switch m-0 flex-grow-1">
                                                    <label class="form-check-label h6 mb-0 ms-2 cursor-pointer" for="dietSwitch">Custom Diet Plan</label>
                                                    <input class="form-check-input ms-0" type="checkbox" name="includes_diet_plan" value="1" id="dietSwitch" {{ old('includes_diet_plan', $plan->includes_diet_plan) ? 'checked' : '' }}>
                                                </div>
                                                <iconify-icon icon="tabler:apple" class="text-success fs-20"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <x-dynamic-fields model-type="App\Models\MembershipPlanTemplate" :model="$plan" />

                                <div class="d-grid pt-2">
                                    <button type="submit" class="btn btn-primary py-2 fs-15 fw-bold shadow-none">
                                        Update Membership Plan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-5 d-none d-lg-block bg-light-subtle border-start">
                        <div class="h-100 d-flex flex-column justify-content-center p-5">
                            <div class="text-center mb-4">
                                <div class="bg-white shadow-sm p-4 rounded-circle d-inline-block mb-3">
                                    <iconify-icon icon="tabler:refresh" class="display-4 text-primary"></iconify-icon>
                                </div>
                                <h5 class="fw-bold">Update Plan Details</h5>
                                <p class="text-muted fs-13">Adjust pricing or service inclusions for existing members and new signups.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
