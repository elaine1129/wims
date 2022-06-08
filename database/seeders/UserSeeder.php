<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'staff',
            'username' => 'staff',
            'password' => Hash::make('staff'),
            'email' => 'staff@wims.com',
            'contact_no' => '0123456789',
            'ic_no' => '1234567-67-1234',
            'address' => 'asdfsd',
            'role' => 'Staff',
            'employed_in' => 'June 2020',
            'warehouse_id' => 1

        ]);
    }
}
