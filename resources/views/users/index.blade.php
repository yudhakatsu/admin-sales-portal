@extends('layouts.app')

@section('title', 'Daftar User')

@section('content')
    <div class="container mt-4">
        <div class="user-container">
            <h2 class="text-center mb-4">Daftar User</h2>

            <div class="text-end mb-3">
                <a href="{{ route('users.create') }}" class="btn btn-add">Tambah User</a>
            </div>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ ucfirst($user->role) }}</td>
                            <td class="text-center">
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                    class="d-inline delete-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-delete" onclick="confirmDelete(this)">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

        </div>
    </div>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(button) {
            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "User yang dihapus tidak bisa dikembalikan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('form').submit();
                }
            });
        }
    </script>

    <style>
        /* Container */
        .user-container {
            max-width: 800px;
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin: auto;
            box-shadow: 2px 4px 10px rgba(0, 0, 0, 0.1);
        }

        .user-container h2 {
            color: #222;
            font-weight: bold;
        }

        /* Button */
        .btn-add {
            background-color: #FCC6FF;
            border: none;
            color: #222;
            font-weight: bold;
            padding: 10px 15px;
            border-radius: 5px;
            transition: 0.3s;
            text-decoration: none;
        }

        .btn-add:hover {
            background-color: #e6b3e6;
        }

        /* Table */
        .table th {
            background-color: #FCC6FF;
            color: #222;
        }

        .table td {
            vertical-align: middle;
        }

        /* Delete Button */
        .btn-delete {
            background-color: #FF6B6B;
            border: none;
            color: white;
            font-weight: bold;
            padding: 6px 10px;
            border-radius: 5px;
            transition: 0.3s;
        }

        .btn-delete:hover {
            background-color: #E04E4E;
        }
    </style>
@endsection
