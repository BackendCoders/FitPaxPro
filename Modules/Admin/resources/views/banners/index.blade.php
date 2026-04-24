<x-app-layout title="Banner Intel | Admin Command Center">
    <style>
        .banner-grid { padding: 25px 0; }
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; text-transform: uppercase; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }

        .tactical-card {
            background: #121418; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px; overflow: hidden; transition: 0.3s;
        }
        .tactical-card:hover { border-color: rgba(225,18,24,0.3); transform: translateY(-5px); }

        .banner-preview-box {
            height: 160px; background: #08090b; display: flex; align-items: center; justify-content: center;
            overflow: hidden; position: relative;
        }
        .banner-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        
        .status-badge {
            position: absolute; top: 15px; right: 15px; padding: 5px 12px;
            border-radius: 20px; font-size: 0.6rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1px; backdrop-filter: blur(10px);
        }
        .status-active { background: rgba(0, 255, 136, 0.1); color: #00ff88; border: 1px solid rgba(0,255,136,0.2); }
        .status-inactive { background: rgba(255, 51, 51, 0.1); color: #ff3333; border: 1px solid rgba(255,51,51,0.2); }

        .banner-info { padding: 20px; }
        .banner-title { font-size: 0.9rem; font-weight: 800; color: #fff; margin-bottom: 5px; }
        .banner-meta { font-size: 0.65rem; color: rgba(255,255,255,0.3); font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; }

        .action-row {
            display: flex; gap: 10px; padding: 15px 20px; background: rgba(255,255,255,0.02);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        .btn-stealth {
            flex: 1; padding: 8px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255,255,255,0.03); color: rgba(255,255,255,0.5); font-size: 0.7rem;
            font-weight: 800; text-transform: uppercase; letter-spacing: 1px; transition: 0.2s;
            text-align: center; text-decoration: none;
        }
        .btn-stealth:hover { background: #fff; color: #000; }
        .btn-delete:hover { background: #E11218; color: #fff; border-color: #E11218; }

        .launch-btn {
            background: linear-gradient(to right, #E11218, #9c0c11);
            color: #fff; border: none; padding: 12px 25px; border-radius: 12px;
            font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.75rem;
            box-shadow: 0 10px 20px rgba(225,18,24,0.2); transition: 0.3s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .launch-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(225,18,24,0.4); color: #fff; }
    </style>

    <div class="banner-grid container-fluid">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <div class="page-header">
                    <h4>Banner Intelligence</h4>
                    <p>App Promotional Visual Assets & Targeting</p>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('admin.banners.create') }}" class="launch-btn">
                    <iconify-icon icon="tabler:plus" class="fs-18"></iconify-icon> NEW DEPLOYMENT
                </a>
            </div>
        </div>

        <div class="row g-4">
            @foreach($banners as $banner)
            <div class="col-md-4 col-lg-3">
                <div class="tactical-card">
                    <div class="banner-preview-box">
                        <img src="{{ $banner->image_url }}" alt="Preview">
                        <span class="status-badge {{ $banner->is_active ? 'status-active' : 'status-inactive' }}">
                            {{ $banner->is_active ? 'Online' : 'Standby' }}
                        </span>
                    </div>
                    <div class="banner-info">
                        <div class="banner-title">{{ $banner->title }}</div>
                        <div class="banner-meta">
                            <iconify-icon icon="tabler:tag" class="align-middle me-1"></iconify-icon> {{ $banner->badge_text ?? 'No Badge' }}
                        </div>
                    </div>
                    <div class="action-row">
                        <button type="button" class="btn-stealth" 
                                onclick="launchPreview('{{ $banner->image_url }}', '{{ $banner->title }}', '{{ $banner->badge_text }}', '{{ $banner->background_color_hex }}')">
                            Review
                        </button>
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn-stealth">Configure</a>
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-stealth btn-delete w-100" onclick="return confirm('Confirm Decommission?')">Scrub</button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Banner Preview Modal -->
    <div class="modal fade" id="bannerPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0" style="background: transparent;">
                <div class="modal-body p-0">
                    <div class="mobile-mockup-wrapper">
                        <!-- Simulated App Banner Component -->
                        <div id="mock-banner-node" style="width: 100%; aspect-ratio: 21/9; border-radius: 20px; overflow: hidden; position: relative; display: flex; align-items: center; justify-content: flex-start; padding: 25px;">
                            <img id="mock-banner-img" src="" style="position: absolute; top:0; left:0; width:100%; height:100%; object-fit: cover; opacity: 0.9;">
                            <div style="position: relative; z-index: 2;">
                                <span id="mock-banner-badge" style="background: rgba(255,255,255,0.2); backdrop-filter: blur(5px); color: #fff; padding: 4px 10px; border-radius: 6px; font-size: 0.6rem; font-weight: 900; letter-spacing: 1px; text-transform: uppercase;"></span>
                                <h4 id="mock-banner-title" style="color: #fff; font-weight: 900; margin-top: 10px; font-size: 1.2rem; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.3);"></h4>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <button type="button" class="btn-stealth px-4" data-bs-dismiss="modal">Close Visualization</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function launchPreview(img, title, badge, color) {
            const modal = new bootstrap.Modal(document.getElementById('bannerPreviewModal'));
            document.getElementById('mock-banner-img').src = img;
            document.getElementById('mock-banner-title').innerText = title;
            document.getElementById('mock-banner-badge').innerText = badge || 'PROMO';
            document.getElementById('mock-banner-node').style.backgroundColor = color || '#E11218';
            
            modal.show();
        }
    </script>
    @endpush
</x-app-layout>
