<div class="surface-card">
    @if ($errors->any())
        <div class="alert alert-danger" role="alert" style="margin-bottom:16px;">
            <strong>Please fix the following errors:</strong>
            <ul style="margin:8px 0 0 18px; padding:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $formAction }}" class="gym-form-grid" enctype="multipart/form-data">
        @csrf
        @if ($httpMethod !== 'POST')
            @method($httpMethod)
        @endif

        <div class="form-field full-width">
            <label for="owner_id">Owner <span class="required">*</span></label>
            <select id="owner_id" name="owner_id" required>
                <option value="">Select owner</option>
                @foreach ($owners as $owner)
                    <option value="{{ $owner->id }}" {{ old('owner_id', $gym->owner_id) == $owner->id ? 'selected' : '' }}>
                        {{ $owner->name }} ({{ $owner->email }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-field">
            <label for="name">Name <span class="required">*</span></label>
            <input id="name" name="name" type="text" value="{{ old('name', $gym->name) }}" required>
        </div>

        <div class="form-field">
            <label for="slug">Slug <span class="required">*</span></label>
            <input id="slug" name="slug" type="text" value="{{ old('slug', $gym->slug) }}" placeholder="example-gym" required>
        </div>

        <div class="form-field full-width">
            <label for="description">Description</label>
            <textarea id="description" name="description" rows="3">{{ old('description', $gym->description) }}</textarea>
        </div>

        <div class="form-field">
            <label for="brand_name">Brand Name</label>
            <input id="brand_name" name="brand_name" type="text" value="{{ old('brand_name', $gym->brand_name) }}">
        </div>

        <div class="form-field full-width">
            <label>Logo Source</label>
            @php
                $logoSource = old('logo_source', 'file_manager');
            @endphp
            <div class="media-source-options">
                <label><input type="radio" name="logo_source" value="desktop" {{ $logoSource === 'desktop' ? 'checked' : '' }}> Desktop Upload</label>
                <label><input type="radio" name="logo_source" value="file_manager" {{ $logoSource === 'file_manager' ? 'checked' : '' }}> File Manager Path</label>
            </div>
        </div>

        <div class="form-field full-width" data-media-field="logo" data-source="desktop">
            <label for="logo_file">Upload Logo (Desktop)</label>
            <input id="logo_file" name="logo_file" type="file" accept=".jpg,.jpeg,.png,.webp,image/*">
        </div>

        <div class="form-field full-width" data-media-field="logo" data-source="file_manager">
            <label for="logo_path">Logo Path (File Manager)</label>
            <input id="logo_path" name="logo_path" type="text" value="{{ old('logo_path', $gym->logo_path) }}" placeholder="uploads/logos/logo.png or storage/gyms/logos/logo.png">
        </div>

        <div class="form-field full-width">
            <label>Intro Video Source</label>
            @php
                $videoSource = old('video_source', 'file_manager');
            @endphp
            <div class="media-source-options">
                <label><input type="radio" name="video_source" value="desktop" {{ $videoSource === 'desktop' ? 'checked' : '' }}> Desktop Upload</label>
                <label><input type="radio" name="video_source" value="file_manager" {{ $videoSource === 'file_manager' ? 'checked' : '' }}> File Manager URL/Path</label>
            </div>
        </div>

        <div class="form-field full-width" data-media-field="video" data-source="desktop">
            <label for="intro_video_file">Upload Intro Video (Desktop)</label>
            <input id="intro_video_file" name="intro_video_file" type="file" accept="video/mp4,video/quicktime,video/x-msvideo,video/x-matroska,video/webm">
        </div>

        <div class="form-field full-width" data-media-field="video" data-source="file_manager">
            <label for="intro_video_url">Intro Video URL/Path (File Manager)</label>
            <input id="intro_video_url" name="intro_video_url" type="text" value="{{ old('intro_video_url', $gym->intro_video_url) }}" placeholder="https://... or storage/gyms/videos/video.mp4">
        </div>

        <div class="form-field full-width">
            <label for="address">Address <span class="required">*</span></label>
            <textarea id="address" name="address" rows="2" required>{{ old('address', $gym->address) }}</textarea>
        </div>

        <div class="form-field">
            <label for="city">City</label>
            <input id="city" name="city" type="text" value="{{ old('city', $gym->city) }}">
        </div>

        <div class="form-field">
            <label for="search_radius_km">Search Radius (KM)</label>
            <input id="search_radius_km" name="search_radius_km" type="number" min="0" value="{{ old('search_radius_km', $gym->search_radius_km) }}">
        </div>

        <div class="form-field">
            <label for="latitude">Latitude</label>
            <input id="latitude" name="latitude" type="number" step="0.00000001" value="{{ old('latitude', $gym->latitude) }}">
        </div>

        <div class="form-field">
            <label for="longitude">Longitude</label>
            <input id="longitude" name="longitude" type="number" step="0.00000001" value="{{ old('longitude', $gym->longitude) }}">
        </div>

        <div class="form-field">
            <label for="member_count_limit">Member Count Limit</label>
            <input id="member_count_limit" name="member_count_limit" type="number" min="0" value="{{ old('member_count_limit', $gym->member_count_limit) }}">
        </div>

        <div class="form-field">
            <label for="rating_avg">Rating Avg</label>
            <input id="rating_avg" name="rating_avg" type="number" min="0" max="5" step="0.01" value="{{ old('rating_avg', $gym->rating_avg) }}">
        </div>

        <div class="form-field">
            <label for="status">Status <span class="required">*</span></label>
            <select id="status" name="status" required>
                @foreach ($statusOptions as $status)
                    <option value="{{ $status }}" {{ old('status', $gym->status) === $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="form-field">
            <label for="is_sponsored">Is Sponsored <span class="required">*</span></label>
            <select id="is_sponsored" name="is_sponsored" required>
                <option value="1" {{ (string) old('is_sponsored', (int) $gym->is_sponsored) === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (string) old('is_sponsored', (int) $gym->is_sponsored) === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="form-field">
            <label for="is_verified">Is Verified <span class="required">*</span></label>
            <select id="is_verified" name="is_verified" required>
                <option value="1" {{ (string) old('is_verified', (int) $gym->is_verified) === '1' ? 'selected' : '' }}>Yes</option>
                <option value="0" {{ (string) old('is_verified', (int) $gym->is_verified) === '0' ? 'selected' : '' }}>No</option>
            </select>
        </div>

        <div class="full-width" style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
            <a class="ghost-button" href="{{ route('admin.gym.index') }}" style="color:#111827; border:1px solid rgba(17,24,39,.2); background:#fff;">Cancel</a>
            <button class="primary-button" type="submit">{{ $httpMethod === 'POST' ? 'Create Gym' : 'Update Gym' }}</button>
        </div>
    </form>
</div>

<script>
    (function () {
        function toggleMediaField(groupName, sourceValue) {
            document.querySelectorAll('[data-media-field="' + groupName + '"]').forEach(function (el) {
                var isActive = el.getAttribute('data-source') === sourceValue;
                el.style.display = isActive ? '' : 'none';
            });
        }

        function initMediaToggle(radioName, groupName) {
            var radios = document.querySelectorAll('input[name="' + radioName + '"]');
            if (!radios.length) {
                return;
            }

            var checked = Array.prototype.find.call(radios, function (radio) {
                return radio.checked;
            });

            toggleMediaField(groupName, checked ? checked.value : 'file_manager');

            radios.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    toggleMediaField(groupName, radio.value);
                });
            });
        }

        initMediaToggle('logo_source', 'logo');
        initMediaToggle('video_source', 'video');
    })();
</script>
