<x-app-layout title="Membership Architect | FitPaxPro">
    @push('styles')
    <style>
        /* Stealth Dark Membership Architect */
        :root {
            --plan-bg: #0b0d0f;
            --plan-card: #121418;
            --plan-input: #08090b;
            --plan-border: rgba(255, 255, 255, 0.05);
            --plan-accent: #E11218;
            --plan-text: #ffffff;
            --plan-muted: rgba(255, 255, 255, 0.4);
            --plan-glow: rgba(225, 18, 24, 0.15);
        }

        body { background-color: var(--plan-bg) !important; color: #fff !important; }

        .architect-container { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        
        .page-header h2 { font-weight: 900; letter-spacing: -1.5px; color: #fff; margin-bottom: 5px; text-transform: uppercase; }
        .page-header p { color: var(--plan-muted); font-size: 0.85rem; letter-spacing: 0.5px; }

        /* Tactical Cards */
        .plan-card { 
            background: var(--plan-card); 
            border: 1px solid var(--plan-border); 
            border-radius: 24px; 
            margin-bottom: 30px; 
            transition: all 0.4s;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }
        .plan-card-header { 
            padding: 22px 30px; border-bottom: 1px solid var(--plan-border); 
            display: flex; align-items: center; gap: 12px;
        }
        .plan-card-header h6 { margin: 0; font-weight: 800; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--plan-accent); }
        .plan-card-header iconify-icon { font-size: 1.3rem; }
        .plan-card-body { padding: 35px; }

        /* Field Styling */
        .field-label { 
            display: block; font-size: 0.65rem; font-weight: 900; color: var(--plan-muted); 
            text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 12px;
        }
        
        .input-node { position: relative; }
        .input-node iconify-icon { 
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%); 
            color: var(--plan-muted); font-size: 1.1rem; transition: 0.3s;
        }
        
        .plan-input { 
            background: var(--plan-input) !important; border: 1px solid var(--plan-border) !important; 
            color: #fff !important; border-radius: 16px !important; font-size: 0.95rem; 
            padding: 16px 20px 16px 50px !important; height: 56px !important; width: 100%; transition: 0.3s !important;
        }
        .plan-input:focus { 
            border-color: var(--plan-accent) !important; box-shadow: 0 0 15px var(--plan-glow) !important; outline: none; background: #000 !important;
        }
        .plan-input:focus + iconify-icon { color: var(--plan-accent); }

        .plan-textarea { 
            background: var(--plan-input) !important; border: 1px solid var(--plan-border) !important; 
            color: #fff !important; border-radius: 16px !important; font-size: 0.95rem; 
            padding: 16px !important; width: 100%; min-height: 100px; transition: 0.3s;
        }
        .plan-textarea:focus { border-color: var(--plan-accent) !important; background: #000 !important; outline: none; }

        /* Feature Toggles */
        .premium-toggle {
            background: var(--plan-input); border: 1px solid var(--plan-border);
            border-radius: 20px; padding: 20px; display: flex; align-items: center; justify-content: space-between; transition: 0.3s;
        }
        .premium-toggle:hover { border-color: rgba(225,18,24,0.3); background: #000; }
        .premium-toggle h6 { margin: 0; font-size: 0.9rem; font-weight: 700; color: #fff; }
        .premium-toggle p { margin: 2px 0 0; font-size: 0.7rem; color: var(--plan-muted); }

        .form-check-input { width: 44px; height: 22px; cursor: pointer; background-color: rgba(255,255,255,0.05); border: 1px solid var(--plan-border); }
        .form-check-input:checked { background-color: var(--plan-accent); border-color: var(--plan-accent); box-shadow: 0 0 10px var(--plan-glow); }

        /* Card Preview Sidebar */
        .sticky-card { position: sticky; top: 100px; }
        .membership-preview {
            background: linear-gradient(135deg, #16191d 0%, #0b0d0f 100%);
            border: 1px solid rgba(225,18,24,0.3); border-radius: 28px; padding: 35px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.6); position: relative; overflow: hidden;
        }
        .preview-glow { position: absolute; top: -50px; right: -50px; width: 150px; height: 150px; background: radial-gradient(circle, rgba(225,18,24,0.1) 0%, transparent 70%); }
        
        .preview-badge { 
            background: var(--plan-accent); color: #fff; font-size: 0.6rem; font-weight: 900; 
            padding: 4px 12px; border-radius: 50px; text-transform: uppercase; letter-spacing: 1px; display: inline-block; margin-bottom: 20px;
        }

        .btn-publish-plan {
            background: linear-gradient(to right, #E11218, #9c0c11);
            border: none; color: white; font-weight: 800; padding: 18px; border-radius: 18px;
            font-size: 0.9rem; width: 100%; text-transform: uppercase; letter-spacing: 1.5px; transition: 0.4s;
            box-shadow: 0 10px 30px rgba(225,18,24,0.3); display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-publish-plan:hover { transform: translateY(-3px); box-shadow: 0 15px 45px rgba(225,18,24,0.5); }

    </style>
    @endpush

    <div class="architect-container">
        <div class="row align-items-end mb-5">
            <div class="col-md-9">
                <div class="page-header text-uppercase">
                    <h2>Membership Architect</h2>
                    <p>ENGINEERING ELITE SUBSCRIPTION TIERS FOR YOUR REVENUE STREAM</p>
                </div>
            </div>
            <div class="col-md-3 text-md-end">
                <a href="{{ route('gym.plans.index') }}" class="btn btn-dark px-4 py-2 rounded-pill fs-12 border-secondary border-opacity-25 shadow-none">
                    <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> EXIT ARCHITECT
                </a>
            </div>
        </div>

        <form action="{{ route('gym.plans.store') }}" method="POST">
            @csrf
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- Identity & Logic -->
                    <div class="plan-card">
                        <div class="plan-card-header">
                            <iconify-icon icon="tabler:atom-2"></iconify-icon>
                            <h6>IDENTITY & BRANDING</h6>
                        </div>
                        <div class="plan-card-body">
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="field-label">Tier Designation</label>
                                    <div class="input-node">
                                        <input type="text" name="name" id="planName" class="plan-input" placeholder="e.g. ULTIMATE PERFORMANCE" required>
                                        <iconify-icon icon="tabler:signature"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="field-label">Visual Badge</label>
                                    <div class="input-node">
                                        <input type="text" name="tagline" id="planTagline" class="plan-input" placeholder="e.g. ELITE ONLY">
                                        <iconify-icon icon="tabler:award"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="field-label">Strategic Narrative</label>
                                    <textarea name="description" id="planDesc" class="plan-textarea" placeholder="Describe the exclusive value proposition of this tier..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Economics & Tenure -->
                    <div class="plan-card">
                        <div class="plan-card-header">
                            <iconify-icon icon="tabler:currency-real"></iconify-icon>
                            <h6>ECONOMIC PARAMETERS</h6>
                        </div>
                        <div class="plan-card-body">
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="field-label">Base Valuation (₹)</label>
                                    <div class="input-node">
                                        <input type="number" step="0.01" name="price" id="planPrice" class="plan-input" placeholder="0.00" required>
                                        <iconify-icon icon="tabler:receipt"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="field-label">Tactical Offer (₹)</label>
                                    <div class="input-node">
                                        <input type="number" step="0.01" name="offer_price" id="planOffer" class="plan-input" placeholder="OPTIONAL">
                                        <iconify-icon icon="tabler:discount-2"></iconify-icon>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="field-label">Tenure (Months)</label>
                                    <div class="input-node">
                                        <input type="number" name="duration_months" id="planDuration" class="plan-input" value="12" required>
                                        <iconify-icon icon="tabler:clock-bolt"></iconify-icon>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Privilege Highlights -->
                    <div class="plan-card">
                        <div class="plan-card-header">
                            <iconify-icon icon="tabler:list-details"></iconify-icon>
                            <h6>PRIVILEGE HIGHLIGHTS</h6>
                        </div>
                        <div class="plan-card-body">
                            <label class="field-label">System Features (One Per Line)</label>
                            <textarea name="features_list" id="planFeatures" class="plan-textarea" placeholder="Full Access to Ops Center&#10;Tactical Equipment Access&#10;Bioluminescent Locker Access" rows="4"></textarea>
                            <p class="fs-10 text-white-30 mt-3 letter-spacing-1">INTERNAL NOTE: THESE HIGHLIGHTS WILL BE RENDERED IN THE ELITE MEMBER MOBILE PORTAL.</p>
                        </div>
                    </div>

                    <!-- Dynamic Extended Data -->
                    <x-dynamic-fields model-type="App\Models\MembershipPlanTemplate" />

                    <!-- Advanced Tactical Modules -->
                    <div class="plan-card">
                        <div class="plan-card-header">
                            <iconify-icon icon="tabler:assembly"></iconify-icon>
                            <h6>ADVANCED TACTICAL MODULES</h6>
                        </div>
                        <div class="plan-card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="premium-toggle">
                                        <div>
                                            <h6>Command Trainer</h6>
                                            <p>Access to dedicated performance coach.</p>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="includes_trainer" value="1">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="premium-toggle">
                                        <div>
                                            <h6>Nutrition Intelligence</h6>
                                            <p>Custom biological fueling protocols.</p>
                                        </div>
                                        <div class="form-check form-switch m-0">
                                            <input class="form-check-input" type="checkbox" name="includes_diet_plan" value="1">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LIVE PREVIEW SIDEBAR -->
                <div class="col-lg-4">
                    <div class="sticky-card">
                        <div class="membership-preview mb-4">
                            <div class="preview-glow"></div>
                            <div id="previewBadgeCont" style="display: none;">
                                <span class="preview-badge" id="previewBadge">ELITE</span>
                            </div>
                            
                            <h2 class="text-white fw-900 mb-1" id="previewName">SYSTEM TIER</h2>
                            <p class="text-white-30 fs-11 mb-4" id="previewDesc">Awaiting structural input...</p>
                            
                            <div class="mb-4">
                                <h1 class="text-danger fw-900 mb-0">₹<span id="previewPrice">0</span></h1>
                                <span class="fs-10 text-white-50 letter-spacing-2" id="previewDuration">FOR 12 MONTH CYCLE</span>
                            </div>

                            <div class="vstack gap-2" id="previewFeatures">
                                <div class="d-flex align-items-center gap-2">
                                    <iconify-icon icon="tabler:circle-check" class="text-danger fs-12"></iconify-icon>
                                    <span class="text-white-50 fs-11">Awaiting core features...</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-publish-plan">
                            DEPLOY TIER PROTOCOL <iconify-icon icon="tabler:rocket" class="fs-16"></iconify-icon>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = {
                name: document.getElementById('planName'),
                tagline: document.getElementById('planTagline'),
                desc: document.getElementById('planDesc'),
                price: document.getElementById('planPrice'),
                offer: document.getElementById('planOffer'),
                duration: document.getElementById('planDuration'),
                features: document.getElementById('planFeatures')
            };

            const previews = {
                name: document.getElementById('previewName'),
                badge: document.getElementById('previewBadge'),
                badgeCont: document.getElementById('previewBadgeCont'),
                desc: document.getElementById('previewDesc'),
                price: document.getElementById('previewPrice'),
                duration: document.getElementById('previewDuration'),
                features: document.getElementById('previewFeatures')
            };

            const updatePreview = () => {
                previews.name.innerText = inputs.name.value.toUpperCase() || 'SYSTEM TIER';
                
                if(inputs.tagline.value) {
                    previews.badge.innerText = inputs.tagline.value.toUpperCase();
                    previews.badgeCont.style.display = 'block';
                } else {
                    previews.badgeCont.style.display = 'none';
                }

                previews.desc.innerText = inputs.desc.value || 'Awaiting structural input...';
                
                const finalPrice = inputs.offer.value || inputs.price.value || 0;
                previews.price.innerText = parseFloat(finalPrice).toLocaleString();
                
                previews.duration.innerText = `FOR ${inputs.duration.value || 1} MONTH CYCLE`;

                // Features logic
                const features = inputs.features.value.split('\n').filter(f => f.trim() !== '');
                if(features.length > 0) {
                    previews.features.innerHTML = features.slice(0, 3).map(f => `
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="tabler:circle-check" class="text-danger fs-12"></iconify-icon>
                            <span class="text-white-50 fs-11">${f}</span>
                        </div>
                    `).join('');
                } else {
                    previews.features.innerHTML = `
                        <div class="d-flex align-items-center gap-2">
                            <iconify-icon icon="tabler:circle-check" class="text-danger fs-12"></iconify-icon>
                            <span class="text-white-50 fs-11">Awaiting core features...</span>
                        </div>
                    `;
                }
            };

            Object.values(inputs).forEach(input => {
                input.addEventListener('input', updatePreview);
            });
        });
    </script>
    @endpush
</x-app-layout>
