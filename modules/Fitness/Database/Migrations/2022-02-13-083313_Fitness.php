<?php

namespace Modules\Fitness\Database\Migrations;

use CodeIgniter\Database\Migration;

class Fitness extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'  => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],


            'vehicle_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],


            'fitness_name' => [
                'type'    => 'TINYTEXT',
            ],

            'start_date' => [
                'type'    => 'TINYTEXT',
            ],
            'end_date' => [
                'type'    => 'TINYTEXT',
            ],
            'start_milage' => [
                'type'    => 'TINYTEXT',
            ],

            'end_milage' => [
                'type'    => 'TINYTEXT',
                'null' => true,
            ],
            'total_milage' => [ // New field: total_milage
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        
            'tire_condition' => [ // New field: tire_condition
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        
            'windshield_washer_condition' => [ // New field: windshield_washer_condition
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        
            'windshield_condition' => [ // New field: windshield_condition
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        
            'wiper_condition' => [ // New field: wiper_condition
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        
            'overall_car_condition' => [ // New field: overall_car_condition
                'type' => 'TINYTEXT',
                'null' => true,
            ],
        
            'remarks' => [
                'type' => 'TEXT', // Changed type to TEXT for larger text storage
                'null' => true,
            ],
            'driver_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            // New field: subtrip_id, bigint
            'subtrip_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => true,
            ],


            'created_at datetime default current_timestamp',
            'updated_at datetime default current_timestamp on update current_timestamp',
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('fitnesses');
    }

    public function down()
    {
        $this->forge->dropTable('fitnesses');
    }
}
