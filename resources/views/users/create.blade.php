@extends('layouts.app')

@section('title', 'Tambah User')

@section('content')
    <div class="container mt-4">
        <div class="tambah-user-container">
            <h2 class="text-center mb-4">Tambah User</h2>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="name" class="form-label">Nama</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label">Role</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="admin">Admin</option>
                        <option value="kasir">Kasir</option>
                        <option value="gudang">Gudang</option>
                    </select>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-submit">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <style>
        /* Container Styling */
        .tambah-user-container {
            max-width: 600px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin: auto;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .tambah-user-container h2 {
            color: #222;
            font-weight: bold;
        }

        /* Button */
        .btn-submit {
            background-color: #FCC6FF;
            border: none;
            color: #222;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-submit:hover {
            background-color: #e6b3e6;
        }
    </style>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll("input, select");
            const form = document.querySelector("form");

            // Animasi saat input difokuskan
            inputs.forEach(input => {
                input.addEventListener("focus", function() {
                    this.style.borderColor = "#FCC6FF";
                    this.style.boxShadow = "0px 0px 8px rgba(252, 198, 255, 0.7)";
                });

                input.addEventListener("blur", function() {
                    this.style.borderColor = "#ccc";
                    this.style.boxShadow = "none";
                });
            });

            // Konfirmasi sebelum submit
            form.addEventListener("submit", function(event) {
                if (!confirm("Apakah Anda yakin ingin menambahkan user ini?")) {
                    event.preventDefault();
                }
            });
        });
    </script>
@endsection
