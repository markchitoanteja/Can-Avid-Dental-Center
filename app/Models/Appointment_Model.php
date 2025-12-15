<?php

namespace App\Models;

use CodeIgniter\Model;

class Appointment_Model extends Model
{
    protected $table = "appointments";
    protected $primary_key = "id";
    protected $allowedFields = [
        'client_id',
        'service',
        'phone',
        'appointment_date',
        'appointment_time',
        'created_at',
    ];
}
