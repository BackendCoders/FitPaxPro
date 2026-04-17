<x-app-layout title="Expert Tier Design | FitPaxPro">
    @push('styles')
    <style>
        /* Stealth Dark Professional Theme */
        :root {
            --stealth-bg: #0b0d0f;
            --stealth-card: #121418;
            --stealth-input: #08090b;
            --stealth-border: rgba(255, 255, 255, 0.04);
            --stealth-accent: #E11218;
            --stealth-text-dim: rgba(255, 255, 255, 0.4);
            --stealth-glow: rgba(225, 18, 24, 0.15);
        }

        body { background-color: var(--stealth-bg) !important; color: #fff !important; }
        .main-content { background-color: var(--stealth-bg) !important; }

        .creator-wrapper { max-width: 1200px; margin: 0 auto; padding: 40px 20px; }
        
        /* Typography */
        .page-heading { font-weight: 900; letter-spacing: -1.5px; color: #fff; margin-bottom: 5px; }
        .page-subheading { color: var(--stealth-text-dim); font-size: 0.85rem; letter-spacing: 0.5px; }

        /* Cards */
        .stealth-card { 
            background: var(--stealth-card); 
            border: 1px solid var(--stealth-border); 
            border-radius: 24px; 
            margin-bottom: 30px; 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }
        .stealth-card:hover { border-color: rgba(225, 18, 24, 0.3); transform: translateY(-2px); }
        
        .stealth-header { 
            padding: 24px 30px; 
            border-bottom: 1px solid var(--stealth-border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .stealth-header h6 { margin: 0; font-weight: 800; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 2px; color: var(--stealth-accent); }
        .stealth-header iconify-icon { font-size: 1.4rem; }
        
        .stealth-body { padding: 35px; }

        /* Professional Fields UI */
        .field-group { margin-bottom: 25px; position: relative; }
        .field-label { 
            display: block; font-size: 0.65rem; font-weight: 900; 
            color: var(--stealth-text-dim); text-transform: uppercase; 
            letter-spacing: 1.5px; margin-bottom: 10px; padding-left: 2px;
        }
        
        .stealth-input-container { position: relative; }
        .stealth-input-container iconify-icon { 
            position: absolute; left: 18px; top: 50%; transform: translateY(-50%); 
            color: var(--stealth-text-dim); font-size: 1.2rem; transition: 0.3s;
            z-index: 2;
        }
        
        .stealth-input { 
            background: var(--stealth-input) !important; 
            border: 1px solid var(--stealth-border) !important; 
            color: #fff !important; 
            border-radius: 16px !important; 
            font-size: 0.95rem; 
            padding: 16px 20px 16px 52px !important; 
            height: 58px !important;
            transition: all 0.3s !important; 
            width: 100%;
            caret-color: var(--stealth-accent);
        }
        .stealth-input::placeholder { color: rgba(255,255,255,0.1); }
        .stealth-input:focus { 
            background: #000 !important; 
            border-color: var(--stealth-accent) !important; 
            box-shadow: 0 0 20px var(--stealth-glow) !important;
            outline: none;
        }
        .stealth-input:focus + iconify-icon { color: var(--stealth-accent); }

        /* Switches */
        .stealth-switch-box {
            background: var(--stealth-input); border: 1px solid var(--stealth-border);
            border-radius: 20px; padding: 22px; transition: 0.3s; display: flex; 
            align-items: center; justify-content: space-between; margin-bottom: 15px;
        }
        .stealth-switch-box:hover { border-color: rgba(225,18,24,0.2); background: #000; }
        .stealth-switch-box h6 { margin: 0; font-size: 0.95rem; font-weight: 700; color: #fff; }
        .stealth-switch-box p { margin: 4px 0 0; font-size: 0.75rem; color: var(--stealth-text-dim); }

        .form-check-input {
            width: 46px; height: 24px; background-color: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.1); cursor: pointer; transition: 0.3s;
        }
        .form-check-input:checked { background-color: var(--stealth-accent); border-color: var(--stealth-accent); box-shadow: 0 0 10px var(--stealth-glow); }

        /* Sidebar Preview */
        .stealth-sidebar { position: sticky; top: 100px; }
        .blueprint-card {
            background: linear-gradient(135deg, #16191d 0%, #0b0d0f 100%);
            border: 1px solid rgba(225,18,24,0.3); border-radius: 28px; padding: 35px;
            box-shadow: 0 20px 50px rgba(0,0,0,0.6);
        }
        .blueprint-label { font-size: 0.6rem; font-weight: 900; color: var(--stealth-accent); letter-spacing: 2.5px; margin-bottom: 25px; display: block; }
        
        /* Buttons */
        .btn-stealth-submit {
            background: linear-gradient(to right, #E11218, #9c0c11);
            border: none; color: white; font-weight: 800;
            padding: 18px; border-radius: 18px; font-size: 0.95rem; width: 100%;
            text-transform: uppercase; letter-spacing: 1.5px; transition: all 0.4s;
            box-shadow: 0 10px 30px rgba(225,18,24,0.3);
            display: flex; align-items: center; justify-content: center; gap: 10px;
        }
        .btn-stealth-submit:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(225,18,24,0.5); }

    </style>
    @endpush

    <div class="creator-wrapper">
        <div class="row align-items-end mb-5">
            <div class="col-md-8">
                <h1 class="page-heading">PLAN ARCHITECT</h1>
                <p class="page-subheading">CONSTRUCTING GLOBAL ECONOMIC INFRASTRUCTURE</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="{{ route('admin.platform-plans.index') }}" class="btn btn-dark px-4 py-2 rounded-pill fs-12 border-secondary border-opacity-25 shadow-none">
                    <iconify-icon icon="tabler:logout-2" class="me-1 align-middle"></iconify-icon> ABORT DESIGN
                </a>
            </div>
        </div>

        <form action="{{ route('admin.platform-plans.store') }}" method="POST" id="stealthForm">
            @csrf
            <div class="row g-5">
                <div class="col-lg-8">
                    <!-- SECTION: CORE IDENTITY -->
                    <div class="stealth-card">
                        <div class="stealth-header">
                            <h6><iconify-icon icon="tabler:binary-tree" class="me-2 align-middle"></iconify-icon> CORE IDENTITY</h6>
                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-2 fs-10 fw-bold">PROTOCOL 01</span>
                        </div>
                        <div class="stealth-body">
                            <div class="field-group">
                                <label class="field-label">System Tier Designation</label>
                                <div class="stealth-input-container">
                                    <input type="text" name="name" id="nameInput" class="stealth-input" placeholder="e.g. OMNI-REVENUE NODE" required>
                                    <iconify-icon icon="tabler:id"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: ECONOMIC MODEL -->
                    <div class="stealth-card">
                        <div class="stealth-header">
                            <h6><iconify-icon icon="tabler:currency-bitcoin" class="me-2 align-middle"></iconify-icon> ECONOMIC MODEL</h6>
                        </div>
                        <div class="stealth-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="field-group">
                                        <label class="field-label">Monthly Retention Rate (₹)</label>
                                        <div class="stealth-input-container">
                                            <input type="number" step="0.01" name="monthly_price" id="priceInput" class="stealth-input" placeholder="0.00" required>
                                            <iconify-icon icon="tabler:receipt-2"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-group">
                                        <label class="field-label">Annual Commitment (₹)</label>
                                        <div class="stealth-input-container">
                                            <input type="number" step="0.01" name="yearly_price" class="stealth-input" placeholder="OPTIONAL">
                                            <iconify-icon icon="tabler:lock-star"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: SCALABILITY -->
                    <div class="stealth-card">
                        <div class="stealth-header">
                            <h6><iconify-icon icon="tabler:schema" class="me-2 align-middle"></iconify-icon> SCALABILITY MESH</h6>
                        </div>
                        <div class="stealth-body">
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="field-group">
                                        <label class="field-label">Max Node Count (Gyms)</label>
                                        <div class="stealth-input-container">
                                            <input type="number" name="max_gyms" id="gymsInput" class="stealth-input" value="1" required>
                                            <iconify-icon icon="tabler:server-cog"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="field-group">
                                        <label class="field-label">Member Density (Per Node)</label>
                                        <div class="stealth-input-container">
                                            <input type="number" name="max_members" id="membersInput" class="stealth-input" placeholder="INFINITE">
                                            <iconify-icon icon="tabler:user-plus"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION: ADVANCED MODULES -->
                    <div class="stealth-card">
                        <div class="stealth-header">
                            <h6><iconify-icon icon="tabler:plug-connected" class="me-2 align-middle"></iconify-icon> OPT-IN MODULES</h6>
                        </div>
                        <div class="stealth-body">
                            <div class="stealth-switch-box">
                                <div>
                                    <h6>Neural Analytics Core</h6>
                                    <p>Deploy real-time business intelligence and forecasting.</p>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="has_analytics" value="1">
                                </div>
                            </div>
                            <div class="stealth-switch-box">
                                <div>
                                    <h6>Mobile Asset Extension</h6>
                                    <p>Propagate white-labeled native mobile application nodes.</p>
                                </div>
                                <div class="form-check form-switch m-0">
                                    <input class="form-check-input" type="checkbox" name="has_mobile_app" value="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SIDEBAR PREVIEW -->
                <div class="col-lg-4">
                    <div class="stealth-sidebar">
                        <div class="blueprint-card mb-4">
                            <span class="blueprint-label">DATA BLUEPRINT</span>
                            
                            <h2 class="text-white fw-900 mb-2" id="previewName">PENDING ID...</h2>
                            <div class="mb-4">
                                <span class="fs-12 text-white-50">ESTIMATED YIELD</span>
                                <h1 class="text-danger fw-900 mb-0">₹<span id="previewPrice">0.00</span></h1>
                                <span class="fs-10 text-white-30 letter-spacing-1">PER NODE / MONTHLY</span>
                            </div>

                            <hr class="border-secondary opacity-10 my-4">

                            <div class="vstack gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <iconify-icon icon="tabler:router" class="text-danger"></iconify-icon>
                                    <span class="fs-12 text-white opacity-80"><strong id="previewGyms">1</strong> NODES AUTHORIZED</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <iconify-icon icon="tabler:users" class="text-danger"></iconify-icon>
                                    <span class="fs-12 text-white opacity-80"><strong id="previewMembers">INFINITE</strong> MEMBERS / NODE</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn-stealth-submit">
                            INITIALIZE PROTOCOL <iconify-icon icon="tabler:send" class="fs-16"></iconify-icon>
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
                name: document.getElementById('nameInput'),
                price: document.getElementById('priceInput'),
                gyms: document.getElementById('gymsInput'),
                members: document.getElementById('membersInput')
            };

            const previews = {
                name: document.getElementById('previewName'),
                price: document.getElementById('previewPrice'),
                gyms: document.getElementById('previewGyms'),
                members: document.getElementById('previewMembers')
            };

            const updatePreview = () => {
                previews.name.innerText = inputs.name.value.toUpperCase() || 'PENDING ID...';
                previews.price.innerText = parseFloat(inputs.price.value || 0).toLocaleString(undefined, {minimumFractionDigits: 2});
                previews.gyms.innerText = inputs.gyms.value || '1';
                previews.members.innerText = inputs.members.value || 'INFINITE';
            };

            Object.values(inputs).forEach(input => {
                input.addEventListener('input', updatePreview);
            });
        });
    </script>
    @endpush
</x-app-layout>
