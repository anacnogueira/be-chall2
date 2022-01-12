<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\Transaction;


class ProductController extends Controller
{
    
    private $endpoint;
    private $transaction;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->endpoint = "http://makeup-api.herokuapp.com/api/v1/products.json";
        $this->transaction = $transaction;
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

    public function buy(Request $request)
    {

        $transaction = $this->transaction->create([
             $request->all();
        ]);
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
