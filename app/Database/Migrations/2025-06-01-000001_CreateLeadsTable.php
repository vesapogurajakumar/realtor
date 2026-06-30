<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeadsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'           => ['type' => 'VARCHAR', 'constraint' => 120],
            'email'          => ['type' => 'VARCHAR', 'constraint' => 180],
            'phone'          => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'interest'       => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'message'        => ['type' => 'TEXT', 'null' => true],
            'preferred_time' => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'source'         => ['type' => 'VARCHAR', 'constraint' => 160, 'null' => true],
            'ip'             => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('email');
        $this->forge->addKey('created_at');
        $this->forge->createTable('leads', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('leads', true);
    }
}
