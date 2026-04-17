<style>
    .terminal-footer {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        height: 26px; /* Increased by 2px as requested */
        background: #E11218;
        z-index: 1050;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        font-size: 11px; /* Slightly larger and cleaner font */
        font-weight: 700;
        color: #fff;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        box-shadow: 0 -4px 15px rgba(0,0,0,0.3);
        border-top: 1px solid rgba(255,255,255,0.1); /* Subtle highlight edge */
    }
    
    .footer-section { 
        display: flex; 
        align-items: center; 
        gap: 15px; 
        background: transparent !important; /* Force transparent to remove dark strips */
    }

    .footer-section div, .footer-section span {
        background: transparent !important; /* Ensure no inherited dark backgrounds */
    }

    .status-dot { 
        width: 8px; 
        height: 8px; 
        background: #fff; 
        border-radius: 50%; 
        display: inline-block; 
        margin-right: 8px; 
        animation: pulse-glow 2s infinite; 
        box-shadow: 0 0 8px rgba(255,255,255,0.5);
    }
    
    @keyframes pulse-glow {
        0% { opacity: 1; transform: scale(1); box-shadow: 0 0 5px rgba(255,255,255,0.5); }
        50% { opacity: 0.7; transform: scale(1.1); box-shadow: 0 0 12px rgba(255,255,255,0.8); }
        100% { opacity: 1; transform: scale(1); box-shadow: 0 0 5px rgba(255,255,255,0.5); }
    }

    .footer-link { 
        color: #fff !important; 
        text-decoration: none !important; 
        display: flex; 
        align-items: center; 
        gap: 6px; 
        transition: 0.2s; 
        background: transparent !important;
        padding: 2px 8px;
        border-radius: 4px;
    }
    .footer-link:hover { background: rgba(255,255,255,0.1) !important; }
    
    .system-sep {
        width: 1px;
        height: 12px;
        background: rgba(255,255,255,0.3);
        margin: 0 10px;
    }

    /* Adjust body to avoid footer overlap */
    body { padding-bottom: 26px !important; }
</style>

<footer class="terminal-footer">
    <div class="footer-section">
        <div class="d-flex align-items-center" title="Core Connection: Active">
            <span class="status-dot"></span>
            <span>OS: LIVE // CLOUD-NODE-01</span>
        </div>
        <div class="system-sep"></div>
        <div class="d-none d-md-flex align-items-center" title="MariaDB 11.8 Enterprise">
            <iconify-icon icon="tabler:database" class="fs-13 me-1 text-white"></iconify-icon>
            <span>DB: STABLE // RED-CLUSTER</span>
        </div>
    </div>

    <div class="footer-section">
        <a href="{{ route('admin.settings.index') }}" class="footer-link" title="System Configuration">
            <iconify-icon icon="tabler:settings" class="fs-15"></iconify-icon>
            <span>CONFIG</span>
        </a>
        <div class="system-sep"></div>
        <div class="opacity-90 fw-bold">
            <iconify-icon icon="tabler:world" class="me-1 align-middle"></iconify-icon>
            {{ strtoupper(config('app.env')) }} // {{ date('H:i') }} UTC
        </div>
    </div>
</footer>
