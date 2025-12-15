        <!-- Update User Modal -->
        <div class="modal fade" id="updateUserModal" tabindex="-1" role="dialog" aria-labelledby="updateUserModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md" role="document">
                <div class="modal-content">
                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateUserModalLabel">My Profile</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Form -->
                    <form id="my_profile_form" action="javascript:void(0)">
                        <div class="modal-body">
                            <input type="hidden" id="my_profile_id" value="<?= $user['id'] ?>">

                            <!-- Image -->
                            <div class="form-group">
                                <div class="mb-3 text-center">
                                    <img id="my_profile_preview_image" src="<?= base_url() ?>public/dist/admin/img/uploads/<?= $user['image'] ?>" alt="User Image" class="img-thumbnail rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                                </div>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" id="my_profile_image" accept="image/*">
                                    <label class="custom-file-label" for="my_profile_image">Choose file</label>
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="form-group">
                                <label for="my_profile_name">Full Name</label>
                                <input type="text" class="form-control" id="my_profile_name" placeholder="Enter full name" value="<?= $user['name'] ?>" required>
                            </div>

                            <!-- Email -->
                            <div class="form-group">
                                <label for="my_profile_email">Email Address</label>
                                <input type="email" class="form-control" id="my_profile_email" placeholder="Enter email" value="<?= $user['email'] ?>" required>
                            </div>

                            <!-- Password -->
                            <div class="form-group">
                                <label for="my_profile_password">Password <small class="text-muted">(leave blank if unchanged)</small></label>
                                <input type="password" class="form-control" id="my_profile_password" placeholder="Enter new password">
                            </div>

                            <!-- Confirm Password -->
                            <div class="form-group">
                                <label for="my_profile_confirm_password">Confirm Password</label>
                                <input type="password" class="form-control" id="my_profile_confirm_password" placeholder="Confirm new password">
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary" id="my_profile_submit">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- About Us Modal -->
        <div class="modal fade" id="aboutUsModal" tabindex="-1" role="dialog" aria-labelledby="aboutUsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                <div class="modal-content">

                    <!-- Header -->
                    <div class="modal-header">
                        <h5 class="modal-title" id="aboutUsModalLabel">Can-Avid Dental Center</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body text-center p-4" style="background-color: #f9f9f9; border-radius: 0 0 10px 10px;">

                        <!-- Logo -->
                        <div class="mb-3">
                            <img src="<?= base_url() ?>public/dist/admin/img/logo.png?v=1.3"
                                alt="Can-Avid Dental Center Logo"
                                class="img-fluid rounded-circle shadow-sm"
                                style="max-height: 120px;">
                        </div>

                        <!-- Tagline -->
                        <h6 class="text-primary font-italic mb-4">"Your great smile begins with a great dentist."</h6>

                        <!-- About Description Card -->
                        <div class="card shadow-sm mb-4">
                            <div class="card-body text-justify">
                                Can-Avid Dental Center is dedicated to providing quality dental care with a focus on comfort, safety, and a bright smile. Our team of experienced professionals ensures every visit is a positive experience.
                            </div>
                        </div>

                        <!-- Developers Card -->
                        <div class="card shadow-sm">
                            <div class="card-header bg-light font-weight-bold text-center">
                                Developers
                            </div>
                            <div class="card-body">
                                <ul class="list-unstyled mb-0">
                                    <li>Aljon Rafil Nofies</li>
                                    <li>Jessa Ortega</li>
                                    <li>Jovie Casillano Ada</li>
                                    <li>Noimi Cantos</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border" data-dismiss="modal">Close</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="main-footer">
            <strong>&copy; 2025 Can-Avid Dental Center.</strong> All rights reserved.
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> <?= app_version() ?>
            </div>
        </footer>
        </div>

        <script>
            const base_url = "<?= base_url() ?>";
            const notification = <?= json_encode(session()->getFlashdata()); ?>;
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Scripts -->
        <script src="<?= base_url() ?>public/plugins/admin/jquery/jquery.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/moment/moment.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/overlayScrollbars/js/jquery.overlayScrollbars.min.js"></script>
        <script src="<?= base_url() ?>public/dist/admin/js/adminlte.js"></script>

        <!-- DataTables JS -->
        <script src="<?= base_url() ?>public/plugins/admin/datatables/jquery.dataTables.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-bs4/js/dataTables.bootstrap4.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-responsive/js/dataTables.responsive.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-responsive/js/responsive.bootstrap4.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-buttons/js/dataTables.buttons.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-buttons/js/buttons.bootstrap4.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/jszip/jszip.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/pdfmake/pdfmake.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/pdfmake/vfs_fonts.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-buttons/js/buttons.html5.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-buttons/js/buttons.print.min.js"></script>
        <script src="<?= base_url() ?>public/plugins/admin/datatables-buttons/js/buttons.colVis.min.js"></script>

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script src="<?= base_url('public/dist/admin/js/script.js?v=') . app_version() ?>"></script>

        </body>

        </html>