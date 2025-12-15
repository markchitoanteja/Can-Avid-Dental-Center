<?php

namespace App\Models;

use CodeIgniter\Model;

class Profile_Model extends Model
{
    protected $table = 'profiles';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'user_id',
        'phone',
        'address',
        'birthdate',
        'gender',
        'created_at',
        'updated_at'
    ];
}
