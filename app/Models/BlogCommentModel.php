<?php

declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;

class BlogCommentModel extends Model
{
    protected $table         = 'blog_comments';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $allowedFields  = [
        'post_slug', 'name', 'email', 'comment', 'is_approved', 'ip', 'created_at',
    ];

    protected $validationRules = [
        'post_slug' => 'required|max_length[160]',
        'name'      => 'required|min_length[2]|max_length[120]',
        'email'     => 'required|valid_email|max_length[180]',
        'comment'   => 'required|min_length[3]|max_length[2000]',
    ];
}
