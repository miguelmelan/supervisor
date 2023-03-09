<?php

namespace Database\Seeders;

use App\Models\PropertyKey;
use App\Models\PropertyValue;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyKeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PropertyKey::factory()
            ->count(10)
            ->has(PropertyValue::factory()->count(rand(5, 10)), 'values')
            ->create();
    }
}
