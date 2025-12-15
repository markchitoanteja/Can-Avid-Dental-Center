<?php

namespace App\Models;

use CodeIgniter\Model;

class Billing_Model extends Model
{
    protected $table = 'billings';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'client_id',
        'service_id',
        'main_service_amount',
        'total_amount',
        'payment_date',
        'created_at',
    ];
}
