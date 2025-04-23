<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Menampilkan daftar semua kategori.
     * Method ini mengambil semua kategori dari database dan mengirimnya ke view `categories.index`.
     */
    public function index()
    {
        $categories = Category::all(); // Ambil semua data kategori
        return view('categories.index', compact('categories')); // Kirim ke view
    }

    /**
     * Menampilkan halaman form untuk menambah kategori baru.
     */
    public function create()
    {
        return view('categories.create'); // Tampilkan form create
    }

    /**
     * Menyimpan data kategori baru ke dalam database.
     * - Validasi input agar 'name' wajib diisi.
     * - Simpan data kategori ke database.
     * - Redirect kembali ke daftar kategori setelah berhasil disimpan.
     */
    public function store(Request $request)
    {
        $request->validate(['name' => 'required']); // Validasi input
        Category::create($request->all()); // Simpan ke database
        return redirect()->route('categories.index'); // Redirect ke halaman daftar kategori
    }

    /**
     * Menampilkan halaman edit kategori berdasarkan ID kategori yang dipilih.
     */
    public function edit(Category $category)
    {
        return view('categories.edit', compact('category')); // Kirim data kategori ke form edit
    }

    /**
     * Memperbarui data kategori yang ada di database.
     * - Validasi input.
     * - Update data kategori berdasarkan input dari user.
     * - Redirect kembali ke daftar kategori setelah update berhasil.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate(['name' => 'required']); // Validasi input
        $category->update($request->all()); // Update data di database
        return redirect()->route('categories.index'); // Redirect ke halaman daftar kategori
    }

    /**
     * Menghapus kategori berdasarkan ID kategori yang dipilih.
     * - Menghapus kategori dari database.
     * - Redirect kembali ke daftar kategori setelah berhasil dihapus.
     */
    public function destroy(Category $category)
    {
        $category->delete(); // Hapus data dari database
        return redirect()->route('categories.index'); // Redirect ke halaman daftar kategori
    }
}
