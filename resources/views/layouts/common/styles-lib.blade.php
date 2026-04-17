@inject('settings', 'App\Services\SettingService')

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="shortcut icon" href="{{ $settings->getImageUrl('favicon') }}">

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<link href="{{ asset('assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

<link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

<link href="{{ asset('assets/css/app.css') }}" rel="stylesheet" type="text/css" />

<script src="{{ asset('assets/js/head.js') }}"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<link href="https://cdn.jsdelivr.net/npm/summernote@0.9.0/dist/summernote.min.css" rel="stylesheet">
<style>
    :root {
        --bs-border-color: #343a40;
        --sidebar-bg: #212529;
        --content-bg: #1a1c20;
        --card-bg: #25282c;
        --text-main: #ffffff;
        --text-muted: #ced4da;
        --rich-red: #E11218;
        --rich-red-hover: #a0010f;
    }

    body {
        background-color: var(--content-bg) !important;
        color: var(--text-main) !important;
    }

    /* SideBar Enhancements */
    .app-sidebar-menu { 
        background-color: var(--sidebar-bg) !important;
        border-right: 1px solid var(--bs-border-color) !important;
    }
    
    #sidebar-menu ul li a {
        color: #ced4da !important;
    }
    
    #sidebar-menu ul li a:hover, 
    #sidebar-menu ul li a.active,
    .tp-link.active {
        color: #ffffff !important;
        background-color: rgba(255, 255, 255, 0.08) !important;
    }
    
    .sidebar-text, .nav-icon iconify-icon {
        color: inherit !important;
    }

    .menu-title {
        color: #4b5563 !important;
        text-transform: uppercase;
        font-weight: 700;
    }

    /* Content Area & Cards */
    .content-page {
        background-color: var(--content-bg) !important;
    }

    .card {
        background-color: var(--card-bg) !important;
        border: 1px solid var(--bs-border-color) !important;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.2) !important;
    }

    .card-title, .card-header, .h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
        color: var(--rich-red) !important;
        font-weight: 700 !important;
    }

    /* Tables */
    .table {
        color: var(--text-main) !important;
    }
    .table thead th {
        background-color: rgba(255, 255, 255, 0.02) !important;
        border-bottom-color: var(--bs-border-color) !important;
        color: var(--text-muted) !important;
    }
    .table td {
        border-top-color: var(--bs-border-color) !important;
    }

    /* Header & Logo */
    .topbar-custom { 
        background-color: var(--sidebar-bg) !important;
        border-bottom: 1px solid var(--bs-border-color) !important;
    }
    
    .logo-box {
        background-color: var(--sidebar-bg) !important;
        border-right: 1px solid var(--bs-border-color) !important;
        border-bottom: 1px solid var(--bs-border-color) !important;
    }

    /* Form Elements */
    .form-control, .form-select, .select2-container--default .select2-selection--single { 
        background-color: #1f2937 !important;
        border-color: var(--bs-border-color) !important;
        color: var(--text-main) !important;
    }
    
    .form-label {
        color: var(--text-muted) !important;
    }

    /* Overriding potential light-theme utilities */
    .bg-white, .bg-light { background-color: var(--card-bg) !important; }
    .text-dark { color: var(--text-main) !important; }
    .text-black { color: var(--text-main) !important; }
    
    /* Scrollbar */
    ::-webkit-scrollbar { width: 6px; height: 6px; }
    ::-webkit-scrollbar-track { background: var(--content-bg); }
    ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #475569; }

    .btn-primary {
        background-color: var(--rich-red) !important;
        border-color: var(--rich-red) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    
    .btn-primary:hover {
        background-color: var(--rich-red-hover) !important;
        border-color: var(--rich-red-hover) !important;
        filter: brightness(1.2);
    }

    .dropdown-toggle::after {
        display: none !important;
    }

    /* Sidebar Icons Styling */
    .nav-icon iconify-icon {
        font-size: 1.15rem !important;
        vertical-align: middle;
        opacity: 0.8;
    }
    
    .tp-link[href*="dashboard"] .nav-icon iconify-icon { color: var(--rich-red) !important; } /* Rich Red */
    .tp-link[href*="gym"] .nav-icon iconify-icon { color: #34d399 !important; } /* Emerald */
    .tp-link[href*="settings"] .nav-icon iconify-icon { color: #fbbf24 !important; } /* Amber */
    
    .tp-link:hover .nav-icon iconify-icon, 
    .tp-link.active .nav-icon iconify-icon {
        opacity: 1 !important;
        filter: drop-shadow(0 0 5px currentColor);
    }

    .footer {
        height: 50px !important;
        line-height: 50px !important;
        padding: 0 15px !important;
    }
</style>