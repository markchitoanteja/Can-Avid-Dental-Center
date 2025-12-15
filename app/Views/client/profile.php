<style>
    .profile-section img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 4px solid #dee2e6;
    }

    .card-header i {
        margin-right: 8px;
    }

    .form-section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.2rem;
        color: #495057;
    }

    .form-control-file {
        display: inline-block;
        margin-top: 8px;
    }

    hr.section-divider {
        margin: 4rem 0 3rem;
        border-top: 1px solid #dee2e6;
    }
</style>

<section class="ftco-section">
    <div class="container">
        <h2 class="mb-5 font-weight-bold text-center">My Profile</h2>

        <!-- Profile Form -->
        <form action="javascript:void(0)" id="profile_form">
            <div class="row justify-content-center profile-section">
                <!-- Profile Image -->
                <div class="form-group col-12 text-center mb-5">
                    <img id="profile_image_display" src="<?= base_url('public/uploads/client/' . ($user['image'] ?? 'default-user-image.webp')) ?>" alt="Profile Image" class="rounded-circle shadow-sm mb-3">
                    <div>
                        <input type="file" id="profile_image" class="form-control-file" accept="image/*">
                        <small class="form-text text-muted">Max 2MB. JPG, PNG only.</small>
                    </div>
                </div>
                <!-- Name -->
                <div class="form-group col-md-6">
                    <label for="profile_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="profile_name" value="<?= esc($user['name']) ?>" class="form-control" required>
                </div>
                <!-- Email -->
                <div class="form-group col-md-6">
                    <label for="profile_email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="profile_email" value="<?= esc($user['email']) ?>" class="form-control" required>
                </div>
                <!-- Phone -->
                <div class="form-group col-md-6">
                    <label for="profile_phone"><i class="fas fa-phone"></i> Phone</label>
                    <input type="text" id="profile_phone" value="<?= esc($profile['phone'] ?? '') ?>" class="form-control" required>
                </div>
                <!-- Address -->
                <div class="form-group col-md-6">
                    <label for="profile_address"><i class="fas fa-map-marker-alt"></i> Address</label>
                    <input type="text" id="profile_address" value="<?= esc($profile['address'] ?? '') ?>" class="form-control" required>
                </div>
                <!-- Birthdate -->
                <div class="form-group col-md-6">
                    <label for="profile_birthdate"><i class="fas fa-birthday-cake"></i> Birthdate</label>
                    <input type="date" id="profile_birthdate" value="<?= esc($profile['birthdate'] ?? '') ?>" class="form-control" required>
                </div>
                <!-- Gender -->
                <div class="form-group col-md-6">
                    <label for="profile_gender"><i class="fas fa-venus-mars"></i> Gender</label>
                    <select class="form-control" id="profile_gender" required>
                        <option value disabled <?= !isset($profile['gender']) ? 'selected' : '' ?>>-- Select Gender --</option>
                        <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                        <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                        <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
            </div>
            <div class="text-right mt-4">
                <button class="btn btn-primary px-4" id="profile_update_submit"><i class="fas fa-save"></i> Update Profile</button>
            </div>
        </form>

        <hr class="section-divider">

        <!-- Change Password -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-key text-warning"></i> Change Password</h5>
            </div>
            <div class="card-body">
                <form action="javascript:void(0)" id="profile_password_form">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="profile_current_password"><i class="fas fa-lock"></i> Current Password</label>
                            <input type="password" id="profile_current_password" class="form-control" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="profile_new_password"><i class="fas fa-lock-open"></i> New Password</label>
                            <input type="password" id="profile_new_password" class="form-control" required>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="profile_confirm_password"><i class="fas fa-check-circle"></i> Confirm Password</label>
                            <input type="password" id="profile_confirm_password" class="form-control" required>
                        </div>
                    </div>

                    <div class="text-right mt-3">
                        <button class="btn btn-warning px-4" id="profile_password_submit"><i class="fas fa-exchange-alt"></i> Change Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>