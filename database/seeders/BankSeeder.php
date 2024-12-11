<?php

namespace Database\Seeders;

use App\Models\Bank;
use App\Models\BankBranch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $supportedCountries = [
            'EG', 'ET', 'GH', 'KE', 'MW', 'NG', 'RW', 'SL', 'TZ', 'UG', 'US', 'ZA',
        ];

        foreach ($supportedCountries as $country) {

            $request1 = Http::acceptJson()
                ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
                ->get("https://api.flutterwave.com/v3/banks/$country");

            if ($request1->successful()) {
                $banks = $request1->json()['data'];

                foreach ($banks as $bank) {
                    $newBank = Bank::create([
                        'country' => $country,
                        'name' => $bank['name'],
                        'code' => $bank['code'],
                    ]);

                    $id = $bank['id'];

                    // get branches
                    $request2 = Http::acceptJson()
                        ->withToken(env('FLUTTERWAVE_CLIENT_SECRET'))
                        ->get("https://api.flutterwave.com/v3/banks/$id/branches");

                    if ($request2->successful()) {
                        $branches = $request2->json()['data'];

                        foreach ($branches as $branch) {
                            BankBranch::create([
                                'bank_id' => $newBank->id,
                                'name' => $branch['branch_name'],
                                'code' => $branch['branch_code'],
                                'swift_code' => $branch['swift_code'],
                                'bic' => $branch['bic'],
                            ]);
                        }
                    }
                }
            }
        }
    }
}
