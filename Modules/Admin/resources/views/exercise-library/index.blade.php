<x-app-layout title="Exercise Library | Admin Command Center">
    <style>
        .exercise-wrapper {
            padding: 20px 0 34px;
        }

        .hero-shell {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.06);
            background:
                radial-gradient(circle at top right, rgba(225, 18, 24, 0.14), transparent 32%),
                radial-gradient(circle at bottom left, rgba(255, 255, 255, 0.04), transparent 30%),
                linear-gradient(135deg, #111317 0%, #0d0f12 100%);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.28);
            padding: 28px;
            margin-bottom: 22px;
        }

        .hero-shell::after {
            content: "";
            position: absolute;
            inset: 0;
            background-image: linear-gradient(rgba(255,255,255,0.015) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,0.015) 1px, transparent 1px);
            background-size: 28px 28px;
            pointer-events: none;
            opacity: 0.35;
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: rgba(255,255,255,0.55);
            text-transform: uppercase;
            letter-spacing: 1.6px;
            font-size: 0.68rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .hero-title {
            color: #fff;
            font-weight: 900;
            letter-spacing: -1.8px;
            line-height: 0.95;
            font-size: clamp(2rem, 3vw, 3.4rem);
            margin-bottom: 12px;
        }

        .hero-copy {
            max-width: 700px;
            color: rgba(255,255,255,0.58);
            font-size: 0.95rem;
            line-height: 1.7;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 20px;
        }

        .launch-btn {
            background: linear-gradient(to right, #E11218, #9c0c11);
            color: #fff;
            border: none;
            padding: 12px 22px;
            border-radius: 14px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-size: 0.75rem;
            box-shadow: 0 12px 28px rgba(225, 18, 24, 0.25);
            transition: 0.25s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .launch-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 16px 36px rgba(225, 18, 24, 0.34);
            color: #fff;
        }

        .metric-card {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 20px;
            padding: 18px;
            backdrop-filter: blur(14px);
            min-height: 110px;
        }

        .metric-label {
            color: rgba(255,255,255,0.42);
            text-transform: uppercase;
            letter-spacing: 1.2px;
            font-size: 0.62rem;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .metric-value {
            color: #fff;
            font-size: 2rem;
            line-height: 1;
            font-weight: 900;
            letter-spacing: -1.2px;
        }

        .metric-subtext {
            color: rgba(255,255,255,0.5);
            font-size: 0.8rem;
            margin-top: 8px;
        }

        .import-card {
            background: linear-gradient(180deg, rgba(18,20,24,0.98), rgba(12,14,17,0.98));
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 26px;
            padding: 24px;
            margin-bottom: 24px;
            box-shadow: 0 18px 50px rgba(0,0,0,0.22);
        }

        .panel-title {
            color: #fff;
            font-size: 1.05rem;
            font-weight: 900;
            letter-spacing: -0.5px;
            margin-bottom: 6px;
        }

        .panel-copy {
            color: rgba(255,255,255,0.52);
            font-size: 0.84rem;
            line-height: 1.6;
        }

        .dropzone {
            border: 1px dashed rgba(255,255,255,0.14);
            background: rgba(255,255,255,0.02);
            border-radius: 22px;
            padding: 20px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 14px;
        }

        .dropzone.is-ready {
            border-color: rgba(16,185,129,0.35);
            background: rgba(16,185,129,0.05);
        }

        .dropzone.is-empty {
            border-color: rgba(255,255,255,0.1);
        }

        .upload-input {
            background: #08090b !important;
            border: 1px solid rgba(255,255,255,0.06) !important;
            color: #fff !important;
            border-radius: 14px !important;
            font-size: 0.9rem;
            padding: 12px 16px !important;
        }

        .import-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }

        .import-pill {
            padding: 5px 10px;
            border-radius: 999px;
            background: rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.6);
            font-size: 0.65rem;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .import-pill strong {
            color: #fff;
        }

        .file-list {
            margin: 0;
            padding: 0;
            list-style: none;
            display: grid;
            gap: 8px;
            max-height: 220px;
            overflow: auto;
        }

        .file-list li {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            align-items: center;
            padding: 10px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.72);
            font-size: 0.8rem;
        }

        .file-list code {
            color: rgba(255,255,255,0.42);
            font-size: 0.68rem;
            background: transparent;
        }

        .progress {
            height: 12px;
            background: rgba(255,255,255,0.06);
            border-radius: 999px;
        }

        .progress-bar {
            background: linear-gradient(90deg, #E11218, #ff5b61);
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        .import-status {
            color: rgba(255,255,255,0.55);
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .section-heading {
            color: #fff;
            font-weight: 900;
            letter-spacing: -0.8px;
            margin-bottom: 4px;
        }

        .section-subheading {
            color: rgba(255,255,255,0.45);
            font-size: 0.8rem;
            margin-bottom: 18px;
        }

        .exercise-card {
            background: #121418;
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 24px;
            overflow: hidden;
            transition: 0.28s ease;
            height: 100%;
        }

        .exercise-card:hover {
            transform: translateY(-4px);
            border-color: rgba(225,18,24,0.22);
            box-shadow: 0 20px 40px rgba(0,0,0,0.18);
        }

        .exercise-preview {
            height: 190px;
            background: linear-gradient(135deg, #08090b, #15181d);
            overflow: hidden;
            position: relative;
        }

        .exercise-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .exercise-preview .fallback {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255,255,255,0.18);
            font-size: 2rem;
        }

        .exercise-body {
            padding: 18px 20px 20px;
        }

        .exercise-title {
            color: #fff;
            font-weight: 800;
            font-size: 1rem;
            margin-bottom: 8px;
        }

        .exercise-meta {
            color: rgba(255,255,255,0.35);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.8px;
        }

        .exercise-summary {
            color: rgba(255,255,255,0.65);
            font-size: 0.82rem;
            margin-top: 12px;
            min-height: 42px;
        }

        .pill-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .pill {
            padding: 5px 10px;
            border-radius: 999px;
            font-size: 0.62rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            background: rgba(255,255,255,0.04);
            color: rgba(255,255,255,0.55);
        }

        .pill-active {
            background: rgba(16,185,129,0.12);
            color: #10b981;
        }

        .action-row {
            display: flex;
            gap: 10px;
            padding: 15px 20px;
            background: rgba(255,255,255,0.02);
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        .btn-stealth {
            flex: 1;
            padding: 8px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255,255,255,0.03);
            color: rgba(255,255,255,0.5);
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: 0.2s;
            text-align: center;
            text-decoration: none;
        }

        .btn-stealth:hover {
            background: #fff;
            color: #000;
        }

        .btn-delete:hover {
            background: #E11218;
            color: #fff;
            border-color: #E11218;
        }

        .pagination-shell {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 18px;
            padding: 14px 16px;
            margin-top: 10px;
        }

        .pagination-shell .pagination {
            margin-bottom: 0;
            justify-content: center;
            flex-wrap: wrap;
            gap: 6px;
        }

        .pagination-shell .page-link {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            color: rgba(255,255,255,0.75);
            border-radius: 10px !important;
            padding: 8px 12px;
        }

        .pagination-shell .page-link:hover {
            background: rgba(225,18,24,0.14);
            border-color: rgba(225,18,24,0.25);
            color: #fff;
        }

        .pagination-shell .page-item.active .page-link {
            background: linear-gradient(90deg, #E11218, #ff5b61);
            border-color: #E11218;
            color: #fff;
        }

        .pagination-shell .page-item.disabled .page-link {
            background: rgba(255,255,255,0.02);
            color: rgba(255,255,255,0.28);
            border-color: rgba(255,255,255,0.03);
        }
    </style>

    <div class="exercise-wrapper container-fluid">
        <div class="hero-shell mb-4">
            <div class="row align-items-end g-4 hero-content">
                <div class="col-lg-7">
                    <div class="eyebrow">
                        <iconify-icon icon="tabler:bolt"></iconify-icon>
                        Exercise Content Studio
                    </div>
                    <h4 class="hero-title">Build and import the full exercise library.</h4>
                    <p class="hero-copy mb-0">
                        Upload single exercises, or drop an entire folder of JSON, CSV, and image assets.
                        Files are sorted internally by path, and matching images are linked to the correct records automatically.
                    </p>
                    <div class="hero-actions">
                        <a href="{{ route('admin.exercise-library.create') }}" class="launch-btn">
                            <iconify-icon icon="tabler:plus" class="fs-18"></iconify-icon> New Exercise
                        </a>
                        <a href="#folder-import" class="launch-btn" style="background: rgba(255,255,255,0.06); box-shadow: none;">
                            <iconify-icon icon="tabler:folder-up" class="fs-18"></iconify-icon> Folder Import
                        </a>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="row g-3">
                        <div class="col-4">
                            <div class="metric-card">
                                <div class="metric-label">Exercises</div>
                                <div class="metric-value">{{ $totalExercises }}</div>
                                <div class="metric-subtext">In library</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-card">
                                <div class="metric-label">Active</div>
                                <div class="metric-value">{{ $activeExercises }}</div>
                                <div class="metric-subtext">Published</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="metric-card">
                                <div class="metric-label">Images</div>
                                <div class="metric-value">{{ $withImages }}</div>
                                <div class="metric-subtext">Linked</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="folder-import" class="import-card">
            <div class="row g-4 align-items-stretch">
                <div class="col-lg-6">
                    <div class="panel-title">Bulk Folder Import</div>
                    <div class="panel-copy">
                        Select a folder and the admin will sort the files internally before import.
                        JSON and CSV files create exercise rows, while image files in the same folder are matched by name or slug.
                        CSV files with `imageName`, `base64encoded`, `imageWidth`, `imageHeight`, `poseLandmarks`, and `excercise` are also supported.
                    </div>

                    <div id="importDropzone" class="dropzone is-empty mt-4">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0" style="width: 46px; height: 46px; border-radius: 14px; background: rgba(225,18,24,0.14); display:flex; align-items:center; justify-content:center; color:#fff;">
                                <iconify-icon icon="tabler:folder-upload" class="fs-24"></iconify-icon>
                            </div>
                            <div>
                                <div class="panel-title mb-1">Choose a folder or mixed asset set</div>
                                <div class="panel-copy mb-0">Supports your exercise data folders from `D:\machine learning\fitpaxproai\data`, inline base64 CSV exports, and similar datasets.</div>
                            </div>
                        </div>

                        <input type="file" id="exerciseImportFiles" class="form-control upload-input" accept=".json,.csv,.txt,.jpg,.jpeg,.png,.webp,.gif,.bmp,.svg" multiple webkitdirectory directory>

                        <div class="import-meta">
                            <span class="import-pill">Files: <strong id="selectedFileCount">0</strong></span>
                            <span class="import-pill">Data: <strong id="selectedDataCount">0</strong></span>
                            <span class="import-pill">Images: <strong id="selectedImageCount">0</strong></span>
                            <span class="import-pill">Mode: <strong>Sorted</strong></span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" id="exerciseImportBtn" class="launch-btn">
                                <iconify-icon icon="tabler:cloud-upload" class="fs-18"></iconify-icon> Import Folder
                            </button>
                            <button type="button" id="clearSelectionBtn" class="btn-stealth" style="flex:0 0 auto; min-width: 150px;">
                                Clear Selection
                            </button>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="panel-title">Selected Files</div>
                    <div class="panel-copy">The first files in sorted order are shown below so you can verify the folder before import.</div>

                    <div class="progress mt-4 mb-3">
                        <div id="exerciseImportProgress" class="progress-bar" role="progressbar" style="width: 0%;">0%</div>
                    </div>

                    <div id="exerciseImportText" class="import-status mb-3">Waiting for folder selection.</div>

                    <ul id="selectedFilesList" class="file-list">
                        <li>
                            <span>No files selected yet.</span>
                            <code>Pick a folder to preview its contents</code>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row align-items-center mb-3">
            <div class="col-md-6">
                <div class="section-heading">Exercise Cards</div>
                <div class="section-subheading">Latest assets in the library, sorted by order and name.</div>
            </div>
        </div>

        <div class="row g-4">
            @forelse($exercises as $exercise)
            <div class="col-md-4 col-lg-3">
                <div class="exercise-card">
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
                            {{ \Illuminate\Support\Str::limit($exercise->instructions ?? $exercise->tips ?? 'No supporting notes added yet.', 92) }}
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
                <div class="exercise-card p-5 text-center text-white-50">
                    No exercise assets yet. Start by importing a folder or adding the first exercise manually.
                </div>
            </div>
            @endforelse
        </div>

        @if($exercises->hasPages())
            <div class="pagination-shell">
                {{ $exercises->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        (function () {
            const fileInput = document.getElementById('exerciseImportFiles');
            const importButton = document.getElementById('exerciseImportBtn');
            const clearButton = document.getElementById('clearSelectionBtn');
            const progress = document.getElementById('exerciseImportProgress');
            const status = document.getElementById('exerciseImportText');
            const fileList = document.getElementById('selectedFilesList');
            const dropzone = document.getElementById('importDropzone');
            const fileCount = document.getElementById('selectedFileCount');
            const dataCount = document.getElementById('selectedDataCount');
            const imageCount = document.getElementById('selectedImageCount');

            if (!fileInput || !importButton || !clearButton || !progress || !status || !fileList || !dropzone) {
                return;
            }

            const dataExt = ['json', 'csv', 'txt'];
            const imageExt = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'];

            function escapeHtml(str) {
                return String(str)
                    .replaceAll('&', '&amp;')
                    .replaceAll('<', '&lt;')
                    .replaceAll('>', '&gt;')
                    .replaceAll('"', '&quot;')
                    .replaceAll("'", '&#39;');
            }

            function getSortedFiles() {
                return Array.from(fileInput.files || []).sort((a, b) => {
                    const left = (a.webkitRelativePath || a.name || '').toLowerCase();
                    const right = (b.webkitRelativePath || b.name || '').toLowerCase();
                    return left.localeCompare(right);
                });
            }

            function renderSelection() {
                const files = getSortedFiles();
                const dataFiles = files.filter(file => dataExt.includes((file.name.split('.').pop() || '').toLowerCase()));
                const images = files.filter(file => imageExt.includes((file.name.split('.').pop() || '').toLowerCase()));

                fileCount.textContent = files.length;
                dataCount.textContent = dataFiles.length;
                imageCount.textContent = images.length;

                if (!files.length) {
                    dropzone.classList.add('is-empty');
                    dropzone.classList.remove('is-ready');
                    fileList.innerHTML = '<li><span>No files selected yet.</span><code>Pick a folder to preview its contents</code></li>';
                    status.textContent = 'Waiting for folder selection.';
                    return;
                }

                dropzone.classList.remove('is-empty');
                dropzone.classList.add('is-ready');
                status.textContent = `Ready to import ${files.length} files from the selected folder.`;

                fileList.innerHTML = files.slice(0, 8).map(file => {
                    const path = file.webkitRelativePath || file.name;
                    const kind = (file.name.split('.').pop() || '').toUpperCase();
                    return `<li><span>${escapeHtml(path)}</span><code>${kind}</code></li>`;
                }).join('');

                if (files.length > 8) {
                    fileList.insertAdjacentHTML('beforeend', `<li><span>And ${files.length - 8} more files</span><code>Sorted internally</code></li>`);
                }
            }

            function setProgress(value, label) {
                const pct = Math.max(0, Math.min(100, value));
                progress.style.width = pct + '%';
                progress.textContent = pct + '%';
                status.textContent = label || `${pct}% uploaded`;
            }

            fileInput.addEventListener('change', renderSelection);

            clearButton.addEventListener('click', function () {
                fileInput.value = '';
                fileInput.disabled = false;
                importButton.disabled = false;
                setProgress(0, 'Selection cleared.');
                renderSelection();
            });

            importButton.addEventListener('click', function () {
                const files = getSortedFiles();

                if (!files.length) {
                    status.textContent = 'Select a folder first.';
                    return;
                }

                importButton.disabled = true;
                fileInput.disabled = true;

                const formData = new FormData();
                files.forEach(file => {
                    formData.append('import_files[]', file, file.webkitRelativePath || file.name);
                    formData.append('import_paths[]', file.webkitRelativePath || file.name);
                });
                formData.append('_token', '{{ csrf_token() }}');

                const xhr = new XMLHttpRequest();
                xhr.open('POST', '{{ route('admin.exercise-library.import') }}', true);
                xhr.responseType = 'json';

                xhr.upload.onprogress = function (event) {
                    if (event.lengthComputable) {
                        setProgress(Math.round((event.loaded / event.total) * 100), `Uploading ${files.length} sorted files...`);
                    }
                };

                xhr.onloadstart = function () {
                    setProgress(0, `Starting folder upload for ${files.length} files...`);
                };

                xhr.onload = function () {
                    const payload = xhr.response || {};

                    if (xhr.status >= 200 && xhr.status < 300 && payload.success) {
                        setProgress(100, `Imported ${payload.created || 0} records. ${payload.skipped || 0} skipped. Processing complete.`);
                        if (payload.errors && payload.errors.length) {
                            console.warn('Exercise import warnings:', payload.errors);
                        }
                        setTimeout(function () {
                            window.location.reload();
                        }, 700);
                    } else {
                        setProgress(0, payload.message || `Import failed with HTTP ${xhr.status}.`);
                        if (payload.errors && payload.errors.length) {
                            status.innerHTML = `${escapeHtml(payload.message || 'Import failed.')}<br><span style="text-transform:none;letter-spacing:0;display:block;margin-top:8px;color:rgba(255,255,255,0.35);">${escapeHtml(payload.errors[0])}</span>`;
                        }
                        importButton.disabled = false;
                        fileInput.disabled = false;
                    }
                };

                xhr.onerror = function () {
                    setProgress(0, 'Upload failed. Please try again.');
                    importButton.disabled = false;
                    fileInput.disabled = false;
                };

                xhr.onabort = function () {
                    setProgress(0, 'Upload cancelled.');
                    importButton.disabled = false;
                    fileInput.disabled = false;
                };

                setProgress(0, `Uploading ${files.length} sorted files...`);
                xhr.send(formData);
            });

            renderSelection();
        })();
    </script>
    @endpush
</x-app-layout>
