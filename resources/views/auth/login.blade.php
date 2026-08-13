<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Hasan Tour </title>

    <!-- FAVICON -->
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #425678;
            --card-bg: #506487;
            --input-bg: #e9eeee;
        }

        body {
            margin: 0;
            padding: 0;
            background: var(--primary);
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        /* CARD LOGIN */
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 42px 36px;
            border-radius: 28px;
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.28);
            text-align: center;
            animation: fadeIn .5s ease;
        }

        /* LOGO */
        .logo-box img {
            width: 180px;
            max-height: 180px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .title {
            color: #fff;
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .subtitle {
            color: #d3dcd8;
            font-size: 15px;
            margin-bottom: 28px;
        }

        /* INPUT */
        .input-group {
            position: relative;
            margin-bottom: 18px;
        }

        .form-control {
            width: 100%;
            height: 56px;
            border-radius: 14px;
            border: none;
            background: var(--input-bg);
            padding: 0 18px;
            font-size: 15px;
            outline: none;
            box-sizing: border-box;
        }

        /* PASSWORD TOGGLE */
        .toggle-pass {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #555;
            cursor: pointer;
        }

        /* BUTTON */
        .btn-login {
            width: 100%;
            height: 56px;
            border-radius: 14px;
            border: none;
            background: #ffffff;
            font-weight: 700;
            font-size: 17px;
            cursor: pointer;
            color: var(--primary);
            transition: .2s;
        }

        .btn-login:hover {
            background: #f3f3f3;
        }

        /* FOOTER */
        .login-footer {
            margin-top: 26px;
            font-size: 13px;
            color: #cdd7d3;
            opacity: 0.85;
        }

        .login-footer span {
            font-weight: 600;
            color: #ffffff;
        }

        /* ANIMATION */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        @media(max-width:480px){
            .login-card {
                margin: 0 18px;
                padding: 34px 26px;
            }
            .logo-box img {
                width: 150px;
            }
        }
    </style>
</head>

<body>

<div class="login-card">

    <!-- LOGO -->
    <div class="logo-box">
        <img src="{{ asset('hasantour.png') }}" alt="Logo">
    </div>

    <!-- TITLE -->
    <div class="title">Selamat Datang</div>

    <!-- SUBTITLE -->
    <div class="subtitle">Masuk untuk melanjutkan ke dashboard Jamaah & Umrah</div>

    <!-- FORM -->
    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="input-group">
            <input type="text"
                name="login"
                autocomplete="username"
                class="form-control"
                placeholder="Email / No HP"
                required>
        </div>

        <div class="input-group">
            <input type="password"
                id="password"
                name="password"
                autocomplete="current-password"
                class="form-control"
                placeholder="Password"
                required>
            <i class="fa-solid fa-eye toggle-pass" onclick="togglePassword()"></i>
        </div>

        <button type="submit" class="btn-login">Login</button>
    </form>

    <!-- FOOTER -->
    <div class="login-footer">
        Design by
        <a href='https://ditelaga.id'
           target='_blank'
           style="
               color:#6b7280;
               text-decoration:none;
               font-weight:600;
           "
        >© Ditelaga Creative Digital</a>
    </div>

</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon = document.querySelector('.toggle-pass');

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>

</body>
</html>
