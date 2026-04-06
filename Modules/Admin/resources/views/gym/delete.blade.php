@extends('admin::components.layouts.master')

@section('title', 'Delete Gym | FitPaxPro')

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">{{ $page['eyebrow'] }}</span>
            <h1 class="hero-title">{{ $page['title'] }}</h1>
            <p style="max-width: 700px; color: rgba(248, 250, 252, 0.84);">{{ $page['description'] }}</p>
        </article>

        <div class="side-stack">
            @foreach ($page['stats'] as $stat)
                <article class="surface-card">
                    <div class="muted">{{ $stat['label'] }}</div>
                    <div class="metric-value">{{ $stat['value'] }}</div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="panel-card">
        <div class="panel-header">
            <div>
                <h2 class="section-title">Delete Workflow</h2>
                <div class="muted">A separate page component for higher-risk gym removal decisions.</div>
            </div>
            <span class="mini-chip">Route: /admin/gym/delete</span>
        </div>

        <div class="record-grid">
            @foreach ($page['highlights'] as $highlight)
                <article class="record-card">
                    <h3 style="margin-top: 0;">Deletion safeguard</h3>
                    <div>{{ $highlight }}</div>
                </article>
            @endforeach
        </div>
    </section>
    </div>
</div>
@endsection

