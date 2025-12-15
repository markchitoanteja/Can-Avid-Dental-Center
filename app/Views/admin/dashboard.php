<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Dashboard Content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Info boxes -->
            <div class="row">
                <!-- Total Appointments -->
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Appointments</span>
                            <span class="info-box-number"><?= esc($totalAppointments) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Total Clients -->
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-success"><i class="fas fa-user-injured"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Clients</span>
                            <span class="info-box-number"><?= esc($totalClients) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Total Services -->
                <div class="col-md-4 col-sm-6 col-12">
                    <div class="info-box">
                        <span class="info-box-icon bg-warning"><i class="fas fa-tooth"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">Total Services</span>
                            <span class="info-box-number"><?= esc($totalServices) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Appointments Table -->
            <div class="card mt-4">
                <div class="card-header">
                    <h3 class="card-title">Recent Appointments</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped dataTable">
                        <thead>
                            <tr>
                                <th>Client Name</th>
                                <th>Service</th>
                                <th>Phone</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php $now = new DateTime(); ?>
                                <?php foreach ($appointments as $appt): ?>
                                    <?php
                                    $apptDateTime = new DateTime($appt['appointment_date'] . ' ' . $appt['appointment_time']);
                                    $status = ($apptDateTime < $now) ? 'Completed' : 'Upcoming';
                                    ?>
                                    <tr>
                                        <td><?= esc($appt['client_name'] ?? '—') ?></td>
                                        <td><?= esc($appt['service']) ?></td>
                                        <td><?= esc($appt['phone']) ?></td>
                                        <td><?= esc($appt['appointment_date']) ?></td>
                                        <td><?= esc($appt['appointment_time']) ?></td>
                                        <td class="text-center">
                                            <?php if ($status === 'Completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">Upcoming</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No recent appointments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>