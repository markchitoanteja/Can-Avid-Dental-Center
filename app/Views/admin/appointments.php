<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Appointments</h1>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#appointmentModal">
                        <i class="fas fa-plus mr-1"></i>
                        Add Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <section class="content">
        <div class="container-fluid">
            <div class="card">
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
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($appointments)): ?>
                                <?php $now = new DateTime(); ?>
                                <?php foreach ($appointments as $i => $appt): ?>
                                    <?php
                                    // Combine date and time into one DateTime object
                                    $apptDateTime = new DateTime($appt['appointment_date'] . ' ' . $appt['appointment_time']);

                                    // Determine status
                                    $status = ($apptDateTime < $now) ? 'Completed' : 'Upcoming';
                                    ?>
                                    <tr>
                                        <td><?= htmlspecialchars($appt['client_name']) ?></td>
                                        <td><?= htmlspecialchars($appt['service']) ?></td>
                                        <td><?= htmlspecialchars($appt['phone']) ?></td>
                                        <td><?= htmlspecialchars($appt['appointment_date']) ?></td>
                                        <td><?= htmlspecialchars($appt['appointment_time']) ?></td>
                                        <td class="text-center">
                                            <?php if ($status === 'Completed'): ?>
                                                <span class="badge badge-success">Completed</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">Upcoming</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($status === 'Upcoming'): ?>
                                                <i class="fas fa-pencil-alt text-primary mr-1 edit_appointment" role="button" title="Edit Appointment" data-id="<?= $appt['id'] ?>"></i>
                                                <i class="fas fa-trash-alt text-danger cancel_appointment" role="button" title="Cancel Appointment" data-id="<?= $appt['id'] ?>"></i>
                                            <?php else: ?>
                                                —
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
    </section>
</div>

<!-- Modal: Add Appointment -->
<div class="modal fade" id="appointmentModal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="javascript:void(0)" id="add_appointment_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appointmentModalLabel">Add Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="main-form">
                        <div class="form-group">
                            <label for="add_appointment_service">Service</label>
                            <select id="add_appointment_service" class="custom-select" required>
                                <option value="" selected disabled>Select Service</option>
                                <?php if (!empty($groupedServices)): ?>
                                    <?php foreach ($groupedServices as $category => $services): ?>
                                        <optgroup label="<?= esc($category) ?>">
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?= esc($service) ?>"><?= esc($service) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_appointment_client_id">Client Name</label>
                            <select id="add_appointment_client_id" class="custom-select" required>
                                <option value="" selected disabled>Select Client</option>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= esc($user['id']) ?>"><?= esc($user['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="add_appointment_phone">Phone</label>
                            <input type="text" class="form-control" id="add_appointment_phone" placeholder="Enter phone number" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="add_appointment_date">Appointment Date</label>
                                <input type="date" class="form-control" id="add_appointment_date" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="add_appointment_time">Appointment Time</label>
                                <input type="time" class="form-control" id="add_appointment_time" required>
                            </div>
                        </div>
                    </div>
                    <div class="loading justify-content-center align-items-center d-none" style="height: 200px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="loading-text ml-2">Please wait...</div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Edit Appointment -->
<div class="modal fade" id="edit_appointment_modal" tabindex="-1" role="dialog" aria-labelledby="appointmentModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form action="javascript:void(0)" id="edit_appointment_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Appointment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="loading justify-content-center align-items-center d-flex" style="height: 200px;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <div class="loading-text ml-2">Please wait...</div>
                    </div>

                    <div class="main-form d-none">
                        <input type="hidden" id="edit_appointment_id">

                        <div class="form-group">
                            <label for="edit_appointment_service">Service</label>
                            <select id="edit_appointment_service" class="custom-select" required>
                                <option value="" selected disabled>Select Service</option>
                                <?php if (!empty($groupedServices)): ?>
                                    <?php foreach ($groupedServices as $category => $services): ?>
                                        <optgroup label="<?= esc($category) ?>">
                                            <?php foreach ($services as $service): ?>
                                                <option value="<?= esc($service) ?>"><?= esc($service) ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_appointment_client_id">Client Name</label>
                            <select id="edit_appointment_client_id" class="custom-select" required>
                                <option value="" selected disabled>Select Client</option>
                                <?php if (!empty($users)): ?>
                                    <?php foreach ($users as $user): ?>
                                        <option value="<?= esc($user['id']) ?>"><?= esc($user['name']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_appointment_phone">Phone</label>
                            <input type="text" class="form-control" id="edit_appointment_phone" placeholder="Enter phone number" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="edit_appointment_date">Appointment Date</label>
                                <input type="date" class="form-control" id="edit_appointment_date" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="edit_appointment_time">Appointment Time</label>
                                <input type="time" class="form-control" id="edit_appointment_time" required>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="edit_appointment_submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>