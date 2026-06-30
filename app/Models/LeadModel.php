<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class LeadModel extends Model
{
    protected $table         = 'leads';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields  = [
        'name', 'email', 'phone', 'interest', 'message',
        'preferred_time', 'source', 'ip', 'created_at',
    ];

    protected $validationRules = [
        'name'  => 'required|min_length[2]|max_length[120]',
        'email' => 'required|valid_email|max_length[180]',
    ];
}
