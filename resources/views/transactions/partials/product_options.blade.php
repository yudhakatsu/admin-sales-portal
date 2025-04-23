<label for="category" class="form-label">Pilih Kategori</label>
<select class="form-control category-select">
    <option value="">-- Pilih Kategori --</option>
    @foreach ($categories as $category)
        <option value="{{ $category->id }}" 
            {{ $category->id == old('category', $selectedCategory ?? '') ? 'selected' : '' }}>
            {{ $category->name }}
        </option>
    @endforeach
</select>

<label for="product" class="form-label mt-2">Pilih Produk</label>
<select name="products[0][id]" class="form-control product-select">
    <option value="">-- Pilih Produk --</option>
    @foreach ($products as $product)
        <option value="{{ $product->id }}" 
            {{ $product->id == old('product', $selectedProduct ?? '') ? 'selected' : '' }}>
            {{ $product->name }} - Rp{{ number_format($product->price, 2) }}
        </option>
    @endforeach
</select>