<!DOCTYPE html>
<html>
<head>
    <title>Login Jamaah</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-gray-100 flex items-center justify-center min-h-screen">

<div class="w-full max-w-md bg-white p-6 rounded-lg shadow">
    <h2 class="text-xl font-bold mb-4 text-center">
        Login Jamaah
    </h2>

    <form method="POST" action="{{ route('jamaah.login.submit') }}">
        @csrf

        <div class="mb-3">
            <label>Email / No HP</label>
            <input type="text" name="login"
                   class="form-input w-full"
                   required autofocus>
        </div>

        <div class="mb-4">
            <label>Password</label>
            <input type="password" name="password"
                   class="form-input w-full"
                   required>
        </div>

        @error('login')
            <div class="text-red-600 text-sm mb-3">
                {{ $message }}
            </div>
        @enderror

        <button class="w-full bg-green-600 text-white py-2 rounded">
            Login
        </button>
    </form>
</div>

</body>
</html>
