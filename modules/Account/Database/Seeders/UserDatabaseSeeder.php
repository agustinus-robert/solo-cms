<?php

namespace Modules\Account\Database\Seeders;

use Carbon\Carbon;
use Modules\Account\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class UserDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataUsersAdmin = [
            [
                'name' => 'Administrator',
                'position' => 1,
                'sex' => 1,
                'unit_id' => 1
            ],
        ];


        $dataUsersCasier = [
            [
                'name' => 'Anton',
                'position' => 1,
                'sex' => 1,
                'unit_id' => 11
            ],
        ];



        $dataUsers = array_merge($dataUsersAdmin, $dataUsersCasier);


        $faker = Faker::create();

        foreach ($dataUsers as $key => $value) {
            $user = new User([
                'name' => $value['name'],
                'username' => $this->generateUsername($value['name']),
                'email' => $faker->unique()->safeEmail(),
                'password' => 'password',
                'current_team_id' => 1
            ]);

            $user->save();
            // if ($user->save()) {
            //     $user->setMeta('profile_sex', $value['sex']);
            //     $empl = $user->employee()->create([
            //         'joined_at' => Carbon::parse(now()),
            //         'grade_id' => $value['unit_id']
            //     ]);

            //     $contract = $empl->contract()->create([
            //         'kd' => ($key + 1) . '/SLCMS/'.$value['unit_id'].'/'.date('Y'),
            //         'contract_id' => 2,
            //         'work_location' => 1,
            //         'start_at' => '2021-01-01 01:00:00',
            //         'end_at' => null,
            //         'created_by' => User::first()->id,
            //         'updated_by' => User::first()->id
            //     ]);

            //     $contract->position()->create([
            //         'empl_id' => $contract->empl_id,
            //         'position_id' => $value['position'],
            //         'start_at' => $contract->start_at,
            //         'end_at' => $contract->end_at,
            //     ]);
            // }
        }
    }

    public function generateUsername($name)
    {
        $nameWithoutSpaces = str_replace(' ', '', $name);
        $username = strtolower(substr($nameWithoutSpaces, 0, 8));
        return $username;
    }
};
