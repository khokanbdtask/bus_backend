<?php
namespace Modules\Layout\Database\Migrations;

use CodeIgniter\Database\Migration;

class Layouts extends Migration
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
            'layout_number' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'car_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'total_seat' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'total_row' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'total_column' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('layouts');
    }

    public function down()
    {
        //
        $this->forge->dropTable('layouts');
    }
}
