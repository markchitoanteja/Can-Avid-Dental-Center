<?php

namespace App\Models;

use CodeIgniter\Model;

class Package_Model extends Model
{
    protected $table = 'packages';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'price',
        'unit',
        'features'
    ];
}
