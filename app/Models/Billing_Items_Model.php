<?php

namespace App\Models;

use CodeIgniter\Model;

class Billing_Items_Model extends Model
{
    protected $table = "billing_items";
    protected $primary_key = "id";
    protected $allowedFields = [
        'billing_id',
        'misc_name',
        'misc_amount',
        'created_at',
    ];
}
