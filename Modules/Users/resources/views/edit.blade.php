<x-app-layout title="System Calibration | FitPaxPro">
    @push('styles')
    <style>
        .edit-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 24px; padding: 40px; }
        .input-dark { background: #1c2126 !important; border: 1px solid rgba(255,255,255,0.05) !important; color: #fff !important; border-radius: 12px !important; padding: 12px 15px !important; }
        .input-dark:focus { border-color: #E11218 !important; box-shadow: 0 0 0 4px rgba(225,18,24,0.1) !important; }
        .form-label-tactical { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: rgba(255,255,255,0.4); margin-bottom: 8px; }
        
        .role-badge { 
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05); 
            border-radius: 16px; padding: 15px 20px; cursor: pointer; transition: 0.3s;
            display: flex; align-items: center; justify-content: space-between;
        }
        .role-badge:hover { background: rgba(255,255,255,0.04); }
        .role-badge.selected { background: rgba(225,18,24,0.05); border-color: #E11218; }
        
        .side-panel { background: #0d0f12; border-left: 1px solid rgba(255,255,255,0.05); padding: 40px; height: 100%; border-radius: 0 24px 24px 0; }
        .neon-switch .form-check-input { width: 3em; height: 1.5em; background-color: rgba(255,255,255,0.1); border-color: transparent; }
        .neon-switch .form-check-input:checked { background-color: #E11218; border-color: #E11218; }
    </style>
    @endpush

    <div class="row align-items-center mb-5">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Node Calibration</h4>
            <p class="text-white-50 fs-14 mb-0">Modifying operational parameters for <strong>{{ $user->name }}</strong></p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-dark px-4 py-2 border-0 shadow-lg" style="background: #16191d;">
                <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> BACK TO INTELLIGENCE
            </a>
        </div>
    </div>

    <div class="row g-0 justify-content-center">
        <div class="col-xl-11">
            <div class="edit-card shadow-2xl p-0 overflow-hidden">
                <div class="row g-0">
                    <div class="col-lg-7 p-4 p-xl-5">
                        <form action="{{ route('users.update', $user->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-5">
                                <h6 class="text-white fw-bold mb-4 d-flex align-items-center">
                                    <span class="badge bg-primary me-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span> 
                                    CORE IDENTITY
                                </h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <label class="form-label-tactical">FULL OPERATIVE NAME</label>
                                        <input type="text" name="name" class="form-control input-dark @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                        @error('name') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-tactical">ENCRYPTED EMAIL ADDRESS</label>
                                        <input type="email" name="email" class="form-control input-dark @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                        @error('email') <div class="text-danger fs-11 mt-1">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-tactical">SECURE COMMS (PHONE)</label>
                                        <input type="text" name="phone" class="form-control input-dark" value="{{ old('phone', $user->phone) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-5">
                                <h6 class="text-white fw-bold mb-4 d-flex align-items-center">
                                    <span class="badge bg-warning me-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span> 
                                    AUTHORIZATION CLEARANCE
                                </h6>
                                <div class="row g-3">
                                    @foreach($roles as $role)
                                    <div class="col-sm-6">
                                        <label class="role-badge {{ $user->hasRole($role->name) ? 'selected' : '' }}" for="role_{{ $role->id }}">
                                            <div>
                                                <h6 class="text-white fs-14 mb-0">{{ strtoupper($role->name) }}</h6>
                                                <small class="text-white-50 fs-10">Access Tier {{ $loop->iteration }}</small>
                                            </div>
                                            <div class="form-check m-0">
                                                <input class="form-check-input neon-switch" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ $user->hasRole($role->name) ? 'checked' : '' }}>
                                            </div>
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mb-5">
                                <x-dynamic-fields model-type="App\Models\User" :model="$user" />
                            </div>

                            <div class="pt-2">
                                <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 shadow-none fw-bold uppercase letter-spacing-1">
                                    APPLY SYSTEM CALIBRATION
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="col-lg-5">
                        <div class="side-panel">
                            <h6 class="text-white fw-bold mb-4 d-flex align-items-center">
                                <span class="badge bg-danger me-2" style="width: 10px; height: 10px; border-radius: 50%; padding: 0;"> </span> 
                                OPERATIONAL STATUS
                            </h6>
                            <div class="p-4 bg-dark bg-opacity-50 rounded-4 border border-secondary border-opacity-10 mb-4 neon-switch">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <h6 class="text-white fs-14 mb-1">Node Activity Status</h6>
                                        <p class="text-white-50 fs-11 mb-0">Toggle encrypted session access</p>
                                    </div>
                                    <input class="form-check-input" type="checkbox" name="status" value="1" form="calibration-form" {{ $user->status ? 'checked' : '' }}>
                                </div>
                                <div class="alert alert-info bg-info bg-opacity-10 border-0 fs-11 text-white-50 mb-0">
                                    Offline status will immediately terminate all active API keys and web sessions.
                                </div>
                            </div>

                            <h6 class="text-white fw-bold mb-4 mt-5 d-flex align-items-center uppercase fs-11 letter-spacing-1">Node Analytics</h6>
                            <div class="vstack gap-3">
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-white-50 fs-12">Initial Connection</span>
                                    <span class="text-white fs-12 fw-bold">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom border-secondary border-opacity-10">
                                    <span class="text-white-50 fs-12">Last Sync Log</span>
                                    <span class="text-white fs-12 fw-bold">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center py-2">
                                    <span class="text-white-50 fs-12">Verified Subscriptions</span>
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-2">{{ $user->gymSubscriptions->count() }}</span>
                                </div>
                            </div>

                            <div class="mt-auto pt-5">
                                <div class="text-center p-3 bg-black bg-opacity-20 rounded-3">
                                    <p class="mb-0 fs-11 text-uppercase fw-bold letter-spacing-1 opacity-50">Log ID Reference</p>
                                    <code class="text-white fs-14">{{ $user->id }}</code>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
