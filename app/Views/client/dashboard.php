<style>
    .border-4 {
        border-width: 4px !important;
    }

    .card:hover {
        transform: translateY(-3px);
        transition: 0.2s ease-in-out;
    }
</style>

<section class="ftco-section">
    <div class="container">
        <h2 class="mb-5 font-weight-bold text-center">Client Dashboard</h2>

        <!-- Analytics Cards -->
        <div class="row text-center mb-5 justify-content-center">
            <!-- Total Appointments -->
            <div class="col-md-4 col-sm-6 mb-4">
                <a href="<?= base_url('client/appointments') ?>" class="text-decoration-none">
                    <div class="card shadow-sm border-left border-primary border-4 h-100">
                        <div class="card-body py-4">
                            <div class="mb-3">
                                <i class="fas fa-calendar-check fa-3x text-primary"></i>
                            </div>
                            <h6 class="text-muted">Total Appointments</h6>
                            <h3 class="font-weight-bold mb-0">
                                <?= esc($appointmentCount ?? 0) ?>
                            </h3>
                            <span class="stretched-link"></span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Total Messages -->
            <div class="col-md-4 col-sm-6 mb-4">
                <a href="<?= base_url('client/messages') ?>" class="text-decoration-none">
                    <div class="card shadow-sm border-left border-warning border-4 h-100">
                        <div class="card-body py-4">
                            <div class="mb-3">
                                <i class="fas fa-envelope fa-3x text-warning"></i>
                            </div>
                            <h6 class="text-muted">Total Messages</h6>
                            <h3 class="font-weight-bold mb-0">
                                <?= esc($messageCount ?? 0) ?>
                            </h3>
                            <span class="stretched-link"></span>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Recent Appointments Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">
                    <i class="fas fa-clock mr-2 text-primary"></i>Recent Appointments
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover align-middle">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-tooth mr-1"></i> Service</th>
                                <th><i class="fas fa-calendar-day mr-1"></i> Date</th>
                                <th><i class="fas fa-clock mr-1"></i> Time</th>
                                <th><i class="fas fa-phone mr-1"></i> Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)) : ?>
                                <?php foreach ($appointments as $appointment) : ?>
                                    <tr>
                                        <td><?= esc($appointment['service']) ?></td>
                                        <td><?= esc($appointment['appointment_date']) ?></td>
                                        <td><?= esc($appointment['appointment_time']) ?></td>
                                        <td><?= esc($appointment['phone']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No appointments found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>