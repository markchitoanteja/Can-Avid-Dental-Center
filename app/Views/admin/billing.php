<!-- Content Wrapper -->
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Billing & Payments</h1>
                </div>
                <div class="col-sm-6">
                    <button class="btn btn-primary float-right" data-toggle="modal" data-target="#addBillingModal">
                        <i class="fas fa-plus mr-1"></i>
                        Add Payment
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
                                <th>Client</th>
                                <th>Service</th>
                                <th>Payment Date</th>
                                <th class="text-center">Total Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($billings)): ?>
                                <?php foreach ($billings as $billing): ?>
                                    <tr>
                                        <td><?= esc($billing['client_name']) ?></td>
                                        <td>
                                            <?= esc($billing['service_name']) ?>
                                            <?php if (!empty($billing['items_description'])): ?>
                                                <br>
                                                <small class="text-muted"><?= esc($billing['items_description']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= date('F j, Y', strtotime($billing['payment_date'])) ?></td>
                                        <td class="text-center">₱<?= number_format($billing['total_amount_with_items'], 2) ?></td>
                                        <td class="text-center">
                                            <a href="<?= base_url('print_billing/' . $billing['id']) ?>" class="btn btn-sm btn-success" target="_blank" title="Print Receipt">
                                                <i class="fas fa-print"></i> Print
                                            </a>
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

<!-- Add Payment Modal -->
<div class="modal fade" id="addBillingModal" tabindex="-1" role="dialog" aria-labelledby="addPaymentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="add_billing_form" action="javascript:void(0)">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPaymentModalLabel">Record New Payment</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <!-- Client Selection -->
                    <div class="form-group">
                        <label for="payment_client">Select Client</label>
                        <select id="payment_client" name="client_id" class="custom-select" required>
                            <option value="" selected disabled>Select Client</option>
                            <?php if (!empty($clients)): ?>
                                <?php foreach ($clients as $client): ?>
                                    <option value="<?= esc($client['id']) ?>">
                                        <?= esc($client['name']) ?> — <?= esc($client['phone']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Service Selection + Admin Amount -->
                    <div class="form-group">
                        <label for="payment_service">Select Service</label>
                        <div class="input-group">
                            <select id="payment_service" name="service_id" class="custom-select" required>
                                <option value="" selected disabled>Select Service</option>
                                <?php
                                if (!empty($services)):
                                    $grouped = [];
                                    foreach ($services as $srv) {
                                        $grouped[$srv['category']][] = $srv;
                                    }
                                    foreach ($grouped as $category => $items):
                                ?>
                                        <optgroup label="<?= esc($category) ?>">
                                            <?php foreach ($items as $srv): ?>
                                                <option
                                                    value="<?= esc($srv['id']) ?>"
                                                    data-amount="<?= esc($srv['price'] ?? 0) ?>">
                                                    <?= esc($srv['name']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                <?php endforeach;
                                endif; ?>
                            </select>

                            <input type="number" id="service_admin_amount" name="main_service_amount" class="form-control" placeholder="Amount (₱)" step="0.01" min="0" required>
                        </div>
                    </div>

                    <!-- Miscellaneous Services -->
                    <div class="form-group">
                        <label>Miscellaneous Services <small class="text-muted">(Optional)</small></label>
                        <div id="misc_services_wrapper">
                            <div class="input-group mb-2 misc-item">
                                <input type="text" name="misc_service[]" class="form-control misc_desc" placeholder="Enter miscellaneous service">
                                <input type="number" name="misc_amount[]" class="form-control misc_amount" placeholder="Amount (₱)" step="0.01" min="0">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-danger remove-misc"><i class="fas fa-times"></i></button>
                                </div>
                            </div>
                        </div>
                        <button type="button" id="add_misc_service" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-plus"></i> Add Miscellaneous Service
                        </button>
                    </div>

                    <!-- Total Amount -->
                    <div class="form-group">
                        <label for="payment_total">Total Amount (₱)</label>
                        <input type="number" id="payment_total" name="total_amount" class="form-control" placeholder="Enter total amount" required step="0.01" min="0">
                    </div>

                    <!-- Payment Date -->
                    <div class="form-group">
                        <label for="payment_date">Payment Date</label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control" required value="<?= date('Y-m-d') ?>">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-dismiss="modal">Cancel</button>
                    <button type="submit" id="add_billing_submit" class="btn btn-primary">Save Payment</button>
                </div>
            </form>
        </div>
    </div>
</div>