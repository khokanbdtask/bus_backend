<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LayoutDetails extends Migration
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
            'layout_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'row_no' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'column1' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'column2' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'column3' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'column4' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'column5' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'seat_no1' => [
                'type'       => 'VARCHAR',
                'constraint' => 255, // Adjust the constraint based on your requirements
                'default'    => null,
            ],
            'seat_no2' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => null,
            ],
            'seat_no3' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => null,
            ],
            'seat_no4' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => null,
            ],
            'seat_no5' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'default'    => null,
            ],
            'created_at' => [
                'type' => 'datetime',
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'default' => null,
                'on update' => 'CURRENT_TIMESTAMP',
            ],
            'created_by' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'updated_by' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('layout_id', 'layouts', 'id');
        $this->forge->createTable('layout_details');
    }

    public function down()
    {
        $this->forge->dropTable('layout_details');
    }
}
