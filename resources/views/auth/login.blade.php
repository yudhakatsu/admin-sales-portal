<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body {
            background: linear-gradient(to right, #FCC6FF, #f3e5ff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }

        .login-container label {
            display: block;
            text-align: left;
            margin-left: 40px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #7a00cc;
        }

        .login-container h2 {
            color: #7a00cc;
            font-weight: bold;
        }

        .form-control {
            border-radius: 10px;
            border: 1px solid #c084fc;
            width: 75%;
            /* Mengecilkan ukuran input */
            padding: 10px;
            margin: 10px auto;
            /* Agar tetap rata tengah */
            display: block;
        }

        .btn-primary {
            background-color: #9b5de5;
            border: none;
            width: 80%;
            /* Samakan ukuran dengan input */
            border-radius: 10px;
            padding: 10px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s;
            display: block;
            margin-top: 20px;
            margin: 10px auto;
        }

        .btn-primary:hover {
            background-color: #7a00cc;
        }

        .logo {
            width: 150px;
            /* Perbesar logo */
            height: auto;
            /* Jaga proporsi */
            margin-bottom: 20px;
            /* Beri jarak dengan elemen di bawahnya */
            margin-top: 10px;
            /* Geser sedikit ke bawah */
        }
    </style>
</head>

<body>
    <div class="login-container">
        <img src="/image/Logo.jpeg" alt="Logo" class="logo">
        <h2>Login</h2>
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
            <button type="submit" class="btn-primary">Login</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inputs = document.querySelectorAll(".form-control");
            inputs.forEach(input => {
                input.addEventListener("focus", function() {
                    this.style.borderColor = "#7a00cc";
                    this.style.boxShadow = "0px 0px 8px rgba(122, 0, 204, 0.3)";
                });
                input.addEventListener("blur", function() {
                    this.style.borderColor = "#c084fc";
                    this.style.boxShadow = "none";
                });
            });
        });
    </script>
</body>

</html>
