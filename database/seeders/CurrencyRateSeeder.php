<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurrencyRate;
use Illuminate\Support\Facades\DB;

class CurrencyRateSeeder extends Seeder
{
    public function run(): void
    {
        // $currencyRates = [
        //     ['from_currency' => 'USD', 'to_currency' => 'USD', 'rate' => 1],
        //     ['from_currency' => 'GBP', 'to_currency' => 'GBP', 'rate' => 1],
        //     ['from_currency' => 'NGN', 'to_currency' => 'NGN', 'rate' => 1],
        //     ['from_currency' => 'CAD', 'to_currency' => 'CAD', 'rate' => 1],
        //     ['from_currency' => 'USD', 'to_currency' => 'NGN', 'rate' => 1600],
        //     ['from_currency' => 'USD', 'to_currency' => 'EUR', 'rate' => 0.92],
        //     ['from_currency' => 'EUR', 'to_currency' => 'GBP', 'rate' => 0.85],
        //     ['from_currency' => 'GBP', 'to_currency' => 'NGN', 'rate' => 1350.50],
        //     ['from_currency' => 'NGN', 'to_currency' => 'JPY', 'rate' => 0.30],
        //     ['from_currency' => 'CAD', 'to_currency' => 'AUD', 'rate' => 1.10],
        // ];

        // CurrencyRate::upsert($currencyRates, ['from_currency', 'to_currency'], ['rate']);
    }
}
