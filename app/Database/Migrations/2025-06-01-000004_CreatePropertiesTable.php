<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePropertiesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'code'          => ['type' => 'VARCHAR', 'constraint' => 40],
            'title'         => ['type' => 'VARCHAR', 'constraint' => 180],
            'type'          => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'status'        => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'For Sale'],
            'featured'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'price'         => ['type' => 'BIGINT', 'constraint' => 15, 'default' => 0],
            'beds'          => ['type' => 'INT', 'constraint' => 4, 'default' => 0],
            'baths'         => ['type' => 'INT', 'constraint' => 4, 'default' => 0],
            'sqft'          => ['type' => 'INT', 'constraint' => 8, 'default' => 0],
            'lot_size'      => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'year_built'    => ['type' => 'INT', 'constraint' => 4, 'null' => true],
            'mls'           => ['type' => 'VARCHAR', 'constraint' => 40, 'null' => true],
            'address'       => ['type' => 'VARCHAR', 'constraint' => 180, 'null' => true],
            'city'          => ['type' => 'VARCHAR', 'constraint' => 90, 'null' => true],
            'state'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'zip'           => ['type' => 'VARCHAR', 'constraint' => 15, 'null' => true],
            'lat'           => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'lng'           => ['type' => 'DECIMAL', 'constraint' => '10,7', 'null' => true],
            'neighborhood'  => ['type' => 'VARCHAR', 'constraint' => 90, 'null' => true],
            'description'   => ['type' => 'TEXT', 'null' => true],
            'hoa'           => ['type' => 'INT', 'constraint' => 10, 'default' => 0],
            'garage'        => ['type' => 'INT', 'constraint' => 4, 'default' => 0],
            'walk_score'    => ['type' => 'INT', 'constraint' => 4, 'default' => 0],
            'transit_score' => ['type' => 'INT', 'constraint' => 4, 'default' => 0],
            'virtual_tour'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'listed_date'   => ['type' => 'DATE', 'null' => true],
            'images'        => ['type' => 'TEXT', 'null' => true],   // JSON array
            'features'      => ['type' => 'TEXT', 'null' => true],   // JSON array
            'amenities'     => ['type' => 'TEXT', 'null' => true],   // JSON array
            'agent'         => ['type' => 'TEXT', 'null' => true],   // JSON object
            'pois'          => ['type' => 'TEXT', 'null' => true],   // JSON array
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('status');
        $this->forge->addKey('city');
        $this->forge->createTable('properties', true);
    }

    public function down(): void
    {
        $this->forge->dropTable('properties', true);
    }
}
