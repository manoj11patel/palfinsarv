<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $india = Country::where('code', 'IN')->first();
        $indiaId = $india?->id;

        $states = [
            ['name' => 'Andhra Pradesh', 'code' => 'AP', 'country_id' => $indiaId],
            ['name' => 'Arunachal Pradesh', 'code' => 'AR', 'country_id' => $indiaId],
            ['name' => 'Assam', 'code' => 'AS', 'country_id' => $indiaId],
            ['name' => 'Bihar', 'code' => 'BR', 'country_id' => $indiaId],
            ['name' => 'Chhattisgarh', 'code' => 'CT', 'country_id' => $indiaId],
            ['name' => 'Goa', 'code' => 'GA', 'country_id' => $indiaId],
            ['name' => 'Gujarat', 'code' => 'GJ', 'country_id' => $indiaId],
            ['name' => 'Haryana', 'code' => 'HR', 'country_id' => $indiaId],
            ['name' => 'Himachal Pradesh', 'code' => 'HP', 'country_id' => $indiaId],
            ['name' => 'Jharkhand', 'code' => 'JH', 'country_id' => $indiaId],
            ['name' => 'Karnataka', 'code' => 'KA', 'country_id' => $indiaId],
            ['name' => 'Kerala', 'code' => 'KL', 'country_id' => $indiaId],
            ['name' => 'Madhya Pradesh', 'code' => 'MP', 'country_id' => $indiaId],
            ['name' => 'Maharashtra', 'code' => 'MH', 'country_id' => $indiaId],
            ['name' => 'Manipur', 'code' => 'MN', 'country_id' => $indiaId],
            ['name' => 'Meghalaya', 'code' => 'ML', 'country_id' => $indiaId],
            ['name' => 'Mizoram', 'code' => 'MZ', 'country_id' => $indiaId],
            ['name' => 'Nagaland', 'code' => 'NL', 'country_id' => $indiaId],
            ['name' => 'Odisha', 'code' => 'OD', 'country_id' => $indiaId],
            ['name' => 'Punjab', 'code' => 'PB', 'country_id' => $indiaId],
            ['name' => 'Rajasthan', 'code' => 'RJ', 'country_id' => $indiaId],
            ['name' => 'Sikkim', 'code' => 'SK', 'country_id' => $indiaId],
            ['name' => 'Tamil Nadu', 'code' => 'TN', 'country_id' => $indiaId],
            ['name' => 'Telangana', 'code' => 'TG', 'country_id' => $indiaId],
            ['name' => 'Tripura', 'code' => 'TR', 'country_id' => $indiaId],
            ['name' => 'Uttar Pradesh', 'code' => 'UP', 'country_id' => $indiaId],
            ['name' => 'Uttarakhand', 'code' => 'UT', 'country_id' => $indiaId],
            ['name' => 'West Bengal', 'code' => 'WB', 'country_id' => $indiaId],
            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN', 'country_id' => $indiaId],
            ['name' => 'Chandigarh', 'code' => 'CH', 'country_id' => $indiaId],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DD', 'country_id' => $indiaId],
            ['name' => 'Lakshadweep', 'code' => 'LD', 'country_id' => $indiaId],
            ['name' => 'Delhi', 'code' => 'DL', 'country_id' => $indiaId],
            ['name' => 'Puducherry', 'code' => 'PY', 'country_id' => $indiaId],
            ['name' => 'Ladakh', 'code' => 'LA', 'country_id' => $indiaId],
            ['name' => 'Jammu and Kashmir', 'code' => 'JK', 'country_id' => $indiaId],
        ];

        foreach ($states as $state) {
            State::updateOrCreate(
                ['code' => $state['code']],
                $state
            );
        }
    }
}
