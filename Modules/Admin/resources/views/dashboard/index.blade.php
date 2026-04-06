@extends('admin::components.layouts.master')

@section('title', 'FitPaxPro Dashboard Overview')

@section('content')
    @include('admin::components.partials.navigation')

    <section class="page-hero">
        <article class="surface-card hero-card">
            <span class="hero-kicker">Dashboard Index Page</span>
            <h1 class="hero-title">Dashboard Overview</h1>
            <p style="max-width: 720px; color: rgba(248, 250, 252, 0.84);">
                A dedicated super admin landing page with modular navigation, real platform totals, conversation activity, and a lightweight analytics graph.
            </p>

            <div class="hero-actions" style="margin-top: 22px;">
                <a class="primary-button" href="{{ route('admin.gym.create') }}">Create Gym</a>
                <a class="ghost-button" href="{{ route('admin.gym.index') }}">Open Gym Module</a>
            </div>
        </article>

        <div class="side-stack">
            <article class="surface-card">
                <div class="section-title">Routing Snapshot</div>
                <div class="insight-list">
                    <div class="chat-item">
                        <strong>/admin/dashboard</strong>
                        <div class="muted">Dedicated index page for dashboard overview.</div>
                    </div>
                    <div class="chat-item">
                        <strong>/admin/gym/create</strong>
                        <div class="muted">Standalone create route for gym operations.</div>
                    </div>
                    <div class="chat-item">
                        <strong>/admin/gym/attendance</strong>
                        <div class="muted">Independent route for AttendanceLog records.</div>
                    </div>
                </div>
            </article>
        </div>
    </section>

    <section class="metrics-grid">
        @foreach ($dashboard['summary'] as $card)
            <article class="metric-card {{ $card['accent'] }}">
                <div class="muted">{{ $card['label'] }}</div>
                <div class="metric-value">{{ $card['value'] }}</div>
                <div class="muted">Live total from the existing platform models.</div>
            </article>
        @endforeach
    </section>

    <section class="content-grid">
        <article class="chat-card">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">Chats</h2>
                    <div class="muted">Recent gym conversations and feedback activity collected from enquiries and reviews.</div>
                </div>
                <span class="mini-chip">{{ count($dashboard['chatItems']) }} items</span>
            </div>

            <div class="chat-list">
                @forelse ($dashboard['chatItems'] as $chat)
                    <div class="chat-item">
                        <div style="display: flex; justify-content: space-between; gap: 12px; margin-bottom: 8px;">
                            <strong>{{ $chat['title'] }}</strong>
                            <span class="muted">{{ $chat['meta'] }}</span>
                        </div>
                        <div class="muted" style="margin-bottom: 8px;">{{ $chat['context'] }}</div>
                        <div>{{ $chat['message'] }}</div>
                    </div>
                @empty
                    <div class="chat-item">
                        <strong>No chat activity yet</strong>
                        <div class="muted">This section is ready and will show recent enquiry and review conversations when records exist.</div>
                    </div>
                @endforelse
            </div>
        </article>

        <article class="chat-card">
            <div class="panel-header">
                <div>
                    <h2 class="section-title">Analytics</h2>
                    <div class="muted">Pie-style distribution built from the current dashboard totals.</div>
                </div>
            </div>

            <div class="chart-wrap">
                <div class="chart-ring" style="background: conic-gradient({{ $dashboard['analytics']['chart'] }});">
                    <div class="chart-label">
                        <div>
                            <div style="font-size: 1.8rem;">{{ array_sum(array_column($dashboard['analytics']['legend'], 'value')) }}</div>
                            <div class="muted">tracked items</div>
                        </div>
                    </div>
                </div>

                <div class="insight-list">
                    @foreach ($dashboard['analytics']['legend'] as $item)
                        <div class="legend-item">
                            <span class="legend-dot" style="background: {{ $item['color'] }};"></span>
                            <div>
                                <strong>{{ $item['label'] }}</strong>
                                <div class="muted">{{ $item['value'] }} records | {{ rtrim(rtrim(number_format($item['share'], 2), '0'), '.') }}%</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </article>
    </section>
    </div>
</div>
@endsection

