<x-app-layout title="Category Mapping | Admin Command Center">
    <style>
        .category-wrapper { padding: 25px 0; }
        .page-header h4 { font-weight: 900; letter-spacing: -1.2px; color: #fff; text-transform: uppercase; }
        .page-header p { color: rgba(255,255,255,0.3); font-size: 0.75rem; letter-spacing: 0.5px; text-transform: uppercase; }

        .tactical-card {
            background: #121418; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 20px; overflow: hidden;
        }

        .tactical-table { margin-bottom: 0; }
        .tactical-table thead th {
            background: rgba(255,255,255,0.02); border-bottom: 1px solid rgba(255,255,255,0.05);
            color: rgba(255,255,255,0.3); font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1px; padding: 15px 25px;
        }
        .tactical-table tbody td {
            padding: 18px 25px; vertical-align: middle; border-bottom: 1px solid rgba(255,255,255,0.03);
            color: rgba(255,255,255,0.7); font-size: 0.85rem; font-weight: 600;
        }
        
        .icon-node {
            width: 40px; height: 40px; background: #08090b; border: 1px solid rgba(255,255,255,0.05);
            border-radius: 10px; display: flex; align-items: center; justify-content: center;
            color: var(--rich-red); font-size: 1.2rem;
        }

        .status-pill {
            display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px;
            border-radius: 20px; font-size: 0.65rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-online { background: rgba(0, 255, 136, 0.1); color: #00ff88; }
        .status-standby { background: rgba(255, 255, 255, 0.05); color: rgba(255,255,255,0.3); }

        .btn-action {
            width: 32px; height: 32px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.05);
            background: rgba(255,255,255,0.03); color: rgba(255,255,255,0.4);
            display: inline-flex; align-items: center; justify-content: center; transition: 0.2s;
            text-decoration: none;
        }
        .btn-action:hover { background: #fff; color: #000; }
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

    <div class="category-wrapper container-fluid">
        <div class="row align-items-center mb-5">
            <div class="col-md-6">
                <div class="page-header">
                    <h4>Category Mapping</h4>
                    <p>System Taxonomy & Domain Classification</p>
                </div>
            </div>
            <div class="col-md-6 text-md-end">
                <a href="{{ route('admin.categories.create') }}" class="launch-btn">
                    <iconify-icon icon="tabler:plus" class="fs-18"></iconify-icon> NEW CLASSIFICATION
                </a>
            </div>
        </div>

        <div class="tactical-card">
            <div class="table-responsive">
                <table class="table tactical-table">
                    <thead>
                        <tr>
                            <th width="80">Icon</th>
                            <th>Name</th>
                            <th>Identifier (Slug)</th>
                            <th width="150">Status</th>
                            <th width="120" class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>
                                <div class="icon-node">
                                    <iconify-icon icon="{{ $category->icon_class ?? 'tabler:hash' }}"></iconify-icon>
                                </div>
                            </td>
                            <td>
                                <div class="fw-900 text-white">{{ $category->name }}</div>
                            </td>
                            <td>
                                <code class="text-white-30 fs-11">{{ $category->slug }}</code>
                            </td>
                            <td>
                                <span class="status-pill {{ $category->is_active ? 'status-online' : 'status-standby' }}">
                                    <iconify-icon icon="{{ $category->is_active ? 'tabler:circle-check-filled' : 'tabler:circle' }}"></iconify-icon>
                                    {{ $category->is_active ? 'ACTIVE' : 'STANDBY' }}
                                </span>
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn-action" title="Modify">
                                        <iconify-icon icon="tabler:edit"></iconify-icon>
                                    </a>
                                    <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-action btn-delete" title="Decommission" onclick="return confirm('Confirm Deletion?')">
                                            <iconify-icon icon="tabler:trash"></iconify-icon>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
