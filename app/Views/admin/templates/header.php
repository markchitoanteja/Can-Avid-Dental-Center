<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $current_page_title ?> | Can-Avid Dental</title>

    <link rel="shortcut icon" href="<?= base_url('favicon.ico') ?>" type="image/x-icon" />

    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/fontawesome-free/css/all.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/datatables-buttons/css/buttons.bootstrap4.min.css">

    <!-- AdminLTE -->
    <link rel="stylesheet" href="<?= base_url() ?>public/dist/admin/css/adminlte.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/overlayScrollbars/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="<?= base_url('public/dist/admin/css/style.css?v=') . app_version() ?>">

    <style>
        .nav-sidebar .nav-link p {
            width: 100%;
        }

        .nav-sidebar .nav-link .fa-wrench {
            font-size: 0.8rem;
            margin-top: 4px;
            opacity: 0.9;
        }
    </style>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
        <!-- Loading Overlay -->
        <div id="loadingOverlay">
            <div class="spinner"></div>
            <p class="loading-text">Please wait...</p>
        </div>

        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <!-- User Settings Dropdown -->
                <li class="nav-item dropdown">
                    <a class="nav-link" data-toggle="dropdown" href="javascript:void(0)">
                        <i class="fas fa-user-cog"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#updateUserModal">
                            <i class="fas fa-user-circle mr-2"></i> My Profile
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item" data-toggle="modal" data-target="#aboutUsModal">
                            <i class="fas fa-info-circle mr-2"></i> About Us
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="javascript:void(0)" class="dropdown-item logoutBtn">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="dashboard" class="brand-link">
                <img src="<?= base_url() ?>public/dist/admin/img/logo.png?v=<?= app_version() ?>" alt="DentalCare+" class="brand-image img-circle elevation-3" style="opacity: .8">
                <span class="brand-text font-weight-light">Can-Avid Dental</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- User Panel -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="<?= base_url() ?>public/dist/admin/img/uploads/<?= session('user')["image"] ?>" class="img-circle elevation-2" alt="User Image" style="object-fit: cover; width: 35px; height: 35px;">
                    </div>
                    <div class="info">
                        <a href="javascript:void(0)" class="d-block"><?= session('user')["name"] ?></a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="dashboard" class="nav-link <?= ($current_page === 'dashboard') ? 'active' : '' ?> loadable">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        <li class="nav-header">CLINIC OPERATIONS</li>

                        <!-- Appointments -->
                        <li class="nav-item">
                            <a href="appointments" class="nav-link <?= ($current_page === 'appointments') ? 'active' : '' ?> loadable">
                                <i class="nav-icon fas fa-calendar-check"></i>
                                <p>Appointments</p>
                            </a>
                        </li>

                        <!-- Clients -->
                        <li class="nav-item">
                            <a href="clients" class="nav-link <?= ($current_page === 'clients') ? 'active' : '' ?> loadable position-relative">
                                <i class="nav-icon fas fa-user-injured"></i>
                                <p>Clients</p>
                            </a>
                        </li>

                        <!-- Dental Services -->
                        <li class="nav-item">
                            <a href="services" class="nav-link <?= ($current_page === 'services') ? 'active' : '' ?> loadable position-relative">
                                <i class="nav-icon fas fa-tooth"></i>
                                <p class="mb-0">Dental Services</p>
                            </a>
                        </li>

                        <!-- Billing & Payments -->
                        <li class="nav-item">
                            <a href="billing" class="nav-link <?= ($current_page === 'billing') ? 'active' : '' ?> loadable position-relative">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p class="mb-0">
                                    Billing & Payments
                                </p>
                            </a>
                        </li>

                        <li class="nav-header">SYSTEM</li>

                        <!-- Logout -->
                        <li class="nav-item">
                            <a href="javascript:void(0)" class="nav-link loadable logoutBtn">
                                <i class="nav-icon fas fa-sign-out-alt"></i>
                                <p>Logout</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>