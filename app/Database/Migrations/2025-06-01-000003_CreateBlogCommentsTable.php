<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBlogCommentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'post_slug'   => ['type' => 'VARCHAR', 'constraint' => 160],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'email'       => ['type' => 'VARCHAR', 'constraint' => 180],
            'comment'     => ['type' => 'TEXT'],
            'is_approved' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'ip'          => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('post_slug');
        $this->forge->addKey('is_approved');
        $this->forge->createTable('blog_comments', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('blog_comments', true);
    }
}
