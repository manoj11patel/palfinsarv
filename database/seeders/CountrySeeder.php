<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'India', 'code' => 'IN'],
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'United Kingdom', 'code' => 'GB'],
            ['name' => 'Canada', 'code' => 'CA'],
            ['name' => 'Australia', 'code' => 'AU'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'Singapore', 'code' => 'SG'],
            ['name' => 'United Arab Emirates', 'code' => 'AE'],
            ['name' => 'Saudi Arabia', 'code' => 'SA'],
            ['name' => 'Nepal', 'code' => 'NP'],
            ['name' => 'Bangladesh', 'code' => 'BD'],
            ['name' => 'Sri Lanka', 'code' => 'LK'],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(
                ['code' => $country['code']],
                $country
            );
        }
    }
}
