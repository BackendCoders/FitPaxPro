<x-app-layout title="Media Portfolio Management | FitPaxPro">
    @push('styles')
    <style>
        .portfolio-card { background: #16191d; border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; padding: 30px; }
        .current-cover { width: 100%; height: 300px; border-radius: 15px; object-fit: cover; border: 4px solid #1d2126; margin-bottom: 20px; }
        
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; }
        .gallery-item { position: relative; border-radius: 12px; overflow: hidden; height: 150px; border: 1px solid rgba(255,255,255,0.05); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
        .gallery-item .delete-overlay { 
            position: absolute; top:0; left:0; width:100%; height:100%; 
            background: rgba(225,18,24,0.7); display: flex; align-items: center; justify-content: center;
            opacity: 0; transition: 0.3s; cursor: pointer;
        }
        .gallery-item:hover .delete-overlay { opacity: 1; }
        .youtube-list { display: grid; gap: 10px; }
        .youtube-item {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.05);
            border-radius: 12px;
            padding: 12px 14px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .media-dropzone {
            border: 2px dashed rgba(255,255,255,0.1); border-radius: 15px; padding: 40px;
            text-align: center; cursor: pointer; transition: 0.3s;
        }
        .media-dropzone:hover { border-color: #E11218; background: rgba(225,18,24,0.02); }
    </style>
    @endpush

    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-1 text-white fw-bold">Media Portfolio</h4>
            <p class="text-white-50 fs-14 mb-0">Managing visual assets for <strong>{{ $gym->name }}</strong></p>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('gym.index') }}" class="btn btn-dark px-4 py-2 border-0">
                <iconify-icon icon="tabler:arrow-left" class="me-1 align-middle"></iconify-icon> Back to Directory
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="portfolio-card">
                <h6 class="text-white fw-bold mb-3 uppercase fs-12 letter-spacing-1">Primary Display Asset</h6>
                <img src="{{ $gym->image ? asset('storage/' . $gym->image) : 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?q=80&w=1200' }}" class="current-cover shadow-lg">
                
                <form action="{{ route('gym.media.update', $gym->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <label class="form-label text-white-50 fs-11 fw-bold">UPDATE COVER IMAGE</label>
                    <input type="file" name="image" class="form-control bg-dark border-0 text-white mb-3" accept="image/*" onchange="this.form.submit()">
                    <p class="fs-11 text-muted"><iconify-icon icon="tabler:info-circle" class="me-1"></iconify-icon> Selecting a file will immediately update the main display.</p>
                </form>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="portfolio-card">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-white fw-bold mb-0 uppercase fs-12 letter-spacing-1">Location Gallery ({{ $gym->galleryMedia->count() }} Assets)</h6>
                    <button class="btn btn-primary btn-sm py-1 px-3" onclick="document.getElementById('plus-gallery').click()">+ ADD MEDIA</button>
                </div>

                <div class="gallery-grid mb-4">
                    @foreach($gym->galleryMedia as $media)
                    <div class="gallery-item shadow-sm">
                        <img src="{{ asset('storage/' . $media->file_path) }}">
                        <div class="delete-overlay" onclick="if(confirm('Are you sure you want to delete this image?')) document.getElementById('delete-media-{{ $media->id }}').submit()">
                            <iconify-icon icon="tabler:trash" class="text-white fs-24"></iconify-icon>
                        </div>
                        <form action="{{ route('gym.media.destroy', $media->id) }}" method="POST" id="delete-media-{{ $media->id }}" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                    @endforeach
                </div>

                <form action="{{ route('gym.media.update', $gym->id) }}" method="POST" enctype="multipart/form-data" id="gallery-form">
                    @csrf
                    <input type="file" name="gallery[]" id="plus-gallery" class="d-none" accept="image/*" multiple onchange="this.form.submit()">
                    <div class="media-dropzone" onclick="document.getElementById('plus-gallery').click()">
                        <iconify-icon icon="tabler:photo-plus" class="text-white-50 display-6 mb-2"></iconify-icon>
                        <p class="text-white-50 mb-0 fs-13">Drag files here or click to expand collection</p>
                    </div>
                </form>
            </div>

            <div class="portfolio-card mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="text-white fw-bold mb-0 uppercase fs-12 letter-spacing-1">YouTube Videos ({{ $gym->videoMedia->count() }} Links)</h6>
                </div>

                <div class="youtube-list mb-4">
                    @forelse($gym->videoMedia as $video)
                    <a href="{{ $video->file_path }}" target="_blank" rel="noopener noreferrer" class="youtube-item">
                        <iconify-icon icon="tabler:brand-youtube" class="text-danger fs-20"></iconify-icon>
                        <span>{{ $video->file_name ?? $video->file_path }}</span>
                    </a>
                    @empty
                    <p class="text-white-50 mb-0">No YouTube links added yet.</p>
                    @endforelse
                </div>

                <form action="{{ route('gym.media.update', $gym->id) }}" method="POST">
                    @csrf
                    @php
                        $youtubeLinks = old('youtube_links', collect($gym->youtube_video_links ?? [])->pluck('url')->all());
                        if (empty($youtubeLinks)) {
                            $youtubeLinks = [''];
                        }
                    @endphp
                    <div id="media-youtube-links-container">
                        @foreach($youtubeLinks as $link)
                        <div class="row g-2 align-items-center mb-2">
                            <div class="col-11">
                                <input type="url" name="youtube_links[]" class="form-control bg-dark border-0 text-white" placeholder="https://www.youtube.com/watch?v=..." value="{{ $link }}">
                            </div>
                            <div class="col-1 text-end">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-video-link">&times;</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm" id="add-media-youtube-link">+ Add Link</button>
                        <button type="submit" class="btn btn-primary btn-sm">Save YouTube Links</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        const youtubeLinksContainer = document.getElementById('media-youtube-links-container');
        const addYoutubeButton = document.getElementById('add-media-youtube-link');

        addYoutubeButton?.addEventListener('click', () => {
            const row = document.createElement('div');
            row.className = 'row g-2 align-items-center mb-2';
            row.innerHTML = `
                <div class="col-11">
                    <input type="url" name="youtube_links[]" class="form-control bg-dark border-0 text-white" placeholder="https://www.youtube.com/watch?v=...">
                </div>
                <div class="col-1 text-end">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-video-link">&times;</button>
                </div>
            `;
            youtubeLinksContainer.appendChild(row);
            row.querySelector('.remove-video-link').addEventListener('click', () => row.remove());
        });

        youtubeLinksContainer?.querySelectorAll('.remove-video-link').forEach(btn => {
            btn.addEventListener('click', () => btn.closest('.row').remove());
        });
    </script>
    @endpush
</x-app-layout>
