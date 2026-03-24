@extends('layouts.auth')

@section('content')

<div class="w-full max-w-md bg-white/5 backdrop-blur-xl border border-white/10 rounded-3xl shadow-2xl p-10 text-white">

    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold">Umrah Core</h1>
        <p class="text-sm text-white/70 mt-2">
            Masuk untuk melanjutkan ke dashboard Jamaah & Umrah
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <input type="email" name="email" required autofocus
            placeholder="Email / No HP"
            class="w-full px-5 py-4 rounded-2xl bg-white/20 border border-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/40"
        />

        <input type="password" name="password" required
            placeholder="Password"
            class="w-full px-5 py-4 rounded-2xl bg-white/20 border border-white/20 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-white/40"
        />

        <button type="submit"
            class="w-full py-4 rounded-2xl bg-white text-primary font-semibold text-lg hover:bg-gray-200 transition">
            Login
        </button>

    </form>

</div>

@endsection