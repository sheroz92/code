<?php

use yii\db\Migration;

/**
 * Table for export
 */
class m220619_150800_export extends Migration
{
    public function up()
    {
        $this->createTable('{{%export}}', [
            'id' => $this->bigPrimaryKey(),
            'user_id' => $this->bigInteger(),
            'name' => $this->string(),
            'entity' => $this->string(),
            'filter' => $this->json(),
            'file' => $this->string(),
            'type' => $this->string(),
            'format' => $this->string(),
            'percent' => $this->integer()->notNull()->defaultValue(0),
            'status' => $this->smallInteger()->notNull()->defaultValue(1),
            'completed_at' => $this->dateTime()->null(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey('fk_user_export', '{{%export}}', 'user_id', '{{%user}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_user_export', '{{%export}}');
        $this->dropTable('{{%export}}');
    }
}
