@php
    $iconMap = [
        'dashboard' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 12.5 12 4l9 8.5"></path><path d="M6.5 10.5V20h11V10.5"></path></svg>',
        'gym' => '<svg viewBox="0 0 24 24" aria-hidden="true"><rect x="4" y="4" width="6" height="6"></rect><rect x="14" y="4" width="6" height="6"></rect><rect x="4" y="14" width="6" height="6"></rect><rect x="14" y="14" width="6" height="6"></rect></svg>',
        'modules' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 7h16"></path><path d="M4 12h16"></path><path d="M4 17h16"></path></svg>',
        'reports' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 20V4"></path><path d="M10 20V10"></path><path d="M15 20V7"></path><path d="M20 20V13"></path></svg>',
        'settings' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 8.5a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Z"></path><path d="M19.4 15a1.8 1.8 0 0 0 .36 1.98l.04.04a2 2 0 1 1-2.83 2.83l-.04-.04a1.8 1.8 0 0 0-1.98-.36 1.8 1.8 0 0 0-1.1 1.65V21a2 2 0 1 1-4 0v-.07a1.8 1.8 0 0 0-1.1-1.65 1.8 1.8 0 0 0-1.98.36l-.04.04a2 2 0 1 1-2.83-2.83l.04-.04A1.8 1.8 0 0 0 4.6 15a1.8 1.8 0 0 0-1.65-1.1H2.9a2 2 0 1 1 0-4h.05a1.8 1.8 0 0 0 1.65-1.1A1.8 1.8 0 0 0 4.24 6.8L4.2 6.76a2 2 0 1 1 2.83-2.83l.04.04A1.8 1.8 0 0 0 9.05 4.3a1.8 1.8 0 0 0 1.1-1.65V2.6a2 2 0 1 1 4 0v.05a1.8 1.8 0 0 0 1.1 1.65 1.8 1.8 0 0 0 1.98-.36l.04-.04a2 2 0 1 1 2.83 2.83l-.04.04a1.8 1.8 0 0 0-.36 1.98 1.8 1.8 0 0 0 1.65 1.1h.05a2 2 0 1 1 0 4h-.05A1.8 1.8 0 0 0 19.4 15Z"></path></svg>',
        'dot' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="2"></circle></svg>',
    ];
@endphp

<aside class="admin-sidebar" aria-label="Sidebar Navigation">
    <div class="brand-lockup">
        <div class="brand-logo-chip">
            <img class="brand-logo-image" src="{{ asset('images/fitpaxpro-logo.png') }}" alt="FitPaxPro logo">
        </div>
        <div class="brand-copy">
            <strong>FitPaxPro</strong>
            <div class="muted">Admin Panel</div>
        </div>
    </div>

    <div class="sidebar-toolbar">
        <button class="icon-button sidebar-collapse-button" type="button" data-sidebar-toggle aria-label="Collapse sidebar">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M15 6 9 12l6 6"></path></svg>
        </button>
    </div>

    <nav class="sidebar-nav" aria-label="Main Navigation">
        @foreach ($navigation as $item)
            @php
                $icon = $iconMap[$item['icon'] ?? 'dot'] ?? $iconMap['dot'];
            @endphp

            @if (($item['type'] ?? 'link') === 'dropdown')
                @php
                    $isActive = collect($item['items'] ?? [])->contains(fn ($child) => ($currentRouteName ?? null) === ($child['route'] ?? null));
                @endphp

                <details class="sidebar-dropdown" {{ $isActive ? 'open' : '' }}>
                    <summary class="sidebar-dropdown-toggle {{ $isActive ? 'active' : '' }}" title="{{ $item['label'] }}">
                        <span class="sidebar-icon">{!! $icon !!}</span>
                        <span class="sidebar-label-text">{{ $item['label'] }}</span>
                        <svg class="sidebar-caret" viewBox="0 0 24 24" aria-hidden="true"><path d="m6 9 6 6 6-6"></path></svg>
                    </summary>

                    <div class="sidebar-submenu">
                        <div class="sidebar-submenu-inner">
                            @foreach ($item['items'] as $child)
                                <a class="sidebar-sublink {{ ($currentRouteName ?? null) === $child['route'] ? 'active' : '' }}" href="{{ route($child['route']) }}" title="{{ $child['label'] }}">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </details>
            @else
                <a class="sidebar-link {{ ($currentRouteName ?? null) === $item['route'] ? 'active' : '' }}" href="{{ route($item['route']) }}" title="{{ $item['label'] }}">
                    <span class="sidebar-icon">{!! $icon !!}</span>
                    <span class="sidebar-label-text">{{ $item['label'] }}</span>
                </a>
            @endif
        @endforeach
    </nav>

    
</aside>
