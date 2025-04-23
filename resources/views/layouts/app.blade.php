<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FHAFLORIST')</title>

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <!-- Tambahkan Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <style>
        /* Styling Sidebar */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        .sidebar {
            width: 250px;
            /* Lebar dikurangi */
            min-height: 100vh;
            background: #660d57;
            color: white;
            padding: 20px;
            /* Padding dikurangi */
            display: flex;
            flex-direction: column;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            color: white;
            font-size: 16px;
            font-weight: 500;
            padding: 10px;
            gap: 8px;
            border-radius: 5px;
            transition: background 0.3s ease-in-out;
            text-decoration: none;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.3);
            font-weight: bold;
            color: #fff;
        }

        .sidebar .nav-link i {
            font-size: 18px;
            /* Ikon lebih kecil */
        }

        .sidebar-title {
            font-size: 22px;
            /* Judul lebih kecil */
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .logout-btn {
            width: 100%;
            background: linear-gradient(90deg, #ff6b6b, #ff4d4d);
            border: none;
            font-weight: bold;
            transition: 0.3s;
            padding: 10px;
            border-radius: 5px;
            color: white;
            font-size: 16px;
        }

        .logout-btn:hover {
            background: linear-gradient(90deg, #ff4d4d, #ff1a1a);
        }

        .d-flex {
            display: flex;
            /* Memastikan sidebar dan konten sejajar */
        }

        .content {
            flex: 1;
            /* Konten mengisi sisa ruang di samping sidebar */
            padding: 20px;
        }

        .submenu {
            display: none;
            list-style: none;
            padding-left: 20px;
        }

        .submenu li {
            margin-top: 5px;
        }

        .show {
            display: block !important;
        }

    </style>
</head>

<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-title">
                <i class="fa-solid fa-seedling"></i> FHAFLORIST
            </div>
            <a class="nav-link {{ Request::is('dashboard*') ? 'active' : '' }}" href="{{ url('/dashboard') }}">
                <i class="fa-solid fa-chart-line"></i> Dashboard
            </a>

            @if (auth()->check())
                @if (auth()->user()->role == 'admin')
                    <a class="nav-link {{ Request::is('categories*') ? 'active' : '' }}"
                        href="{{ route('categories.index') }}">
                        <i class="fa-solid fa-tags"></i> Kategori
                    </a>
                    <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="fa-solid fa-box"></i> Produk
                    </a>
                    <a class="nav-link {{ Request::is('users*') ? 'active' : '' }}"
                        href="{{ route('users.index') }}">
                        <i class="fa-solid fa-user"></i> User
                    </a>
                @elseif (auth()->user()->role == 'gudang')
                    <a class="nav-link {{ Request::is('categories*') ? 'active' : '' }}"
                        href="{{ route('categories.index') }}">
                        <i class="fa-solid fa-tags"></i> Kategori
                    </a>
                    <a class="nav-link {{ Request::is('products*') ? 'active' : '' }}"
                        href="{{ route('products.index') }}">
                        <i class="fa-solid fa-box"></i> Produk
                    </a>
                @endif
            @endif

            @if (auth()->check() && auth()->user()->role != 'gudang')
                <a class="nav-link {{ Request::is('transactions/create') ? 'active' : '' }}"
                    href="{{ route('transactions.create') }}">
                    <i class="fa-solid fa-cash-register"></i> Transaksi
                </a>
                <div class="nav-item">
                    <a class="nav-link {{ request('status') ? 'active' : '' }}" href="#" id="transactionToggle">
                        <i class="fa-solid fa-history"></i> Riwayat Transaksi
                    </a>
                    <ul class="submenu {{ request('status') ? 'show' : '' }}" id="transactionMenu">
                        <li>
                            <a class="nav-link {{ request('status') == 'lunas' ? 'active' : '' }}" 
                            href="{{ route('transactions.index', ['status' => 'lunas']) }}">
                                <i class="fa-solid fa-check"></i> Lunas
                            </a>
                        </li>
                        <li>
                            <a class="nav-link {{ request('status') == 'dp' ? 'active' : '' }}" 
                            href="{{ route('transactions.index', ['status' => 'dp']) }}">
                                <i class="fa-solid fa-clock"></i> DP
                            </a>
                        </li>
                    </ul>
                </div>
                <a class="nav-link {{ Request::is('customers*') ? 'active' : '' }}"
                    href="{{ route('customers.index') }}">
                    <i class="fa-solid fa-users"></i> Customers
                </a>
                <a class="nav-link {{ Request::is('cart*') ? 'active' : '' }}"
                    href="{{ route('cart.index') }}">
                    <i class="fa-solid fa-shopping-cart"></i> Cart
                </a>
            @endif

            <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                @csrf
                <button type="submit" class="btn logout-btn"><i class="fa-solid fa-sign-out-alt"></i> Logout</button>
            </form>
        </nav>

        <!-- Main Content -->
        <div class="content container-fluid">
            @yield('content')
        </div>
    </div>
    <!-- Bootstrap & JavaScript -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById("transactionToggle").addEventListener("click", function(event) {
            event.preventDefault();
            document.getElementById("transactionMenu").classList.toggle("show");
        });
    </script>
</body>


</html>
