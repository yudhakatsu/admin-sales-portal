<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\CartDetail;
use App\Models\Product;
use App\Models\Customer;


class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cart = Cart::with(['details.product', 'customer'])->get();
        return view('cart.index', compact('cart'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Cart $cart)
    {
        //
        $categories = Category::all();
        $products = Product::all();

        $cart = Cart::with(['details.product', 'customer'])->findOrFail($cart->id);
        return view('cart.create', compact('cart', 'categories', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'required',
            'customer_address' => 'required',
            'pickup_date' => 'required|date',
            'payment_status' => 'required|in:lunas,dp,bayar nanti',
            'payment_method' => 'required|in:cash,transfer',
            'dp_amount' => 'nullable|numeric|min:0',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $customer = Customer::firstOrCreate(
            ['phone_number' => $request->phone_number], // Kondisi pencarian
            [
                'name' => $request->customer_name,
                'phone_number' => $request->phone_number,
                'address' => $request->customer_address,
            ]
        );

        $totalPrice = 0;
        $totalQuantity = 0;
        
        // Simpan ke tabel cart
        $cart = Cart::create([
            'user_id' => Auth::id(),
            'customer_id' => $customer->id,
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->customer_address,
            'pickup_date' => $request->pickup_date,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'dp_amount' => $request->payment_status === 'dp' ? $request->dp_amount : 0,
            'total' => 0,
            'quantity' => 0,
            'note' => $request->note,
        ]);

        foreach ($request->products as $product) {
            $productData = Product::findOrFail($product['id']);
            $subtotal = $productData->price * $product['quantity'];
            $totalPrice += $subtotal;
            $totalQuantity += $product['quantity'];

            $productData->decrement('stock', $product['quantity']);

            CartDetail::create([
                'cart_id' => $cart->id,
                'product_id' => $productData->id,
                'quantity' => $product['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        // Update total harga dan total kuantitas di cart
        $cart->update([
            'total' => $totalPrice,
            'quantity' => $totalQuantity,
        ]);

        return redirect()->route('cart.index')->with('success', 'Cart berhasil diperbarui');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Cart $cart)
    {
        //
        $categories = Category::all();
        $products = Product::all();

        $cart = Cart::with(['details.product', 'customer'])->findOrFail($cart->id);
        return view('cart.edit', compact('cart', 'categories', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Cart $cart)
    {
        //

        // dd($request, $cart);
        $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'required|string|max:15',
            'customer_address' => 'required|string',
            'pickup_date' => 'required|date',
            'payment_status' => 'required|in:lunas,dp,bayar nanti',
            'payment_method' => 'required|in:cash,transfer',
            'dp_amount' => 'nullable|numeric|min:0',
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
        ]);

        $totalPrice = 0;
        $totalQuantity = 0;

        $cart->update([
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->customer_address,
            'pickup_date' => $request->pickup_date,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'dp_amount' => $request->payment_status === 'dp' ? $request->dp_amount : 0,
            'note' => $request->note,
        ]);

        $cart->details()->delete();

        foreach ($request->products as $product) {
            $productData = Product::findOrFail($product['id']);
            $subtotal = $productData->price * $product['quantity'];
            $totalPrice += $subtotal;
            $totalQuantity += $product['quantity'];

            CartDetail::create([
                'cart_id' => $cart->id,
                'product_id' => $productData->id,
                'quantity' => $product['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        $cart->update([
            'total' => $totalPrice,
            'quantity' => $totalQuantity,
        ]);


        return redirect()->route('cart.index')->with('success', 'Data berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cart = Cart::with(['details.product', 'customer'])->findOrFail($id);

        // Kembalikan stok produk
        foreach ($cart->details as $detail) {
            $product = $detail->product;
            if ($product) {
                $product->stock += $detail->quantity;
                $product->save();
            }
        }

        // Hapus detail cart duluan (biar tidak orphaned)
        $cart->details()->delete();

        // Hapus cart
        $cart->delete();

        return redirect()->route('cart.index')->with('success', 'Keranjang berhasil dihapus dan stok dikembalikan');
    }

    public function checkout(Cart $cart)
    {
        $categories = Category::all();
        $products = Product::all();

        $cart = Cart::with(['details.product', 'customer'])->findOrFail($cart->id);

        return view('cart.create', compact('cart', 'categories', 'products'));
    }

}
