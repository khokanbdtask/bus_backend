<?php

namespace Modules\Ticket\Database\Migrations;

use CodeIgniter\Database\Migration;

class TicketTags extends Migration
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

            'ticket_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],

            'tag' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],

            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id');
        $this->forge->createTable('ticket_tags');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_tags');
    }
}
