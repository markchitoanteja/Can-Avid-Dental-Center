<style>
    .border-4 {
        border-width: 4px !important;
    }

    .card:hover {
        transform: translateY(-3px);
        transition: 0.2s ease-in-out;
    }

    .table td,
    .table th {
        vertical-align: middle;
    }
</style>

<section class="ftco-section">
    <div class="container">
        <h2 class="mb-5 font-weight-bold text-center">My Appointments</h2>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">
                    <i class="fas fa-calendar-check mr-2 text-primary"></i>Appointment History
                </h5>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover align-middle text-center">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-tooth mr-1"></i> Service</th>
                                <th><i class="fas fa-calendar-day mr-1"></i> Date</th>
                                <th><i class="fas fa-clock mr-1"></i> Time</th>
                                <th><i class="fas fa-info-circle mr-1"></i> Status</th>
                                <th><i class="fas fa-cogs mr-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($appointments)): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No appointments found.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($appointments as $appointment): ?>
                                    <?php
                                    $appointmentDateTime = strtotime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
                                    $now = time();
                                    $today = date('Y-m-d');
                                    $appointmentDate = date('Y-m-d', $appointmentDateTime);

                                    if ($appointmentDate === $today) {
                                        $status = ($appointmentDateTime > $now) ? 'Today' : 'Completed';
                                    } elseif ($appointmentDateTime > $now) {
                                        $status = 'Upcoming';
                                    } else {
                                        $status = 'Completed';
                                    }

                                    $badgeClass = match ($status) {
                                        'Upcoming'  => 'badge-info',
                                        'Today'     => 'badge-warning',
                                        'Completed' => 'badge-success',
                                        default     => 'badge-secondary',
                                    };
                                    ?>
                                    <tr>
                                        <td><?= esc($appointment['service'] ?? '—') ?></td>
                                        <td><?= esc(date('F j, Y', strtotime($appointment['appointment_date']))) ?></td>
                                        <td><?= esc(date('h:i A', strtotime($appointment['appointment_time']))) ?></td>
                                        <td>
                                            <span class="badge <?= $badgeClass ?>"><?= $status ?></span>
                                        </td>
                                        <td>
                                            <?php if ($status === 'Upcoming'): ?>
                                                <button class="btn btn-sm btn-danger btn-delete-appointment" data-id="<?= esc($appointment['id']) ?>">
                                                    <i class="fas fa-trash-alt"></i> Cancel
                                                </button>
                                            <?php else: ?>
                                                <span class="text-muted">—</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>