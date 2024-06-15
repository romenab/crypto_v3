<?php
//
//namespace CryptoTrade\App\Api;
//
//use CryptoTrade\App\Currency;
//
//class CoinGecko implements CryptoApi
//{
//    public function getResponse(): array
//    {
//        $curl = curl_init();
//        curl_setopt_array($curl, [
//            CURLOPT_URL => "https://pro-api.coingecko.com/api/v3/coins/markets",
//            CURLOPT_RETURNTRANSFER => true,
//            CURLOPT_ENCODING => "",
//            CURLOPT_MAXREDIRS => 10,
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//            CURLOPT_CUSTOMREQUEST => "GET",
//            CURLOPT_HTTPHEADER => ["accept: application/json"],
//            ]);
//        $response = curl_exec($curl);
//        $err = curl_error($curl);
//        $data = json_decode($response);
//        $currencies = [];
//        foreach ($data as $coin) {
//            $currencies[] = new Currency(
//                $coin['name'],
//                $coin['symbol'],
//                $coin['current_price'],
//                $coin['price_change_percentage_1h_in_currency'],
//                $coin['price_change_percentage_24h_in_currency'],
//                $coin['price_change_percentage_7d_in_currency'],
//                $coin['market_cap']
//            );
//        }
//        curl_close($curl);
//        if ($err) {
//            echo "cURL Error #:" . $err;
//        }
//        return $currencies;
//    }
//}