<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubscribersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'email'         => ['type' => 'VARCHAR', 'constraint' => 180],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'subscribed_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('subscribers', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('subscribers', true);
    }
}
