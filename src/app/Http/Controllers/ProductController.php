<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;


class ProductController extends Controller
{
    
    private $endpoint;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->endpoint = "http://makeup-api.herokuapp.com/api/v1/products.json";
    }

    public function search($type, $category)
    {
      
        $response = Http::get($this->endpoint,[
            'product_type' => $type,
            'product_category' => $category
       ]);


       $collection = $response->collect();

       $filtered = $collection->map(function ($item, $key) {
             $item =  collect($item);  
             $item->put('price_BRL', $this->convertCurrency($item['price'], $item['currency'], 'BRL'));
             return $item->only(['name','price','price_BRL','description']);
        });


        return $filtered->all();
       
    }


    private function convertCurrency($amount, $fromCurrency, $toCurrency)
    {
        if ($amount > 0) {

            $apiKey = env('API_KEY');

            $response = Http::get("https://api.fastforex.io/convert", [
                'from' => $fromCurrency,
                'to' => $toCurrency,
                'amount' => $amount,
                'api_key' =>$apiKey,
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);

            return $response->collect()['result'][$toCurrency];            
            
        }
        

        return 0.0;    

        
    }

}
