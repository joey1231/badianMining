<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UpdateUserRole extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            'joey@amafa.com.au', 'tanya@amafa.com.au',
        ];

        foreach ($users as $user) {
            $u = \App\UserAdmin::where('email', $user)->first();
            $u->roles()->sync([1]);
            $u->save();
        }
    }
}
