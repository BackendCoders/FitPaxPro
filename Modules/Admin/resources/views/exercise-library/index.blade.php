<x-app-layout title="Exercise Library | Admin Command Center">
    <style>
        .exercise-wrapper { padding: 25px 0; }
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; text-transform: uppercase; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }

        .launch-btn {
            background: linear-gradient(to right, #E11218, #9c0c11);
            color: #fff; border: none; padding: 12px 25px; border-radius: 12px;
            font-weight: 900; text-transform: uppercase; letter-spacing: 1.5px; font-size: 0.75rem;
            box-shadow: 0 10px 20px rgba(225,18,24,0.2); transition: 0.3s; text-decoration: none;
            display: inline-flex; align-items: center; gap: 10px;
        }
        .launch-btn:hover { transform: translateY(-3px); box-shadow: 0 15px 30px rgba(225,18,24,0.4); color: #fff; }

        .tactical-card {
            background: #121418; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 22px; overflow: hidden; transition: 0.3s;
            height: 100%;
        }
        .tactical-card:hover { transform: translateY(-4px); border-color: rgba(225,18,24,0.25); }

        .exercise-preview {
            height: 190px; background: linear-gradient(135deg, #08090b, #15181d); overflow: hidden;
            position: relative;
        }
        .exercise-preview img { width: 100%; height: 100%; object-fit: cover; }
        .exercise-preview .fallback {
            width: 100%; height: 100%; display: flex; align-items: center; justify-content: center;
            color: rgba(255,255,255,0.18); font-size: 2rem;
        }

        .exercise-body { padding: 18px 20px 20px; }
        .exercise-title { color: #fff; font-weight: 800; font-size: 1rem; margin-bottom: 8px; }
        .exercise-meta { color: rgba(255,255,255,0.35); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.8px; }
        .exercise-summary { color: rgba(255,255,255,0.65); font-size: 0.82rem; margin-top: 12px; min-height: 42px; }

        .pill-row { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 14px; }
        .pill {
            padding: 5px 10px; border-radius: 999px; font-size: 0.62rem; font-weight: 800;
            text-transform: uppercase; letter-spacing: 0.8px; background: rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.55);
        }
        .pill-active { background: rgba(16,185,129,0.12); color: #10b981; }

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
    </style>

    <div class="exercise-wrapper container-fluid">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <div class="page-header">
                    <h4>Exercise Library</h4>
                    <p>Upload exercise images and keep training metadata in sync</p>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('admin.exercise-library.create') }}" class="launch-btn">
                    <iconify-icon icon="tabler:plus" class="fs-18"></iconify-icon> NEW EXERCISE
                </a>
            </div>
        </div>

        <div class="row g-4">
            @forelse($exercises as $exercise)
            <div class="col-md-4 col-lg-3">
                <div class="tactical-card">
                    <div class="exercise-preview">
                        @if($exercise->image_url)
                            <img src="{{ $exercise->image_url }}" alt="{{ $exercise->exercise_name }}">
                        @else
                            <div class="fallback">
                                <iconify-icon icon="tabler:dumbbell"></iconify-icon>
                            </div>
                        @endif
                    </div>

                    <div class="exercise-body">
                        <div class="d-flex justify-content-between gap-2 align-items-start">
                            <div>
                                <div class="exercise-title">{{ $exercise->exercise_name }}</div>
                                <div class="exercise-meta">
                                    {{ $exercise->target_muscle_group ?? 'General' }}
                                    @if($exercise->exercise_category)
                                        <span class="mx-1">&middot;</span>{{ $exercise->exercise_category }}
                                    @endif
                                </div>
                            </div>
                            <span class="pill {{ $exercise->is_active ? 'pill-active' : '' }}">
                                {{ $exercise->is_active ? 'Active' : 'Paused' }}
                            </span>
                        </div>

                        <div class="exercise-summary">
                            {{ \Illuminate\Support\Str::limit($exercise->instructions ?? $exercise->tips ?? 'No supporting notes added yet.', 90) }}
                        </div>

                        <div class="pill-row">
                            @if($exercise->equipment_type)
                                <span class="pill">{{ $exercise->equipment_type }}</span>
                            @endif
                            @if($exercise->difficulty_level)
                                <span class="pill">{{ $exercise->difficulty_level }}</span>
                            @endif
                            @if($exercise->sets)
                                <span class="pill">{{ $exercise->sets }} sets</span>
                            @endif
                            @if($exercise->reps)
                                <span class="pill">{{ $exercise->reps }} reps</span>
                            @endif
                        </div>
                    </div>

                    <div class="action-row">
                        <a href="{{ route('admin.exercise-library.edit', $exercise->id) }}" class="btn-stealth">Edit</a>
                        <form action="{{ route('admin.exercise-library.destroy', $exercise->id) }}" method="POST" class="flex-grow-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-stealth btn-delete w-100" onclick="return confirm('Delete this exercise asset?')">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="tactical-card p-5 text-center text-white-50">
                    No exercise assets yet. Start by adding the first exercise image and metadata set.
                </div>
            </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
