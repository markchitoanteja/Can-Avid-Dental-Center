<style>
    .border-4 {
        border-width: 4px !important;
    }

    .card:hover {
        transform: translateY(-3px);
        transition: 0.2s ease-in-out;
    }
</style>

<section class="ftco-section bg-light py-5">
    <div class="container">
        <h2 class="mb-5 font-weight-bold text-center">Messages</h2>

        <!-- Enhanced Analytics Cards -->
        <div class="row text-center mb-5">
            <!-- Total Messages -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-start border-4 border-primary hover-scale transition">
                    <div class="card-body py-4">
                        <div class="mb-3 position-relative">
                            <i class="fas fa-envelope fa-3x text-primary"></i>
                        </div>
                        <h6 class="text-muted fw-semibold">Total Messages</h6>
                        <h3 class="fw-bold mb-0">
                            <span id="totalMessages"><?= esc(count($messages)) ?></span>
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Unread Messages -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-start border-4 border-danger hover-scale transition position-relative">
                    <div class="card-body py-4">
                        <div class="mb-3 position-relative">
                            <i class="fas fa-envelope-open-text fa-3x text-danger"></i>
                        </div>
                        <h6 class="text-muted fw-semibold">Unread Messages</h6>
                        <h3 class="fw-bold mb-0">
                            <span id="unreadMessages"><?= esc($unreadCount) ?></span>
                        </h3>
                    </div>
                </div>
            </div>

            <!-- Read Messages -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-start border-4 border-success hover-scale transition">
                    <div class="card-body py-4">
                        <div class="mb-3 position-relative">
                            <i class="fas fa-envelope-open fa-3x text-success"></i>
                        </div>
                        <h6 class="text-muted fw-semibold">Read Messages</h6>
                        <h3 class="fw-bold mb-0">
                            <span id="readMessages"><?= esc($readCount) ?></span>
                        </h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Table -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="mb-0">
                    <i class="fas fa-envelope mr-2 text-primary"></i>Recent Messages
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0 table-hover align-middle text-center">
                        <thead class="thead-light">
                            <tr>
                                <th><i class="fas fa-heading mr-1"></i> Subject</th>
                                <th><i class="fas fa-calendar-day mr-1"></i> Received</th>
                                <th><i class="fas fa-cogs mr-1"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($messages)) : ?>
                                <?php foreach ($messages as $msg) : ?>
                                    <tr class="message-row <?= $msg['unread'] ? 'font-weight-bold' : '' ?>" data-id="<?= esc($msg['id']) ?>">
                                        <td><?= esc($msg['subject']) ?></td>
                                        <td><?= esc(date('F j, Y, g:i A', strtotime($msg['received_at']))) ?></td>
                                        <td>
                                            <!-- View Button -->
                                            <button class="btn btn-sm btn-primary view-message-btn"
                                                data-toggle="modal"
                                                data-id="<?= esc($msg['id']) ?>"
                                                data-target="#viewMessageModal"
                                                data-subject="<?= esc($msg['subject']) ?>"
                                                data-date="<?= esc(date('F j, Y, g:i A', strtotime($msg['received_at']))) ?>"
                                                data-content="<?= htmlspecialchars(htmlspecialchars_decode($msg['content']), ENT_QUOTES, 'UTF-8') ?>">
                                                <i class="fas fa-eye"></i> View
                                            </button>

                                            <!-- Delete Button -->
                                            <button class="btn btn-sm btn-danger delete-message-btn" data-id="<?= esc($msg['id']) ?>">
                                                <i class="fas fa-trash-alt"></i> Delete
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No messages found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>