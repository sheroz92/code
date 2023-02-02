<?php

use yii\db\Migration;

/**
 * Table for User_columns
 */
class m220609_121314_user_template_column extends Migration
{
    public function up()
    {
        $this->createTable('{{%user_template_column}}', [
            'id' => $this->primaryKey(),
            'template_id' => $this->integer(),
            'field' => $this->string(),
            'attribute' => $this->string(),
            'value' => $this->string(),
            'created_at' => $this->dateTime()->notNull(),
            'updated_at' => $this->dateTime()->notNull(),
        ]);

        $this->addForeignKey('fk_template_id', '{{%user_template_column}}', 'template_id', '{{%user_template}}', 'id', 'CASCADE');
    }

    public function down()
    {
        $this->dropForeignKey('fk_template_id', '{{%user_template_column}}');
        $this->dropTable('{{%user_template_column}}');
    }
}
