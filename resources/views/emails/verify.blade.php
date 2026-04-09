<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email</title>
</head>
<body style="font-family: Arial; background: #f5f5f5; padding: 20px;">

    <div style="max-width: 500px; margin: auto; background: white; padding: 20px; border-radius: 10px;">
        
        <h2>Assalamu'alaikum {{ $name }} 👋</h2>

        <p>Selamat datang di <b>JadiUmrah</b> ✨</p>

        <p>Silakan verifikasi akun Anda dengan klik tombol di bawah:</p>

        <div style="text-align: center; margin: 20px 0;">
            <a href="{{ $link }}" 
               style="background: #16a34a; color: white; padding: 10px 20px; border-radius: 6px; text-decoration: none;">
               Verifikasi Akun
            </a>
        </div>

        <p>Atau copy link ini:</p>
        <p style="word-break: break-all;">{{ $link }}</p>

        <p>⏳ Link berlaku 30 menit.</p>

        <p>Jika Anda tidak merasa mendaftar, abaikan email ini.</p>

        <br>

        <p>Barakallahu fiikum 🤲</p>
        <p><b>Tim JadiUmrah</b></p>

    </div>

</body>
</html>