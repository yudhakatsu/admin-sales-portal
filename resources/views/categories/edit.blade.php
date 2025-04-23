@extends('layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="container">
        <h2>Edit Kategori</h2>
        <form action="{{ route('categories.update', $category->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Nama Kategori</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $category->name }}" required>
            </div>
            <button type="submit" class="btn btn-save">Update</button>
        </form>
    </div>
    <style>
        /* Edit Kategori */
        .edit-kategori-container {
            max-width: 500px;
            background: white;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .edit-kategori-container h2 {
            color: #222;
        }

        .btn-save {
            background-color: #FCC6FF;
            border: none;
            color: #222;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-save:hover {
            background-color: #e6b3e6;
        }
    </style>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputName = document.getElementById("name");
            const form = document.querySelector("form");

            // Animasi saat input difokuskan
            inputName.addEventListener("focus", function() {
                this.style.borderColor = "#FCC6FF";
                this.style.boxShadow = "0px 0px 8px rgba(252, 198, 255, 0.7)";
            });

            inputName.addEventListener("blur", function() {
                this.style.borderColor = "#ccc";
                this.style.boxShadow = "none";
            });

            // Validasi sebelum submit
            form.addEventListener("submit", function(event) {
                if (inputName.value.trim() === "") {
                    alert("Nama kategori tidak boleh kosong!");
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
