<!-- Content Wrapper -->
<div class="content-wrapper">
    <!-- Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Dental Services</h1>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addServiceModal">
                        <i class="fas fa-plus mr-1"></i> Add Service
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
                                <th>Service Name</th>
                                <th>Category</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($services)): ?>
                                <?php foreach ($services as $index => $service): ?>
                                    <tr>
                                        <td><?= esc($service['name']) ?></td>
                                        <td><?= esc($service['category']) ?></td>
                                        <td class="text-center">
                                            <i class="fas fa-pencil-alt text-primary mr-1 edit_service" role="button" title="Edit" data-id="<?= $service['id'] ?>"></i>
                                            <i class="fas fa-trash-alt text-danger delete_service" role="button" title="Delete" data-id="<?= $service['id'] ?>"></i>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No services found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1" role="dialog" aria-labelledby="addServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="addServiceModalLabel">Add New Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="addServiceForm" action="javascript:void(0)">
                <div class="modal-body">

                    <div class="form-group">
                        <label for="add_service_name">Service Name</label>
                        <input type="text" id="add_service_name" class="form-control" placeholder="Enter service name" required>
                    </div>

                    <div class="form-group">
                        <label for="add_service_category">Category</label>
                        <select id="add_service_category" class="custom-select" required>
                            <option value="" disabled selected>Select category</option>
                            <option value="General Dentistry">General Dentistry</option>
                            <option value="Cosmetic Dentistry">Cosmetic Dentistry</option>
                            <option value="Orthodontics">Orthodontics</option>
                            <option value="Specialty Services">Specialty Services</option>
                        </select>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Edit Service Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1" role="dialog" aria-labelledby="editServiceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form id="editServiceForm" action="javascript:void(0)">
                <div class="modal-body">
                    <input type="hidden" id="edit_service_id">

                    <div class="form-group">
                        <label for="edit_service_name">Service Name</label>
                        <input type="text" id="edit_service_name" class="form-control" placeholder="Enter service name" required>
                    </div>

                    <div class="form-group">
                        <label for="edit_service_category">Category</label>
                        <select id="edit_service_category" class="custom-select" required>
                            <option value="General Dentistry">General Dentistry</option>
                            <option value="Cosmetic Dentistry">Cosmetic Dentistry</option>
                            <option value="Orthodontics">Orthodontics</option>
                            <option value="Specialty Services">Specialty Services</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="edit_service_submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>

        </div>
    </div>
</div>