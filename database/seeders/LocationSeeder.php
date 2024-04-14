<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $locations = [
            'Aşgabat',
            'Ahal welaýaty',
            'Balkan welaýaty',
            'Mary welaýaty',
            'Lebap welaýaty',
            'Daşoguz welaýaty'
        ];

        foreach ($locations as $location) {
            DB::table('locations')->insert([
                'title' => $location,
                'is_active' => true,
            ]);
        }
    }
}
