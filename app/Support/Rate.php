<?php

namespace App\Support;

use App\Models\Currency;
use Cache;
use Exception;
use Illuminate\Support\Facades\Http;

class Rate
{

  
    /**
     * get rate from btcrate
     */
    public static function btcToUsd($btcRate, $scale = 8)
    {
        // Ensure BCMath extension is loaded
        if (!extension_loaded('bcmath')) {
            throw new Exception('BCMath extension is not loaded');
        }
        $bitcoin = Cache::remember('rates/bitcoin', 60, fn() => static::api('rates/bitcoin'));
        $btcUSDRate = $bitcoin['rateUsd'];
        // Convert the scientific notation to a decimal string if necessary
        $tokenBTCRate = number_format($btcRate, 30, '.', '');
        // Multiply token-BTC rate by BTC-USD rate
        $tokenUSDRate = bcmul($tokenBTCRate, $btcUSDRate, $scale + 10);
        // Round to the desired number of decimal places
        return bcmul($tokenUSDRate, '1', $scale);
    }


    public static function api($path)
    {

        $token = settings('site.coincap_apikey', '55014acc-82b7-4970-9ca1-c042809054da');
        $response = Http::withToken($token)->get("https://api.coincap.io/v2/$path");
        if (!$response->successful()) {
            throw new \Exception("Failed to fetch api for $path");
        }
        return $response->json('data', []);
    }

    public static function  symbols()
    {
        return Cache::remember('--rates--symbols--', 60 * 60, function () {
            $assets =  [];
            foreach (static::api('assets') as $asset) {
                $assets[$asset['symbol']] = floatval($asset['priceUsd']);
            }
            foreach (static::api('rates') as $asset) {
                $assets[$asset['symbol']] = floatval($asset['rateUsd']);
            }
            return $assets;
        });
    }

    public static function update()
    {
        $siteCurrency = settings('site.currency_code', 'USD');
        $assets = collect(static::api('assets'))->flatMap(function ($asset) {
            return [$asset['symbol'] => floatval($asset['priceUsd'])];
        });
        $rates = collect(static::api('rates'))->flatMap(function ($asset) {
            return [$asset['symbol'] => floatval($asset['rateUsd'])];
        });
        Currency::pluck('symbol')
            ->unique()
            ->each(function ($symbol) use ($assets, $rates, $siteCurrency) {
                $rate = $assets[$symbol] ?? $rates[$symbol] ?? null;
                if (!$rate) return;
                if ($siteCurrency != 'USD') {
                    $conversionRate = $rates[$siteCurrency];
                    if (!$conversionRate) return;
                    $rate *= $conversionRate;
                }
                Currency::where('symbol', $symbol)->update(['rate' => $rate]);
            });
    }
}
