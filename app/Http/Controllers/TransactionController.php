<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Customer;
use App\Models\TransactionDetail;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');
        if(!in_array($status, ['lunas', 'dp', null])) {
            return redirect()->route('transactions.index');
        }

        $transactions = Transaction::with(['details.product', 'customer'])
                                    ->when($status, function($query, $status){
                                        return $query->where('payment_status', $status);
                                    })
                                    ->orderBy('order_date', 'asc')
                                    ->get();
        return view('transactions.index', compact('transactions'));
    }      

    public function create(Request $request)
    {
        $categories = Category::all();
        
        $inputBudget = $request->input('budget_max');
        // $budgetMax = $inputBudget ? $inputBudget + 5000 : null;

        // $products = Product::when($budgetMax, function($query) use ($budgetMax) {
        //     return $query->where('price', '<=', $budgetMax);
        // })->get();

        $budgetRange = 10000;
        $products = Product::when($inputBudget, function($query) use ($inputBudget, $budgetRange) {
            $min = $inputBudget - $budgetRange;
            $max = $inputBudget + $budgetRange;
            return $query->whereBetween('price', [$min, $max])
                        ->orderByRaw('ABS(price - ?)', [$inputBudget]);
        })->get();

        $selectedCategory = $request->category;
        $selectedProduct = $request->product;

        // Ambil stok berdasarkan product_id
        $selectedStock = Product::where('id', $selectedProduct)->value('stock');

        return view('transactions.create', [
            'categories' => $categories,
            'products' => $products,
            'selectedCategory' => $selectedCategory,
            'selectedProduct' => $selectedProduct,
            'selectedStock' => $selectedStock,
            'budgetMax' => $inputBudget, // Tetap kirim nilai asli ke view
        ]);
    }

    public function getProductsByBudget(Request $request)
    {
        $budgetMax = $request->input('budget_max');

        $products = Product::when($budgetMax, function ($query) use ($budgetMax) {
            return $query->where('price', '<=', $budgetMax);
        })->get();

        // Bangun HTML option langsung
        $html = '<option value="">-- Pilih Produk --</option>';
        foreach ($products as $product) {
            $html .= '<option value="' . $product->id . '">' .
                    $product->name . ' - Rp' . number_format($product->price, 2) .
                    '</option>';
        }

        return response($html);
    }

    public function store(Request $request)
    {

        $request->validate([
            'customer_name' => 'required',
            'phone_number' => 'required',
            'customer_address' => 'required',
            'order_date' => 'required|date',
            'pickup_date' => 'required|date|after_or_equal:order_date',
            'payment_status' => 'required|in:lunas,dp,bayar nanti',
            'payment_method' => 'required|in:cash,transfer',
            'payment' => 'nullable|numeric|min:0',
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
        
        // Simpan transaksi utama di tabel `transactions`
        $transaction = Transaction::create([
            'user_id' => Auth::id(),
            'customer_id' => $customer->id, // Simpan id customer
            'customer_name' => $request->customer_name,
            'phone_number' => $request->phone_number,
            'address' => $request->customer_address,
            'order_date' => $request->order_date,
            'pickup_date' => $request->pickup_date,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'payment' => $request->payment_method ==='cash' ? $request->payment : 0,
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

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productData->id,
                'quantity' => $product['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        $change = 0;
        $payment = $request->payment_method === 'transfer' ? $totalPrice : $request->payment;
        $change = $request->payment_status === 'dp' ? $payment - $request->dp_amount : $payment - $totalPrice ;

        // Update total harga dan total kuantitas di `transactions`
        $transaction->update([
            'total' => $totalPrice,
            'payment' => $payment,
            'change' => $change,
            'quantity' => $totalQuantity,
        ]);

        // Hapus cart setelah transaksi berhasil
        if ($request->has('cart_id')) {
            $cart = Cart::with('details.product')->find($request->cart_id);
        
            // Naikkan stok produk sebelum menghapus cart
            foreach ($cart->details as $detail) {
                $product = $detail->product;
                if ($product) {
                    $product->increment('stock', $detail->quantity);
                }
            }
        
            $cart->details()->delete();
            $cart->delete();
        }        

        return redirect()->route('transactions.index', ['status' => $transaction->payment_status])
                         ->with('success', 'Transaksi berhasil disimpan');
    }

    public function edit(Transaction $transaction)
    {
        $categories = Category::all();
        $products = Product::all();

        // Tambahkan with('details.product') agar data relasi diambil
        $transaction = Transaction::with(['details.product', 'customer',])->findOrFail($transaction->id);

        return view('transactions.edit', compact('transaction', 'categories', 'products'));
    }


    public function update(Request $request, Transaction $transaction)
    {

        $request->validate([
            'customer_name' => 'required',
            'order_date' => 'required|date',
            'pickup_date' => 'required|date|after_or_equal:order_date',
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

        $transaction->update([
            'customer_name' => $request->customer_name,
            'order_date' => $request->order_date,
            'pickup_date' => $request->pickup_date,
            'payment_status' => $request->payment_status,
            'payment_method' => $request->payment_method,
            'dp_amount' => $request->payment_status === 'dp' ? $request->dp_amount : 0,
            'note' => $request->note,
        ]);

        $transaction->details()->delete();

        foreach ($request->products as $product) {
            $productData = Product::findOrFail($product['id']);
            $subtotal = $productData->price * $product['quantity'];
            $totalPrice += $subtotal;
            $totalQuantity += $product['quantity'];

            TransactionDetail::create([
                'transaction_id' => $transaction->id,
                'product_id' => $productData->id,
                'quantity' => $product['quantity'],
                'subtotal' => $subtotal,
            ]);
        }

        $transaction->update([
            'total' => $totalPrice,
            'quantity' => $totalQuantity,
        ]);

        return redirect()->route('transactions.index', ['status' => $transaction->payment_status])->with('success', 'Transaksi berhasil diperbarui');
    }


    public function updatePaymentStatus(Request $request,$id)
    {
        $transaction = Transaction::findOrFail($id);
        
        if ($transaction->payment_status == 'dp' || $transaction->payment_status == 'bayar nanti') {
            $transaction->payment_status = 'lunas';

            $transaction->payment = $request->input('payment');
            $change = $request->payment - ($transaction->total - $transaction->dp_amount);

            $transaction->change = $change >= 0 ? $change : 0;
        }
        
        $transaction->save();
        return redirect()->route('transactions.index', ['status' => $transaction->payment_status])->with('success', 'Status pembayaran diperbarui!');
    }
    
    public function destroy(Transaction $transaction)
    {
        try {
            $transaction->details()->delete();
            $transaction->delete();
            return redirect()->route('transactions.index', ['status' => $transaction->payment_status])->with('success', 'Transaksi berhasil dihapus');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    public function searchCustomers(Request $request)
    {
        $query = $request->input('name');
        
        // Mencari customer berdasarkan nama
        $customers = Customer::where('name', 'like', '%' . $query . '%')
                         ->orWhere('phone_number', 'like', '%' . $query . '%')
                         ->get(['name', 'phone_number', 'address']);

        return response()->json($customers);
    }

    public function print($id)
    {
        $transaction = Transaction::with(['details.product', 'customer'])->findOrFail($id);

        $pdf = PDF::loadView('transactions.pdf', compact('transaction'));
        return $pdf->stream($transaction->id . '-' . $transaction->customer->name . '-' . $transaction->order_date . '.pdf');
    }

    public function downloadPdfAndOpenWa($id)
    {
        $transaction = Transaction::with(['details.product', 'customer'])->findOrFail($id);
        $pdf = PDF::loadView('transactions.pdf', compact('transaction'));

        return $pdf->download( $transaction->id . '-' . $transaction->customer->name . '-' . $transaction->order_date . '.pdf');
    }

}
