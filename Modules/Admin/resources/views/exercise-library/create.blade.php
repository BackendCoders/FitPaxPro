<x-app-layout title="New Exercise | Admin Command Center">
    <style>
        .form-wrapper { padding: 40px 0; max-width: 1100px; margin: 0 auto; }
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; text-transform: uppercase; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }
        .tactical-card {
            background: #121418; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 24px; padding: 40px; margin-bottom: 30px;
        }
        .field-label { display: block; font-size: 0.65rem; font-weight: 800; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
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
        .asset-preview {
            width: 100%; min-height: 220px; border-radius: 18px; object-fit: cover;
            background: #08090b; border: 1px solid rgba(255,255,255,0.05);
        }
    </style>

    <div class="form-wrapper">
        <a href="{{ route('admin.exercise-library.index') }}" class="back-link">
            <iconify-icon icon="tabler:arrow-left"></iconify-icon> RETURN TO LIBRARY
        </a>

        <div class="page-header mb-5">
            <h4>Exercise Asset Intake</h4>
            <p>Upload an exercise image and define its training metadata</p>
        </div>

        <form action="{{ isset($exercise) ? route('admin.exercise-library.update', $exercise->id) : route('admin.exercise-library.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @if(isset($exercise)) @method('PUT') @endif

            <div class="tactical-card">
                <div class="row g-4">
                    <div class="col-lg-5">
                        <label class="field-label">Exercise Image</label>
                        <input type="file" name="image" class="form-control stealth-input" accept="image/*" {{ isset($exercise) ? '' : 'required' }}>
                        @if(isset($exercise) && $exercise->image_url)
                            <div class="mt-3">
                                <img src="{{ $exercise->image_url }}" alt="{{ $exercise->exercise_name }}" class="asset-preview">
                            </div>
                        @endif
                    </div>

                    <div class="col-lg-7">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="field-label">Exercise Name</label>
                                <input type="text" name="exercise_name" class="form-control stealth-input" value="{{ old('exercise_name', $exercise->exercise_name ?? '') }}" placeholder="e.g. Barbell Squat" required>
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Display Order</label>
                                <input type="number" name="order_index" class="form-control stealth-input" value="{{ old('order_index', $exercise->order_index ?? 0) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Status</label>
                                <select name="is_active" class="form-control stealth-input">
                                    <option value="1" {{ old('is_active', $exercise->is_active ?? true) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $exercise->is_active ?? true) ? '' : 'selected' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">Muscle Group</label>
                                <input type="text" name="target_muscle_group" class="form-control stealth-input" value="{{ old('target_muscle_group', $exercise->target_muscle_group ?? '') }}" placeholder="legs, chest, back">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Category</label>
                                <input type="text" name="exercise_category" class="form-control stealth-input" value="{{ old('exercise_category', $exercise->exercise_category ?? '') }}" placeholder="strength, cardio">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Difficulty</label>
                                <input type="text" name="difficulty_level" class="form-control stealth-input" value="{{ old('difficulty_level', $exercise->difficulty_level ?? '') }}" placeholder="beginner, advanced">
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">Equipment</label>
                                <input type="text" name="equipment_type" class="form-control stealth-input" value="{{ old('equipment_type', $exercise->equipment_type ?? '') }}" placeholder="barbell, dumbbell">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Sets</label>
                                <input type="number" name="sets" class="form-control stealth-input" value="{{ old('sets', $exercise->sets ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Reps</label>
                                <input type="text" name="reps" class="form-control stealth-input" value="{{ old('reps', $exercise->reps ?? '') }}" placeholder="8-12">
                            </div>

                            <div class="col-md-4">
                                <label class="field-label">Rest Seconds</label>
                                <input type="number" name="rest_period_seconds" class="form-control stealth-input" value="{{ old('rest_period_seconds', $exercise->rest_period_seconds ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Duration (Minutes)</label>
                                <input type="number" name="estimated_duration_minutes" class="form-control stealth-input" value="{{ old('estimated_duration_minutes', $exercise->estimated_duration_minutes ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="field-label">Video URL</label>
                                <input type="text" name="instruction_video_url" class="form-control stealth-input" value="{{ old('instruction_video_url', $exercise->instruction_video_url ?? '') }}" placeholder="https://...">
                            </div>

                            <div class="col-12">
                                <label class="field-label">Instructions</label>
                                <textarea name="instructions" rows="4" class="form-control stealth-input" placeholder="Add coaching cues and step-by-step directions">{{ old('instructions', $exercise->instructions ?? '') }}</textarea>
                            </div>

                            <div class="col-12">
                                <label class="field-label">Tips</label>
                                <textarea name="tips" rows="3" class="form-control stealth-input" placeholder="Add safety notes, common mistakes, or setup tips">{{ old('tips', $exercise->tips ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="sync-btn">
                {{ isset($exercise) ? 'SYNC EXERCISE' : 'SAVE EXERCISE' }}
                <iconify-icon icon="tabler:upload" class="fs-18"></iconify-icon>
            </button>
        </form>
    </div>
</x-app-layout>
