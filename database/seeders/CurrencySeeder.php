<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class CurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = Http::get('http://www.cbr.ru/scripts/XML_daily.asp');
        $xml = simplexml_load_string($response->body());
        $json = json_encode($xml);
        $data = json_decode($json, true);

        foreach ($data['Valute'] as $item) {
            $name = $item['CharCode'];
            $value = $item['Value'];
            $value = str_replace(',', '.', $value);
            $rate = floatval($value);
            DB::table('currencies')->insert([
                'name' => $name,
                'rate' => $rate
            ]);
        }
    }
}
