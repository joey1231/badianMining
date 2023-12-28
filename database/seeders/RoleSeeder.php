<?php
namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'display_name' => 'Admin',
                'name' => 'admin',
            ],
            [
                'display_name' => 'Investor',
                'name' => 'investor',
            ],

            [
                'display_name' => 'Owner',
                'name' => 'owner',
            ],

        ];
        foreach ($roles as $key => $role) {
            $new_role = Role::where('name', $role['name'])->first();
            if (is_null($new_role)) {
                $new_role = new Role;
            }
            $new_role->role = $role['display_name'];
            $new_role->name = $role['name'];
            $new_role->hash_id = hash('sha256', uniqid());
            $new_role->save();
        }
    }
}
