<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTriggerRolePermission extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       
        DB::unprepared( 'CREATE TRIGGER tr_role_delete AFTER DELETE ON `roles` FOR EACH ROW
            BEGIN
               DELETE FROM `role_has_permissions` WHERE role_id = old.id;
               DELETE FROM `model_has_roles` WHERE role_id = old.id;
            END');
        DB::unprepared( 'CREATE TRIGGER tr_permissions_delete AFTER DELETE ON `permissions` FOR EACH ROW
        BEGIN
            DELETE FROM `role_has_permissions` WHERE permission_id = old.id;
        END');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'DROP TRIGGER tr_permissions_delete');
        Schema::dropIfExists('DROP TRIGGER tr_role_delete');
    }
}
