<?php

namespace App\Models;

use CodeIgniter\Model;

class User_Model extends Model
{
    protected $table = "users";
    protected $primary_key = "id";
    protected $allowedFields = [
        'uuid',
        'name',
        'email',
        'password',
        'image',
        'user_type',
        'created_at',
        'updated_at',
    ];
}
