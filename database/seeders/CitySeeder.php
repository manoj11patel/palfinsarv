<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $stateMap = State::pluck('id', 'code')->toArray();

        $cities = [
            'AP' => ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Tirupati'],
            'AR' => ['Itanagar', 'Naharlagun', 'Tawang', 'Ziro'],
            'AS' => ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon', 'Tinsukia'],
            'BR' => ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Darbhanga', 'Purnia'],
            'CT' => ['Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Durg', 'Rajnandgaon'],
            'GA' => ['Panaji', 'Vasco da Gama', 'Margao', 'Mapusa', 'Ponda'],
            'GJ' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Gandhinagar', 'Jamnagar', 'Anand'],
            'HR' => ['Gurugram', 'Faridabad', 'Panipat', 'Ambala', 'Karnal', 'Hisar', 'Rohtak', 'Sonipat'],
            'HP' => ['Shimla', 'Dharamsala', 'Manali', 'Solan', 'Mandi', 'Kullu', 'Baddi'],
            'JH' => ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Hazaribagh', 'Deoghar'],
            'KA' => ['Bengaluru', 'Mysuru', 'Hubballi', 'Mangaluru', 'Belagavi', 'Davangere', 'Ballari', 'Tumakuru'],
            'KL' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam', 'Palakkad', 'Alappuzha'],
            'MP' => ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain', 'Rewa', 'Sagar', 'Dewas'],
            'MH' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Aurangabad', 'Solapur', 'Thane', 'Navi Mumbai', 'Kolhapur', 'Amravati'],
            'MN' => ['Imphal', 'Churachandpur', 'Thoubal', 'Bishnupur'],
            'ML' => ['Shillong', 'Tura', 'Jowai', 'Nongstoin'],
            'MZ' => ['Aizawl', 'Lunglei', 'Champhai', 'Serchhip'],
            'NL' => ['Kohima', 'Dimapur', 'Mokokchung', 'Tuensang'],
            'OD' => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur', 'Puri', 'Balasore'],
            'PB' => ['Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali', 'Hoshiarpur'],
            'RJ' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Ajmer', 'Bikaner', 'Alwar', 'Bhilwara'],
            'SK' => ['Gangtok', 'Namchi', 'Gyalshing', 'Mangan'],
            'TN' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli', 'Erode', 'Vellore'],
            'TG' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Khammam', 'Ramagundam'],
            'TR' => ['Agartala', 'Dharmanagar', 'Udaipur', 'Kailasahar'],
            'UP' => ['Lucknow', 'Kanpur', 'Agra', 'Varanasi', 'Prayagraj', 'Meerut', 'Ghaziabad', 'Noida', 'Mathura', 'Bareilly'],
            'UT' => ['Dehradun', 'Haridwar', 'Rishikesh', 'Nainital', 'Roorkee', 'Haldwani', 'Mussoorie'],
            'WB' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri', 'Darjeeling', 'Kharagpur'],
            'AN' => ['Port Blair', 'Diglipur', 'Car Nicobar'],
            'CH' => ['Chandigarh'],
            'DD' => ['Silvassa', 'Daman', 'Diu'],
            'LD' => ['Kavaratti', 'Agatti', 'Amini'],
            'DL' => ['New Delhi', 'Dwarka', 'Rohini', 'Janakpuri', 'Laxmi Nagar', 'Saket'],
            'PY' => ['Puducherry', 'Karaikal', 'Mahe', 'Yanam'],
            'LA' => ['Leh', 'Kargil', 'Diskit'],
            'JK' => ['Jammu', 'Srinagar', 'Anantnag', 'Baramulla', 'Sopore'],
        ];

        foreach ($cities as $stateCode => $cityNames) {
            if (!isset($stateMap[$stateCode])) {
                continue;
            }
            $stateId = $stateMap[$stateCode];
            foreach ($cityNames as $cityName) {
                City::updateOrCreate(
                    ['name' => $cityName, 'state_id' => $stateId],
                    ['name' => $cityName, 'state_id' => $stateId]
                );
            }
        }
    }
}
