<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Clients</h1>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addClientModal">
                        <i class="fas fa-plus mr-1"></i>
                        Add Client
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
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Birthdate</th>
                                <th>Gender</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($users): ?>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?= esc($user['name']) ?></td>
                                        <td><?= esc($user['email']) ?></td>
                                        <td><?= esc($user['phone'] ?? 'N/A') ?></td>
                                        <td><?= esc($user['address'] ?? 'N/A') ?></td>
                                        <td><?= esc($user['birthdate'] ? date('F j, Y', strtotime($user['birthdate'])) : 'N/A') ?></td>
                                        <td><?= esc(ucfirst($user['gender'] ?? 'N/A')) ?></td>
                                        <td class="text-center">
                                            <i class="fas fa-pencil-alt text-primary mr-1 edit_client" role="button" title="Edit Client" data-id="<?= $user['id'] ?>"></i>
                                            <i class="fas fa-trash-alt text-danger delete_client" role="button" title="Delete Client" data-id="<?= $user['id'] ?>"></i>
                                        </td>
                                    </tr>
                                <?php endforeach ?>
                            <?php endif ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addClientModalLabel">Add New Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addClientForm" action="javascript:void(0)">
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <img id="add_client_image_preview" src="<?= base_url() ?>public/dist/admin/img/uploads/default-user-image.webp" alt="Preview" class="rounded-circle border" style="width: 120px; height: 120px; object-fit: cover;">
                        <div class="mt-2">
                            <label for="add_client_image" class="btn btn-sm btn-outline-secondary mb-0">Upload Image</label>
                            <input type="file" id="add_client_image" class="d-none" accept="image/*">
                        </div>
                    </div>

                    <!-- Basic Info -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="add_client_name">Full Name</label>
                            <input type="text" id="add_client_name" class="form-control" placeholder="Enter full name" required>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="add_client_gender">Gender</label>
                            <select id="add_client_gender" class="custom-select" required>
                                <option value="" disabled selected>Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="add_client_birthdate">Birthdate</label>
                            <input type="date" id="add_client_birthdate" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="add_client_phone">Phone</label>
                            <input type="text" id="add_client_phone" class="form-control" placeholder="Enter phone number" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="add_client_email">Email Address</label>
                            <input type="email" id="add_client_email" class="form-control" placeholder="Enter email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="add_client_address">Address</label>
                            <textarea id="add_client_address" rows="2" class="form-control" placeholder="Enter address" required></textarea>
                        </div>
                    </div>

                    <!-- Password Fields -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="add_client_password">Password</label>
                            <input type="password" id="add_client_password" class="form-control" placeholder="Enter password" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="add_client_confirm_password">Confirm Password</label>
                            <input type="password" id="add_client_confirm_password" class="form-control" placeholder="Re-enter password" required>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Client</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Edit Client Modal -->
<div class="modal fade" id="edit_client_modal" tabindex="-1" role="dialog" aria-labelledby="editClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="editClientModalLabel">Edit Client</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <!-- Modal Body -->
            <form id="edit_client_form" action="javascript:void(0)">
                <div class="modal-body">
                    <!-- Image Upload & Preview -->
                    <div class="text-center mb-4">
                        <img
                            id="edit_client_image_preview"
                            src="<?= base_url() ?>public/dist/admin/img/uploads/default-user-image.webp"
                            alt="Preview"
                            class="rounded-circle border"
                            style="width: 120px; height: 120px; object-fit: cover;">
                        <div class="mt-2">
                            <label for="edit_client_image" class="btn btn-sm btn-outline-secondary mb-0">Upload Image</label>
                            <input type="file" id="edit_client_image" class="d-none" accept="image/*">
                        </div>
                    </div>

                    <!-- Hidden ID -->
                    <input type="hidden" id="edit_client_id">

                    <!-- Basic Info -->
                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="edit_client_name">Full Name</label>
                            <input type="text" id="edit_client_name" class="form-control" placeholder="Enter full name" required>
                        </div>
                    </div>

                    <!-- Profile Info -->
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit_client_gender">Gender</label>
                            <select id="edit_client_gender" class="custom-select" required>
                                <option value="" disabled selected>Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="edit_client_birthdate">Birthdate</label>
                            <input type="date" id="edit_client_birthdate" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="edit_client_phone">Phone</label>
                            <input type="text" id="edit_client_phone" class="form-control" placeholder="Enter phone number" required>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="edit_client_email">Email Address</label>
                            <input type="email" id="edit_client_email" class="form-control" placeholder="Enter email" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-12">
                            <label for="edit_client_address">Address</label>
                            <textarea id="edit_client_address" rows="2" class="form-control" placeholder="Enter address" required></textarea>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="edit_client_submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>