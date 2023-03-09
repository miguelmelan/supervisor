<?php

namespace Database\Seeders;

use App\Models\AutomatedProcess;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AutomatedProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        AutomatedProcess::factory()
            ->count(10)
            ->create();
    }
}
