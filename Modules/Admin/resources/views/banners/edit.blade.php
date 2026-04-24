<x-app-layout title="New Banner Deployment | Admin Command Center">
    <style>
        .form-wrapper { padding: 40px 0; max-width: 900px; margin: 0 auto; }
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; text-transform: uppercase; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }

        .tactical-card {
            background: #121418; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 24px; padding: 40px; margin-bottom: 30px;
        }

        .field-label { 
            display: block; font-size: 0.65rem; font-weight: 800; color: rgba(255,255,255,0.4); 
            text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;
        }
        .stealth-input { 
            background: #08090b !important; border: 1px solid rgba(255,255,255,0.06) !important; 
            color: #fff !important; border-radius: 14px !important; font-size: 0.9rem; 
            padding: 12px 20px !important; transition: 0.3s !important;
        }
        .stealth-input:focus { border-color: var(--rich-red) !important; box-shadow: 0 0 15px rgba(225,18,24,0.1) !important; background: #000 !important; }

        .sync-btn {
            background: linear-gradient(to right, #E11218, #9c0c11);
            color: #fff; border: none; padding: 16px 40px; border-radius: 16px;
            font-weight: 900; text-transform: uppercase; letter-spacing: 2px; font-size: 0.85rem;
            box-shadow: 0 10px 30px rgba(225,18,24,0.3); transition: 0.3s; width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 12px;
        }
        .sync-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(225,18,24,0.5); }

        .back-link {
            color: rgba(255,255,255,0.3); text-decoration: none; font-size: 0.7rem;
            font-weight: 800; text-transform: uppercase; letter-spacing: 1px;
            display: flex; align-items: center; gap: 8px; margin-bottom: 25px; transition: 0.2s;
        }
        .back-link:hover { color: #fff; }
    </style>

    <div class="form-wrapper">
        <a href="{{ route('admin.banners.index') }}" class="back-link">
            <iconify-icon icon="tabler:arrow-left"></iconify-icon> RETURN TO REGISTRY
        </a>

        <div class="page-header mb-5">
            <h4>Initialize Deployment</h4>
            <p>Define Banner parameters and targeting logic</p>
        </div>

        <form action="{{ isset($banner) ? route('admin.banners.update', $banner->id) : route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($banner)) @method('PUT') @endif

            <div class="tactical-card">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="field-label">Banner Asset (Optimal 1200x400)</label>
                        <input type="file" name="image" class="form-control stealth-input" {{ isset($banner) ? '' : 'required' }}>
                        @if(isset($banner) && $banner->image_url)
                            <div class="mt-3 opacity-50 fs-10 uppercase letter-spacing-1">Current: {{ $banner->image_url }}</div>
                        @endif
                    </div>

                    <div class="col-md-8">
                        <label class="field-label">Primary Headline</label>
                        <input type="text" name="title" class="form-control stealth-input" value="{{ old('title', $banner->title ?? '') }}" placeholder="e.g. Save 50% on Annual Membership" required>
                    </div>

                    <div class="col-md-4">
                        <label class="field-label">Badge Identifier</label>
                        <input type="text" name="badge_text" class="form-control stealth-input" value="{{ old('badge_text', $banner->badge_text ?? '') }}" placeholder="e.g. OFFER">
                    </div>

                    <div class="col-md-6">
                        <label class="field-label">Target Link / Protocol</label>
                        <input type="text" name="target_link" class="form-control stealth-input" value="{{ old('target_link', $banner->target_link ?? '') }}" placeholder="e.g. plan_premium_id">
                    </div>

                    <div class="col-md-3">
                        <label class="field-label">Chroma Node (Hex)</label>
                        <div class="d-flex gap-2">
                            <input type="color" class="form-control stealth-input p-1" style="width: 50px;" 
                                value="{{ old('background_color_hex', $banner->background_color_hex ?? '#E11218') }}"
                                oninput="document.getElementById('hex_text').value = this.value">
                            <input type="text" name="background_color_hex" id="hex_text" class="form-control stealth-input" 
                                value="{{ old('background_color_hex', $banner->background_color_hex ?? '#E11218') }}"
                                oninput="this.previousElementSibling.value = this.value">
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="field-label">Priority Order</label>
                        <input type="number" name="order" class="form-control stealth-input" value="{{ old('order', $banner->order ?? 0) }}">
                    </div>
                </div>
            </div>

            <button type="submit" class="sync-btn">
                {{ isset($banner) ? 'SYNC INTELLIGENCE' : 'CONFIRM DEPLOYMENT' }}
                <iconify-icon icon="tabler:upload" class="fs-18"></iconify-icon>
            </button>
        </form>
    </div>
</x-app-layout>
