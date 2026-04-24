<x-app-layout title="Category Configuration | Admin Command Center">
    <style>
        .form-wrapper { padding: 40px 0; max-width: 700px; margin: 0 auto; }
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

        /* Icon Dropdown Styles */
        #iconDropdown {
            position: absolute; top: 100%; left: 0; right: 0; z-index: 1000;
            background: #1a1d21; border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px; box-shadow: 0 15px 40px rgba(0,0,0,0.5);
            max-height: 300px; overflow-y: auto; padding: 15px; margin-top: 10px;
        }
        .icon-select-btn { border-color: rgba(255,255,255,0.05) !important; background: rgba(255,255,255,0.02) !important; color: #fff !important; }
        .icon-select-btn i { color: #fff !important; }
        .icon-select-btn:hover { background: var(--rich-red) !important; border-color: var(--rich-red) !important; }
    </style>

    <div class="form-wrapper">
        <a href="{{ route('admin.categories.index') }}" class="back-link">
            <iconify-icon icon="tabler:arrow-left"></iconify-icon> RETURN TO CATALOG
        </a>

        <div class="page-header mb-5">
            <h4>Domain Classification</h4>
            <p>Configure Category nodes and identity</p>
        </div>

        <form action="{{ isset($category) ? route('admin.categories.update', $category->id) : route('admin.categories.store') }}" method="POST">
            @csrf
            @if(isset($category)) @method('PUT') @endif

            <div class="tactical-card">
                <div class="row g-4">
                    <div class="col-12">
                        <label class="field-label">Category Designation (Name)</label>
                        <input type="text" name="name" id="name" class="form-control stealth-input" value="{{ old('name', $category->name ?? '') }}" placeholder="e.g. Crossfit Training" required>
                    </div>

                    <div class="col-12 d-none">
                        <label class="field-label">System Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control stealth-input" value="{{ old('slug', $category->slug ?? '') }}" readonly>
                    </div>

                    <div class="col-12 position-relative">
                        <label class="field-label">Icon Identifier (Material Design Icons)</label>
                        <div class="input-group">
                            <span class="input-group-text bg-black border-secondary border-opacity-10 text-white">
                                <i id="iconPreview" class="{{ isset($category) ? ($category->icon_class ?? 'mdi mdi-circle-outline') : 'mdi mdi-circle-outline' }} fs-18"></i>
                            </span>
                            <input type="text" name="icon_class" id="icon_class" class="form-control stealth-input" 
                                value="{{ old('icon_class', $category->icon_class ?? '') }}" 
                                placeholder="Start typing to search icons (e.g. barbell, run)..." autocomplete="off">
                        </div>

                        <div id="iconDropdown" style="display: none;">
                            <div id="iconList" class="d-flex flex-wrap gap-2"></div>
                        </div>
                        <div class="mt-2 text-white-30 fs-10 uppercase letter-spacing-1">Search via MDI Library Nodes</div>
                    </div>
                </div>
            </div>

            <button type="submit" class="sync-btn">
                {{ isset($category) ? 'SYNC CLASSIFICATION' : 'CONFIRM CLASSIFICATION' }}
                <iconify-icon icon="tabler:refresh" class="fs-18"></iconify-icon>
            </button>
        </form>
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            let allIcons = [];
            const iconInput = $('#icon_class');
            const iconDropdown = $('#iconDropdown');
            const iconList = $('#iconList');
            const iconPreview = $('#iconPreview');

            // 1. Fetch Local JSON matching your structure
            fetch("{{ asset('assets/icons.json') }}")
                .then(response => response.json())
                .then(data => {
                    // Accessing data.i based on your provided JSON structure
                    allIcons = data.i; 
                    console.log('MDI Data parsed successfully');
                })
                .catch(err => console.error('Error loading icons.json:', err));

            // 2. Search Logic targeting the "n" property
            iconInput.on('input', function() {
                const query = $(this).val().toLowerCase().replace('mdi-', '').trim();
                iconList.empty();

                if (query.length < 2) {
                    iconDropdown.hide();
                    return;
                }

                // Filter based on "n" (name) property
                const filtered = allIcons.filter(icon => icon.n.includes(query)).slice(0, 48);

                if (filtered.length > 0) {
                    filtered.forEach(icon => {
                        const fullClass = `mdi mdi-${icon.n}`;
                        iconList.append(`
                            <button type="button" class="btn btn-outline-light border p-0 d-flex align-items-center justify-content-center icon-select-btn" 
                                    data-class="${fullClass}" title="${icon.n}" style="width: 42px; height: 42px;">
                                <i class="${fullClass} fs-20"></i>
                            </button>
                        `);
                    });
                    iconDropdown.show();
                } else {
                    iconList.append('<div class="p-3 w-100 text-center text-muted">No matching icons.</div>');
                    iconDropdown.show();
                }
            });

            // 3. Selection Logic
            $(document).on('click', '.icon-select-btn', function() {
                const selectedClass = $(this).data('class');
                iconInput.val(selectedClass);
                iconPreview.attr('class', selectedClass + ' fs-18 text-white');
                iconDropdown.hide();
            });

            // 4. Close dropdown
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.position-relative').length) {
                    iconDropdown.hide();
                }
            });

            // Slug Helper
            $('#name').on('keyup', function() {
                let name = $(this).val();
                let slug = name.toLowerCase().replace(/[^a-z0-9 -]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
                $('#slug').val(slug);
            });
        });
    </script>
    @endpush
</x-app-layout>
