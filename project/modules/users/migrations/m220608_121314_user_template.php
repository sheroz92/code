<?php

use yii\db\Migration;

/**
 * Table for User_columns
 */
class m220608_121314_user_template extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_template}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->bigInteger(),
            'name' => $this->string(),
            'entity' => $this->string(),
            'status' => $this->integer(3)->defaultValue(0),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey('fk_user_uc', '{{%user_template}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_uc','{{%user_template}}');
        $this->dropTable('{{%user_template}}');
    }
}
