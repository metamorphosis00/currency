<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CurrencyUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'currency:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get('http://www.cbr.ru/scripts/XML_daily.asp');
        if ($response->failed()) {
            $this->error('Request to external api failed!');
            return 0;
        }

        $xml  = simplexml_load_string($response->body());
        $json = json_encode($xml);
        $data = json_decode($json, true);

        $bar = $this->output->createProgressBar(count($data));

        $bar->start();

        foreach ($data['Valute'] as $item) {
            $name  = $item['CharCode'];
            $value = $item['Value'];
            $value = str_replace(',', '.', $value);
            $rate  = floatval($value);

            DB::table('currencies')->upsert([
                'name' => $name,
                'rate' => $rate
            ], ['name'], ['rate']);

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nCurrency data successfully updated!");

        return 1;
    }
}
