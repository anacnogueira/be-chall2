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


        return $this->filteredCollection($response->collect())->all();
       
    }


    public function brands($brand)
    {
        $response = Http::get($this->endpoint,[
            'brand' => $brand
       ]);


        $collection =  $response->collect();

        $brands = [
           'cheapest' =>  $this->filteredCollection($collection->where('price', $collection->min('price'))),
           'mostExpensive' =>  $this->filteredCollection($collection->where('price', $collection->max('price')))    


        ];
       

        return $brands;
    }


    private function filteredCollection($collection) 
    {
        return $collection->map(function ($item, $key) {
            $item =  collect($item);  
            $item->put('price_BRL', $this->convertCurrency($item['price'], $item['currency'], 'BRL'));
            return $item->only(['name','price','price_BRL','description']);
        });
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
