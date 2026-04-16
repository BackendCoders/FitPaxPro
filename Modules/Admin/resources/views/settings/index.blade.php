@inject('set', 'App\Services\SettingService')
<x-app-layout title="System Settings | FitPaxPro">
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-dark fw-bold">System Settings</h4>
            <p class="text-muted fs-14 mb-0">Configure your global site preferences and branding assets.</p>
        </div>
    </div>

    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            <!-- Sidebar Navigation Tabs -->
            <div class="col-md-3">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="nav flex-column nav-pills" id="settings-tabs" role="tablist" aria-orientation="vertical">
                        <button class="nav-link active text-start py-3 px-4 border-0 rounded-0 fs-14 fw-semibold" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">
                            <iconify-icon icon="tabler:palette" class="me-2 align-middle"></iconify-icon> Branding & Identity
                        </button>
                        <button class="nav-link text-start py-3 px-4 border-0 rounded-0 fs-14 fw-semibold" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                            <iconify-icon icon="tabler:settings" class="me-2 align-middle"></iconify-icon> General Configuration
                        </button>
                        <button class="nav-link text-start py-3 px-4 border-0 rounded-0 fs-14 fw-semibold" data-bs-toggle="pill" data-bs-target="#tab-localization" type="button" role="tab">
                            <iconify-icon icon="tabler:world" class="me-2 align-middle"></iconify-icon> Localization
                        </button>
                    </div>
                </div>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-4 fw-bold shadow-sm">
                        Save Global Settings <iconify-icon icon="tabler:device-floppy" class="ms-1 align-middle"></iconify-icon>
                    </button>
                </div>
            </div>

            <!-- Tab Content Area -->
            <div class="col-md-9">
                <div class="tab-content" id="settings-content">
                    
                    <!-- Branding Tab -->
                    <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h6 class="fw-bold text-dark mb-4">Branding Assets</h6>
                            
                            <div class="row g-5">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-3">Application Logo</label>
                                    <div class="border rounded-4 p-4 text-center bg-light-subtle position-relative overflow-hidden mb-3">
                                        <img src="{{ $set->getImageUrl('logo') }}" alt="Current Logo" class="img-fluid mb-3" style="max-height: 80px;">
                                        <div class="fs-12 text-muted">Dimensions: 300x120px recommended.</div>
                                    </div>
                                    <input type="file" name="logo" class="form-control rounded-3" accept="image/*">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-bold mb-3">Browser Favicon</label>
                                    <div class="border rounded-4 p-4 text-center bg-light-subtle position-relative overflow-hidden mb-3">
                                        <img src="{{ $set->getImageUrl('favicon') }}" alt="Current Favicon" class="img-fluid mb-3" style="max-height: 48px; width: 48px; object-fit: contain;">
                                        <div class="fs-12 text-muted">Dimensions: 32x32px or 64x64px.</div>
                                    </div>
                                    <input type="file" name="favicon" class="form-control rounded-3" accept="image/x-icon,image/png">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- General Config Tab -->
                    <div class="tab-pane fade" id="tab-general" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h6 class="fw-bold text-dark mb-4">Website Identity</h6>
                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Site Display Title</label>
                                    <input type="text" name="site_title" class="form-control py-2" value="{{ $settings['site_title'] ?? 'FitPaxPro' }}" placeholder="Enter site name">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Administrator Email</label>
                                    <input type="email" name="admin_email" class="form-control py-2" value="{{ $settings['admin_email'] ?? 'admin@fitpaxpro.com' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Support Hot-line</label>
                                    <input type="text" name="contact_number" class="form-control py-2" value="{{ $settings['contact_number'] ?? '+1 234 567 890' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold fs-13">Official Office Address</label>
                                    <textarea name="office_address" class="form-control py-2" rows="3">{{ $settings['office_address'] ?? '' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Localization Tab -->
                    <div class="tab-pane fade" id="tab-localization" role="tabpanel">
                        <div class="card border-0 shadow-sm rounded-4 p-4">
                            <h6 class="fw-bold text-dark mb-4">Financial & Geographic Settings</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Global Currency</label>
                                    <select name="currency" class="form-select py-2">
                                        <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD - United States Dollar</option>
                                        <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                        <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                        <option value="GBP" {{ ($settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Currency Symbol</label>
                                    <input type="text" name="currency_symbol" class="form-control py-2" value="{{ $settings['currency_symbol'] ?? '$' }}" placeholder="$, ₹, €, etc.">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold fs-13">Application Timezone</label>
                                    <select class="form-select py-2">
                                        <option value="UTC">UTC (Default)</option>
                                        <option value="Asia/Kolkata">Asia/Kolkata (IST)</option>
                                        <option value="America/New_York">America/New_York (EST)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </form>
</x-app-layout>

<style>
    .nav-pills .nav-link { 
        color: #64748b;
        background: white;
    }
    .nav-pills .nav-link:hover {
        background: #f8fafc;
    }
    .nav-pills .nav-link.active {
        background: var(--bs-primary) !important;
        color: white !important;
    }
    .bg-light-subtle {
        background-color: #f8fafc !important;
    }
</style>
