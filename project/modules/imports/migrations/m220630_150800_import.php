<?php

use yii\db\Migration;

/**
 * Table for import
 */
class m220630_150800_import extends Migration
{
    public function up()
    {
        $this->createTable('{{%import}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger(),
            'entity' => $this->string(),
            'file' => $this->string(),
            'percent' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'completed_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey('fk_user_import', '{{%import}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_import', '{{%import}}');
        $this->dropTable('{{%import}}');
    }
}
