<?php

use yii\db\Migration;

/**
 * Table for User
 */
class m220601_121212_user extends Migration
{
    public function up()
    {
        $this->createTable('user', [
            'id' => $this->bigPrimaryKey(),
            'login' => $this->string()->notNull()->unique(),
            'email' => $this->string(),
            'phone' => $this->string(),
            'first_name' => $this->string(),
            'last_name' => $this->string(),
            'password_hash' => $this->string(),
            'auth_key' => $this->string(),
            'status' => $this->string(20)->defaultValue('active'),
            'role' => $this->string()->notNull()->defaultValue('user'),
            'created_at' => $this->dateTime(),
            'updated_at' => $this->dateTime(),
        ]);
    }

    public function down()
    {
        $this->dropTable('user');
    }
}
