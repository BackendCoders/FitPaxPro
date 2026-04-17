@inject('set', 'App\Services\SettingService')
<x-app-layout title="System Repo | FitPaxPro">
    @push('styles')
    <style>
        .settings-wrapper { padding: 25px 15px; }
        
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; margin-bottom: 2px; text-transform: uppercase; font-size: 1.4rem; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.4px; }

        /* Compact Command Sidebar */
        .command-pane { 
            background: #121418; border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; overflow: hidden; position: sticky; top: 90px;
        }
        .command-link { 
            display: flex; align-items: center; gap: 12px; padding: 15px 20px; 
            color: rgba(255,255,255,0.4); text-decoration: none; border: none; background: none; width: 100%;
            text-align: left; transition: 0.2s; border-bottom: 1px solid rgba(255,255,255,0.02); 
            font-weight: 700; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 1.2px;
        }
        .command-link iconify-icon { font-size: 1.1rem; }
        .command-link.active { 
            color: #fff; background: rgba(225, 18, 24, 0.08); 
            border-left: 3px solid var(--rich-red); padding-left: 17px;
        }

        /* Compact Config Cards */
        .config-card { 
            background: #121418; border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 20px; padding: 25px; margin-bottom: 20px;
        }
        .config-title { 
            font-size: 0.65rem; font-weight: 900; color: var(--rich-red); 
            letter-spacing: 1.5px; text-transform: uppercase; margin-bottom: 20px;
            display: flex; align-items: center; gap: 8px;
        }

        /* Compact Asset Node */
        .asset-node {
            background: #08090b; border: 1px solid rgba(255,255,255,0.04);
            border-radius: 16px; padding: 25px 15px; text-align: center;
            margin-bottom: 15px;
        }
        .asset-preview { max-height: 60px; margin-bottom: 15px; }
        
        /* Compact Tactical Inputs */
        .field-label { 
            display: block; font-size: 0.6rem; font-weight: 800; color: rgba(255,255,255,0.3); 
            text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 8px;
        }
        .stealth-input { 
            background: #08090b !important; border: 1px solid rgba(255,255,255,0.05) !important; 
            color: #fff !important; border-radius: 10px !important; font-size: 0.85rem; 
            padding: 10px 15px !important; height: 44px !important; width: 100%; transition: 0.2s !important;
        }
        .stealth-input:focus { border-color: var(--rich-red) !important; box-shadow: 0 0 10px rgba(225,18,24,0.1) !important; background: #000 !important; }
        
        textarea.stealth-input { height: auto !important; padding: 12px 15px !important; }

        .btn-sync {
            background: linear-gradient(to right, #E11218, #9c0c11);
            border: none; color: white; font-weight: 800; padding: 14px; border-radius: 14px;
            font-size: 0.8rem; width: 100%; text-transform: uppercase; letter-spacing: 1.5px;
            box-shadow: 0 8px 25px rgba(225,18,24,0.3); display: flex; align-items: center; justify-content: center; gap: 8px;
        }
    </style>
    @endpush

    <div class="settings-wrapper">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="row align-items-center mb-4">
                <div class="col-8">
                    <div class="page-header">
                        <h4>System Repo</h4>
                        <p>CORE PROTOCOL MAPPING & ASSETS</p>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <button type="submit" class="btn btn-link text-white-50 p-0 fs-10 fw-bold text-decoration-none text-uppercase letter-spacing-1">
                        <iconify-icon icon="tabler:history" class="me-1 align-middle"></iconify-icon> Reset Hub
                    </button>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-3">
                    <div class="command-pane">
                        <div class="nav flex-column" id="settings-tabs" role="tablist">
                            <button class="command-link active" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">
                                <iconify-icon icon="tabler:augmented-reality"></iconify-icon> Identity
                            </button>
                            <button class="command-link" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                                <iconify-icon icon="tabler:dna"></iconify-icon> System DNA
                            </button>
                            <button class="command-link" data-bs-toggle="pill" data-bs-target="#tab-localization" type="button" role="tab">
                                <iconify-icon icon="tabler:language"></iconify-icon> Localization
                            </button>
                        </div>
                    </div>

                    <div class="mt-3">
                        <button type="submit" class="btn-sync">
                            SYNC MESH <iconify-icon icon="tabler:refresh" class="fs-14"></iconify-icon>
                        </button>
                    </div>
                </div>

                <div class="col-md-9">
                    <div class="tab-content" id="settings-content">
                        
                        <div class="tab-pane fade show active" id="tab-branding" role="tabpanel">
                            <div class="config-card">
                                <h6 class="config-title"><iconify-icon icon="tabler:brush"></iconify-icon> Brand Infrastructure</h6>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="field-label">Primary Logo (Seal)</label>
                                        <div class="asset-node">
                                            <img src="{{ $set->getImageUrl('logo') }}" alt="Logo" class="asset-preview">
                                            <div class="fs-9 text-white-30 uppercase letter-spacing-1">Alpha-Layer Opt.</div>
                                        </div>
                                        <input type="file" name="logo" class="form-control stealth-input" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="field-label">Favicon Node</label>
                                        <div class="asset-node">
                                            <img src="{{ $set->getImageUrl('favicon') }}" alt="Favicon" class="asset-preview" style="max-height: 48px;">
                                            <div class="fs-9 text-white-30 uppercase letter-spacing-1">64x Scale</div>
                                        </div>
                                        <input type="file" name="favicon" class="form-control stealth-input" accept="image/x-icon,image/png">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-general" role="tabpanel">
                            <div class="config-card">
                                <h6 class="config-title"><iconify-icon icon="tabler:command"></iconify-icon> System DNA</h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="field-label">Registry Title</label>
                                        <input type="text" name="site_title" class="stealth-input" value="{{ $settings['site_title'] ?? 'FitPaxPro' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="field-label">Admin Node Email</label>
                                        <input type="email" name="admin_email" class="stealth-input" value="{{ $settings['admin_email'] ?? 'admin@fitpaxpro.com' }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="field-label">Support Loop</label>
                                        <input type="text" name="contact_number" class="stealth-input" value="{{ $settings['contact_number'] ?? '+1 234 567 890' }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="field-label">HQ Coordinates</label>
                                        <textarea name="office_address" class="stealth-input" rows="3">{{ $settings['office_address'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="tab-localization" role="tabpanel">
                            <div class="config-card">
                                <h6 class="config-title"><iconify-icon icon="tabler:world"></iconify-icon> Geo-Loc Mapping</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="field-label">Global Currency</label>
                                        <select name="currency" class="form-select stealth-input">
                                            <option value="USD" {{ ($settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>USD</option>
                                            <option value="INR" {{ ($settings['currency'] ?? '') == 'INR' ? 'selected' : '' }}>INR</option>
                                            <option value="EUR" {{ ($settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>EUR</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="field-label">Prefix Symbol</label>
                                        <input type="text" name="currency_symbol" class="stealth-input" value="{{ $settings['currency_symbol'] ?? '$' }}">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="field-label">Timezone Sync</label>
                                        <select class="form-select stealth-input">
                                            <option value="UTC">UTC (GLOBAL)</option>
                                            <option value="Asia/Kolkata">ASIA/KOLKATA (IST)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
