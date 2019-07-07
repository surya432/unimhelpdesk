<?php


use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\User;
use Spatie\Permission\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'role-list',
            'role-create',
            'role-edit',
            'role-delete',
            'user-list',
            'user-create',
            'user-edit',
            'user-delete',
            'permission-list',
            'permission-create',
            'permission-edit',
            'permission-delete',
            
        ];
        $user = User::create(['name'=>"Admin", 'email' => "admin@admin.com",'password'=> Hash::make("admin123")]);
        $role = Role::create([ "name"=>'SuperAdmin', 'guard_name' => 'web']);
        $role = Role::findByName('SuperAdmin');
        $user->assignRole($role->id);


        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name'=>'web']);
            $role->givePermissionTo( $permission);

        }
    }
}
