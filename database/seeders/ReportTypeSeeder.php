<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReportTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reportTypes = [
            ['title' => 'Spam', 'message_required' => false],
            ['title' => 'Kezzapçylyk', 'message_required' => false],
            ['title' => 'Ýalňyş maglumat', 'message_required' => false],
            ['title' => 'Azar bermek', 'message_required' => false],
            ['title' => 'Aldaw ýa-da galplyk', 'message_required' => false],
            ['title' => 'Intellektual eýeçiligiň bozulmagy', 'message_required' => false],
            ['title' => 'Başga ýagdaýlar', 'message_required' => true],
        ];

        foreach ($reportTypes as $type) {
            DB::table('report_types')->insert([
                'title' => $type['title'],
                'is_active' => true,
                'message_required' => $type['message_required'],
            ]);
        }
    }
}
