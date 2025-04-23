@extends('layouts.app')

@section('title', 'Tambah Kategori')

@section('content')
    <div class="container mt-4">
        <h2 class="mb-3 text-dark">Tambah Kategori</h2>
        <div class="form-container">
            <form action="{{ route('categories.store') }}" method="POST" id="category-form">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama Kategori</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                    <small class="error-text" id="name-error"></small>
                </div>
                <button type="submit" class="btn btn-custom">
                    <i class="fa-solid fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('category-form').addEventListener('submit', function(event) {
            let nameInput = document.getElementById('name');
            let nameError = document.getElementById('name-error');

            nameError.innerText = '';

            if (nameInput.value.trim() === '') {
                event.preventDefault();
                nameError.innerText = 'Nama kategori tidak boleh kosong!';
                nameInput.classList.add('input-error');
            } else {
                nameInput.classList.remove('input-error');
            }
        });

        document.getElementById('name').addEventListener('focus', function() {
            this.classList.add('input-focus');
        });

        document.getElementById('name').addEventListener('blur', function() {
            this.classList.remove('input-focus');
        });
    </script>

    <style>
        /* Warna Utama */
        :root {
            --main-color: #FCC6FF;
            --dark-color: #222222;
            --danger-color: #ff6b6b;
            --input-border: #ccc;
        }

        /* Form mepet kiri */
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            max-width: 100%;
            width: 400px;
        }

        /* Input Form */
        .form-control {
            border: 2px solid var(--input-border);
            border-radius: 5px;
            padding: 10px;
            transition: 0.3s;
        }

        .form-control:focus {
            border-color: var(--main-color);
            box-shadow: 0px 0px 8px rgba(252, 198, 255, 0.6);
        }

        /* Animasi Fokus Input */
        .input-focus {
            border-color: var(--main-color) !important;
            box-shadow: 0px 0px 8px rgba(252, 198, 255, 0.6) !important;
        }

        /* Error Input */
        .input-error {
            border-color: var(--danger-color) !important;
        }

        .error-text {
            color: var(--danger-color);
            font-size: 14px;
            margin-top: 5px;
            display: block;
        }

        /* Tombol Custom */
        .btn-custom {
            background-color: var(--main-color);
            border: none;
            color: black;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-custom:hover {
            background-color: #e6b3e6;
        }
    </style>
@endsection
