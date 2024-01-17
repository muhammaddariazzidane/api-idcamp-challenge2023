<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeedbacksTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'auto_increment' => true,
                'constraint' => 5,
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => false
            ],
            'report_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => false
            ],
            'comment' => [
                'type' => 'TEXT',
                'null'  => false
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('feedbacks', true);
    }

    public function down()
    {
        $this->forge->dropTable('feedbacks', true);
    }
}
