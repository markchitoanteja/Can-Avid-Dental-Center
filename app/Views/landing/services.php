<section class="ftco-section">
    <div class="container">
        <div class="row justify-content-center mb-5 pb-5">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <h2 class="mb-2">Our Services Keep You Smiling</h2>
                <p>At DentalCare+, we provide gentle, expert care using the latest technology to keep your smile healthy and bright.</p>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 d-flex align-self-stretch ftco-animate">
                <div class="media block-6 services d-block text-center">
                    <div class="icon d-flex justify-content-center align-items-center">
                        <span class="flaticon-tooth-1"></span>
                    </div>
                    <div class="media-body p-2 mt-3">
                        <h3 class="heading">Teeth Whitening</h3>
                        <p>Brighten your smile safely and effectively with our professional teeth whitening treatments tailored just for you.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-self-stretch ftco-animate">
                <div class="media block-6 services d-block text-center">
                    <div class="icon d-flex justify-content-center align-items-center">
                        <span class="flaticon-dental-care"></span>
                    </div>
                    <div class="media-body p-2 mt-3">
                        <h3 class="heading">Teeth Cleaning</h3>
                        <p>Maintain a healthy smile with our thorough, gentle dental cleanings designed to keep your teeth and gums fresh.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-self-stretch ftco-animate">
                <div class="media block-6 services d-block text-center">
                    <div class="icon d-flex justify-content-center align-items-center">
                        <span class="flaticon-tooth-with-braces"></span>
                    </div>
                    <div class="media-body p-2 mt-3">
                        <h3 class="heading">Quality Brackets</h3>
                        <p>We provide comfortable, high-quality orthodontic solutions to help you achieve a beautiful, confident smile.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 d-flex align-self-stretch ftco-animate">
                <div class="media block-6 services d-block text-center">
                    <div class="icon d-flex justify-content-center align-items-center">
                        <span class="flaticon-anesthesia"></span>
                    </div>
                    <div class="media-body p-2 mt-3">
                        <h3 class="heading">Modern Anesthetic</h3>
                        <p>Experience pain-free dental treatments with our advanced anesthetic techniques focused on your comfort.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="ftco-section bg-light">
    <div class="container">
        <div class="row justify-content-center mb-5 pb-5">
            <div class="col-md-7 text-center heading-section ftco-animate">
                <h2 class="mb-3">Affordable Dental Packages</h2>
                <p>Quality dental care made accessible — choose a plan that fits your needs and budget.</p>
            </div>
        </div>
        <div class="row">
            <?php foreach ($packages as $package): ?>
                <div class="col-md-3 ftco-animate">
                    <div class="pricing-entry pb-5 text-center">
                        <div>
                            <h3 class="mb-4"><?= esc($package['name']) ?></h3>
                            <p>
                                <span class="price">₱<?= number_format($package['price'], 0) ?></span>
                                <span class="per">/ <?= esc($package['unit']) ?></span>
                            </p>
                        </div>
                        <ul>
                            <?php foreach (explode("\n", $package['features']) as $feature): ?>
                                <li><?= esc($feature) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <p class="button text-center">
                            <a href="javascript:void(0)"
                                class="btn btn-primary btn-outline-primary px-4 py-3 btn-book-package"
                                data-id="<?= $package['id'] ?>"
                                data-name="<?= esc($package['name']) ?> Package">
                                Book Now
                            </a>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>