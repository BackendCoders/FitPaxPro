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
    /* Force Toastr background colors */
    #toast-container>.toast-success {
        background-color: #28a745 !important;
        /* Green */
        color: white !important;
    }

    #toast-container>.toast-error {
        background-color: #dc3545 !important;
        /* Red */
        color: white !important;
    }

    #toast-container>.toast-warning {
        background-color: #ffc107 !important;
        /* Yellow */
        color: black !important;
    }

    #toast-container>.toast-info {
        background-color: #17a2b8 !important;
        /* Blue */
        color: white !important;
    }

    /* Optional: Fix opacity if it looks faded */
    #toast-container>.toast {
        opacity: 1 !important;
    }

    .topbar-custom {
        background: #4d0091 !important;
    }

    .logo-box {
        background: #763d14 !important;
    }

    .sidebar-menu {
        background: aliceblue !important;
    }

    .topbar-custom .topnav-menu .topbar-button {
        background: #4d0091;
        color: white !important;
    }

    .dropdown-toggle::after {
        display: none !important;
    }

    .btn-primary {
        background: brown !important;
        border: brown;
    }

    .table-card {
        padding: 0px !important;
        margin: 0px !important;
    }
</style>