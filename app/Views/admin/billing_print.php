<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?= esc($billing['client_name']) ?></title>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?= base_url() ?>public/plugins/admin/fontawesome-free/css/all.min.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }

        .receipt {
            width: 780px;
            max-width: 100%;
            margin: auto;
            background: #fff;
            border-radius: 12px;
            padding: 30px;
            position: relative;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        }

        /* Watermark */
        .receipt::before {
            content: "";
            background: url('<?= base_url() ?>public/dist/admin/img/logo.png?v=<?= app_version() ?>') no-repeat center;
            opacity: 0.03;
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300px;
            height: 300px;
            transform: translate(-50%, -50%) rotate(-15deg);
            background-size: contain;
            pointer-events: none;
        }

        .header-strip {
            background: #0d6efd;
            height: 6px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 20px;
        }

        .receipt-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .receipt-header img {
            max-height: 80px;
            margin-bottom: 8px;
        }

        .receipt-header h2 {
            font-size: 26px;
            font-weight: 700;
            margin: 0;
            color: #0d6efd;
        }

        .receipt-header p {
            margin: 2px 0;
            font-size: 13px;
            color: #555;
        }

        .cards {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .cards .card {
            flex: 0 0 48%;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            margin-bottom: 10px;
        }

        .cards .card-body p {
            margin-bottom: 4px;
            font-size: 13px;
        }

        /* Services Table Styling */
        .services-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.05);
            border-radius: 10px;
            overflow: hidden;
        }

        .services-table th,
        .services-table td {
            padding: 12px 15px;
            font-size: 14px;
        }

        .services-table thead {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
            text-align: left;
        }

        .services-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .services-table tbody tr:hover {
            background-color: #f1f5f9;
        }

        .services-table .total-row td {
            font-weight: 700;
            font-size: 15px;
            background-color: #e9ecef;
        }

        .signature {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 180px;
            margin-top: 50px;
        }

        .signature p {
            margin-top: 5px;
            font-size: 13px;
        }

        .qr-code-container {
            text-align: center;
            margin-top: 15px;
            padding: 8px;
            border: 1px solid #e3e3e3;
            border-radius: 10px;
            display: inline-block;
            background-color: #fafafa;
        }

        .thank-you {
            text-align: center;
            margin-top: 30px;
            font-size: 15px;
            font-weight: 600;
            color: #0d6efd;
        }

        .no-print {
            margin-top: 20px;
            text-align: center;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                background-color: #fff;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                border: none;
                padding: 20px;
            }

            .qr-code-container {
                border: none;
                background-color: transparent;
            }
        }
    </style>
</head>

<body>
    <div class="receipt">
        <div class="header-strip"></div>
        <div class="receipt-header">
            <img src="<?= base_url() ?>public/dist/admin/img/logo.png?v=<?= app_version() ?>" alt="Logo">
            <h2>Can-Avid Dental Center</h2>
            <p><em>"Your great smile begins with a great dentist."</em></p>
            <p class="font-weight-bold">Official Receipt</p>
        </div>

        <!-- Client & Payment Info Cards -->
        <div class="cards">
            <div class="card">
                <div class="card-body">
                    <p><strong>Client:</strong> <?= esc($billing['client_name']) ?></p>
                    <p><strong>Email:</strong> <?= esc($billing['email'] ?? 'N/A') ?></p>
                    <p><strong>Phone:</strong> <?= esc($billing['phone'] ?? 'N/A') ?></p>
                </div>
            </div>
            <div class="card text-end">
                <div class="card-body">
                    <p><strong>Payment Date:</strong> <?= date('F j, Y', strtotime($billing['payment_date'])) ?></p>
                    <p><strong>Receipt No:</strong> <?= str_pad($billing['id'], 6, '0', STR_PAD_LEFT) ?></p>
                </div>
            </div>
        </div>

        <!-- Services Table -->
        <table class="table services-table table-striped table-hover">
            <thead>
                <tr>
                    <th>Service / Item</th>
                    <th class="text-end">Amount (₱)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= esc($billing['service_name']) ?></td>
                    <td class="text-end"><?= number_format($billing['main_service_amount'], 2) ?></td>
                </tr>
                <?php if (!empty($billing['items'])): ?>
                    <?php foreach ($billing['items'] as $item): ?>
                        <tr>
                            <td><?= esc($item['misc_name']) ?></td>
                            <td class="text-end"><?= number_format($item['misc_amount'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                <tr class="total-row">
                    <td>Total Paid</td>
                    <td class="text-end">₱<?= number_format($billing['total_amount_with_items'], 2) ?></td>
                </tr>
            </tbody>
        </table>

        <!-- Signature Section -->
        <div class="signature">
            <div class="text-center">
                <p>Prepared by:</p>
                <div class="signature-line"></div>
                <p>Authorized Staff</p>
            </div>
            <div class="text-center">
                <p>Received by:</p>
                <div class="signature-line"></div>
                <p><?= esc($billing['client_name']) ?></p>
            </div>
            <div class="text-center qr-code-container">
                <p>Verify Receipt</p>
                <img src="<?= $qrCodeDataUri ?>" alt="QR Code" style="width:120px; height:120px;">
            </div>
        </div>

        <!-- Thank You -->
        <div class="thank-you">
            Thank you for your payment!
        </div>

        <!-- Print Button -->
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-primary"><i class="fas fa-print"></i> Print Receipt</button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= base_url() ?>public/plugins/admin/jquery/jquery.min.js"></script>
    <script src="<?= base_url() ?>public/plugins/admin/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>