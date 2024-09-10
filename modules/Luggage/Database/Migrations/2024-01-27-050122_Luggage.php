<?php

namespace Modules\Luggage\Database\Migrations;

use CodeIgniter\Database\Migration;

class Luggage extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'free_luggage_kg' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'unsigned' => true,
            ],

            'paid_max_luggage_pcs' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],

            'price_pcs' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            'special_max_luggage_pcs' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],

            'special_price_pcs' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'null' => false,
            ],
            

            'created_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('luggage_settings');
    }


    public function down()
    {
        $this->forge->dropTable('luggage_settings');
    }
}
