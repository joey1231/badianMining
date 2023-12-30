<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\InvestorDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'company' => 'Small Scale Coal Mining',
                'users' => [
                    [
                        'name' => 'Joey Dingcong',
                        'owner' => true,
                        'username' => 'joey1231',
                        'email' => 'joeydngcng1233@gmail.com',
                        'password' => bcrypt('123456'),
                        'role' => [1, 3],
                        'investorDetail' => [
                            'share_type' => 'percentage',
                            'share_value' => 13,
                            'invest_on' => '2023-10-03 00:00:00',
                            'share_start' => '2023-10-03 00:00:00',
                            'invest_value' => 300000,
                        ],
                    ],
                    [
                        'name' => 'Jonifer Dingcong',
                        'owner' => true,
                        'username' => 'jonifer1111',
                        'email' => 'none',
                        'password' => bcrypt('123456'),
                        'role' => [1, 3],
                        'investorDetail' => [
                            'share_type' => 'percentage',
                            'share_value' => 13,
                            'invest_on' => '2023-10-03 00:00:00',
                            'share_start' => '2023-10-03 00:00:00',
                            'invest_value' => 300000,
                        ],
                    ],

                    [
                        'name' => 'Joshua Dingcong',
                        'owner' => true,
                        'username' => 'joshua1111',
                        'email' => 'joeydngcng1233@gmail.com',
                        'password' => bcrypt('123456'),
                        'role' => [3],
                        'investorDetail' => [
                            'share_type' => 'percentage',
                            'share_value' => 13,
                            'invest_on' => '2023-10-03 00:00:00',
                            'share_start' => '2023-10-03 00:00:00',
                            'invest_value' => 300000,
                        ],
                    ],
                    [
                        'name' => 'Nenita Dingcong',
                        'owner' => true,
                        'username' => 'nita1231',
                        'email' => 'nenita@gmail.1',
                        'password' => bcrypt('123456'),
                        'role' => [1, 3],
                        'investorDetail' => [
                            'share_type' => 'percentage',
                            'share_value' => 27.67,
                            'invest_on' => '2023-10-03 00:00:00',
                            'share_start' => '2023-10-03 00:00:00',
                            'invest_value' => 300000,
                        ],
                    ],

                    [
                        'name' => 'Geno Hermosisima',
                        'owner' => true,
                        'username' => 'geno1231',
                        'email' => 'none',
                        'password' => bcrypt('123456'),
                        'role' => [3],
                        'investorDetail' => [
                            'share_type' => 'percentage',
                            'share_value' => 33.33,
                            'invest_on' => '2023-10-03 00:00:00',
                            'share_start' => '2023-10-03 00:00:00',
                            'invest_value' => 300000,
                        ],
                    ],

                ],
            ],
        ];

        foreach ($users as $key => $value) {
            $company = new Company();
            $company->name = $value['company'];
            $company->hash_id = hash('sha256', uniqid());
            $company->save();

            foreach ($value['users'] as $c => $user) {
                $new_user = new User;
                $new_user->name = $user['name'];
                $new_user->is_owner = $user['owner'];
                $new_user->username = $user['username'];
                $new_user->email = $user['email'];
                $new_user->owner = $user['owner'];
                $new_user->password = $user['password'];
                $new_user->hash_id = hash('sha256', uniqid());
                $new_user->company_id = $company->id;

                $new_user->save();
                $new_user->roles()->syncWithoutDetaching($user['role']);

                // if (in_array(2, $user['role'])) {
                $investor_detail = new InvestorDetail;
                $investor_detail->hash_id = hash('sha256', uniqid());
                foreach ($user['investorDetail'] as $key => $v) {
                    $investor_detail->{$key} = $v;
                }
                $investor_detail->user_id = $new_user->id;
                $investor_detail->save();
                //}
            }
        }
    }
}
