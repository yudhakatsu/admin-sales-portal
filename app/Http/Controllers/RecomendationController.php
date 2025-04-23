<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class RecomendationController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan kategori (jika ada)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan minimal dan maksimal budget
        if ($request->filled('budget_min') && $request->filled('budget_max')) {
            $query->whereBetween('price', [$request->budget_min, $request->budget_max]);
        } elseif ($request->filled('budget_min')) {
            $query->where('price', '>=', $request->budget_min);
        } elseif ($request->filled('budget_max')) {
            $query->where('price', '<=', $request->budget_max);
        }

        // Ambil semua produk jika tidak ada filter yang dipilih
        $products = $query->get();
        $categories = Category::all();
        
        return view('recomendations.index', compact('categories', 'products')); 
    }

    public function filter(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan kategori (jika ada)
        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        // Filter berdasarkan budget (jika ada)
        if ($request->filled('budget_min') && $request->filled('budget_max')) {
            $query->whereBetween('price', [$request->budget_min, $request->budget_max]);
        } elseif ($request->filled('budget_min')) {
            $query->where('price', '>=', $request->budget_min);
        } elseif ($request->filled('budget_max')) {
            $query->where('price', '<=', $request->budget_max);
        }

        $products = $query->get();
        return response()->json($products);
    }


}
