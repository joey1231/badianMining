<?php
namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [

            'sales' => [
                'view',
                'delete',
                'edit',
                'create',
                'update',
            ],

            'users' => [
                'view',
                'delete',
                'edit',
                'create',
                'update',

            ],

            'sharings' => [
                'view',
                'delete',
                'edit',
                'create',
                'update',

            ],

        ];

        foreach ($permissions as $key => $permission) {

            foreach ($permission as $p) {
                $per = Permission::where('name', $key . '.' . $p)->first();
                if (is_null($per)) {
                    $per = new Permission;
                }

                $per->name = $key . '.' . $p;
                $per->permission = strtoupper($key . ' ' . $p);
                $per->hash_id = hash('sha256', uniqid());
                $per->save();

            }
        }
    }
}
