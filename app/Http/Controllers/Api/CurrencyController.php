<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;
use App\Http\Resources\CurrencyCollection;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $currencies = new CurrencyCollection(Currency::all());

        return response()->json($currencies);
    }
    /**
     * Display the specified resource.
     *
     * @param  string  $code
     * @return \Illuminate\Http\Response
     */
    public function show($code)
    {
        $currency = Currency::where('name', $code)->first();

        return response()->json([
            'name' => $currency->name,
            'rate' => $currency->rate
        ]);
    }
}
