<?php
namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'admin' => 'all',
            'Owner' => [
                'sales.view',
                'sales.edit',
                'sales.create',
                'sales.update',
                'sharings.view',
                'sharings.edit',
                'sharings.create',
                'sharings.update',
                'users.view',

            ],
            'Investor' => [
                'sales.view',
                'sharings.view',
            ],

        ];

        $listOfpermissions = Permission::get();

        foreach ($permissions as $key => $per) {
            $role = Role::where('name', $key)->first();
            $array_role = [];
            foreach ($listOfpermissions as $c => $p) {
                if ($per == 'all') {
                    $array_role[] = $p->id;
                } else {
                    if (in_array($p->name, $per)) {
                        $array_role[] = $p->id;
                    }
                }
            }

            $role->permissions()->sync($array_role);
            $role->save();

        }
    }
}
