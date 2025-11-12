<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController
{
    public function index()
    {
        $response = Http::get('http://127.0.0.1:8000/api/products'); // System A URL
        $products = $response->json();

        return Inertia::render('Products', [
            'products' => $products,
        ]);
    }
}
