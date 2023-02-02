<?php

use app\modules\users\models\User;
use yii\db\Migration;

/**
 * Table for User_columns
 */
class m220622_121314_add_test_superadmin extends Migration
{
    public function up()
    {
        $user = new User();
        $user->login = 'superadmin';
        $user->password = '12345';
        $user->first_name = 'name';
        $user->last_name = 'lname';
        $user->role = User::ROLE_SUPERADMIN;
        $user->status = User::STATUS_ACTIVE;
        $user->save();
    }

    public function down()
    {

    }
}
