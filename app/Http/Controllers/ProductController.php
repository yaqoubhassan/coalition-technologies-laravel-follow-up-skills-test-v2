<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    private $filePath = 'products.json';

    public function index()
    {
        $data = File::exists(storage_path($this->filePath)) ? json_decode(File::get(storage_path($this->filePath)), true) : [];

        return view('welcome', compact('data'));
    }

    public function store(Request $request)
    {
        $products = File::exists(storage_path($this->filePath)) ? json_decode(File::get(storage_path($this->filePath)), true) : [];

        $product = [
            'product_name' => $request->input('product_name'),
            'quantity_in_stock' => (int)$request->input('quantity_in_stock'),
            'price_per_item' => (float)$request->input('price_per_item'),
            'date_submitted' => now()->toDateTimeString()
        ];

        $product['total_value'] = $product['quantity_in_stock'] * $product['price_per_item'];
        $products[] = $product;

        File::put(storage_path($this->filePath), json_encode($products, JSON_PRETTY_PRINT));

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    public function update(Request $request)
    {
        $products = json_decode(File::get(storage_path($this->filePath)), true);

        $index = $request->input('index');

        $products[$index] = [
            'product_name' => $request->input('product_name'),
            'quantity_in_stock' => (int)$request->input(('quantity_in_stock')),
            'price_per_item' => (float)$request->input('price_per_item'),
            'date_submitted' => $products[$index]['date_submitted'],
            'total_value' => (int)$request->input('quantity_in_stock') * (float)$request->input('price_per_item')
        ];

        File::put(storage_path($this->filePath), json_encode($products, JSON_PRETTY_PRINT));

        return response()->json(['success' => true, 'data' => $products]);
    }
}
