<?php

namespace App\Models;

use CodeIgniter\Model;

class Message_Model extends Model
{
    protected $table = 'messages';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'subject',
        'content',
        'received_at',
        'is_read',
        'is_deleted',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
