<?php

namespace App\Models;

use CodeIgniter\Model;

class Services_Model extends Model
{
    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'name',
        'category'
    ];
}
