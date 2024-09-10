<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeatElement extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'element' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('seat_elements');
    }

    public function down()
    {
        $this->forge->dropTable('seat_elements');
    }
}
