<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Email JadiUmrah</title>
</head>
<body style="margin:0; padding:0; background:#f3f4f6; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:20px;">
<tr>
<td align="center">

    <!-- CARD -->
    <table width="500" cellpadding="0" cellspacing="0" 
           style="background:#ffffff; border-radius:10px; padding:24px;">

        <!-- HEADER -->
        <tr>
            <td align="center" style="padding-bottom:20px;">
                <h2 style="margin:0; color:#16a34a;">
                    JadiUmrah
                </h2>
                <p style="margin:5px 0 0; font-size:12px; color:#6b7280;">
                    Perjalanan Ibadah Lebih Mudah
                </p>
            </td>
        </tr>

        <!-- CONTENT -->
        <tr>
            <td style="color:#111827; font-size:14px; line-height:1.6;">
                
                <p>Assalamu'alaikum <b>{{ $name }}</b> 👋</p>

                <p>
                    Selamat datang di <b>JadiUmrah</b> ✨  
                    Silakan verifikasi akun Anda untuk melanjutkan.
                </p>

                <!-- BUTTON -->
                <div style="text-align:center; margin:25px 0;">
                    <a href="{{ $link }}" 
                       style="display:inline-block;
                              background:#16a34a;
                              color:#ffffff;
                              padding:12px 20px;
                              border-radius:6px;
                              text-decoration:none;
                              font-weight:bold;">
                        Verifikasi Akun
                    </a>
                </div>

                <p style="font-size:12px; color:#6b7280;">
                    Jika tombol tidak berfungsi, copy link berikut:
                </p>

                <p style="font-size:12px; word-break:break-all; color:#16a34a;">
                    {{ $link }}
                </p>

                <p style="font-size:12px; color:#6b7280;">
                    ⏳ Link berlaku selama 30 menit.
                </p>

                <p style="font-size:12px; color:#6b7280;">
                    Jika Anda tidak merasa mendaftar, abaikan email ini.
                </p>

                <br>

                <p>
                    Barakallahu fiikum 🤲<br>
                    <b>Tim JadiUmrah</b>
                </p>

            </td>
        </tr>

        <!-- FOOTER -->
        <tr>
            <td align="center" style="padding-top:20px; font-size:11px; color:#9ca3af;">
                © {{ date('Y') }} JadiUmrah. All rights reserved.
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>