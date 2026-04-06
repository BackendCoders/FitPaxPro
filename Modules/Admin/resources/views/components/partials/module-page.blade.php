<section class="page-hero">
    <article class="surface-card hero-card">
        <span class="hero-kicker">Existing Module</span>
        <h1 class="hero-title">{{ $modulePage['title'] }}</h1>
        <p style="max-width: 700px; color: rgba(248, 250, 252, 0.84);">{{ $modulePage['description'] }}</p>

        <div class="badge-row" style="margin-top: 20px;">
            @foreach ($modulePage['badges'] as $label => $value)
                <span class="ghost-button">{{ $label }}: {{ $value }}</span>
            @endforeach
        </div>
    </article>

    <div class="side-stack">
        <article class="surface-card">
            <div class="section-title">Module Metrics</div>
            <div class="insight-list">
                @foreach ($modulePage['metrics'] as $label => $value)
                    <div class="chat-item">
                        <strong>{{ $value }}</strong>
                        <div class="muted">{{ $label }}</div>
                    </div>
                @endforeach
            </div>
        </article>
    </div>
</section>

<section class="panel-card">
    <div class="panel-header">
        <div>
            <h2 class="section-title">Dedicated Route Records</h2>
            <div class="muted">Each gym operation module now has its own page and route-backed view.</div>
        </div>
        <span class="mini-chip">{{ count($modulePage['records']) }} latest records</span>
    </div>

    <div class="record-grid">
        @forelse ($modulePage['records'] as $record)
            <article class="record-card">
                <h3 style="margin: 0 0 10px;">{{ $record['title'] }}</h3>
                <div class="muted" style="margin-bottom: 14px;">{{ $record['context'] }}</div>
                <div class="record-chip-row">
                    @foreach ($record['chips'] as $chip)
                        <div class="record-chip">
                            <strong>{{ $chip['label'] }}:</strong>
                            <span>{{ $chip['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            </article>
        @empty
            <article class="record-card">
                <h3 style="margin-top: 0;">No records available yet</h3>
                <div class="muted">The route is ready and will populate automatically when this module has database records.</div>
            </article>
        @endforelse
    </div>
</section>
