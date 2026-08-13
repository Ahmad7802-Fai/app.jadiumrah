<?php

namespace App\Services;

use App\Models\Jamaah;
use Illuminate\Support\Str;

class WhatsAppLinkService
{
    public function generate(Jamaah $jamaah, string $type = 'follow_up'): string
    {
        $phone = $this->normalizePhone($jamaah->no_hp);
        $message = $this->template($type, $jamaah);

        return 'https://api.whatsapp.com/send?phone='
            . $phone
            . '&text=' . urlencode($message);
    }

    /* ===============================
     | TEMPLATE MESSAGE
     =============================== */
    protected function template(string $type, Jamaah $jamaah): string
    {
        return match ($type) {

            'closing' => $this->closing($jamaah),

            'reminder' => $this->reminder($jamaah),

            default => $this->followUp($jamaah),
        };
    }

    protected function followUp(Jamaah $jamaah): string
    {
        return <<<TEXT
Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},

Saya {$this->agentName()} dari {$this->brand()} 😊
Kami menerima pendaftaran Umrah Bapak/Ibu melalui website kami.

InsyaAllah saya akan membantu proses selanjutnya.
TEXT;
    }

    protected function reminder(Jamaah $jamaah): string
    {
        return <<<TEXT
Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},

Saya {$this->agentName()} dari {$this->brand()} 😊
Izin follow up kembali terkait pendaftaran Umrah Bapak/Ibu.

Apakah ada yang bisa kami bantu hari ini?
TEXT;
    }

    protected function closing(Jamaah $jamaah): string
    {
        return <<<TEXT
Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},

Saya {$this->agentName()} dari {$this->brand()} 😊
Alhamdulillah data pendaftaran Umrah sudah kami terima.

Selanjutnya kami akan bantu proses administrasi & keberangkatan.
Mohon konfirmasi jika sudah siap ya 🙏
TEXT;
    }

    /* ===============================
     | HELPER
     =============================== */
    protected function normalizePhone(?string $phone): string
    {
        return ltrim(preg_replace('/[^0-9]/', '', $phone), '0')
            ? '62' . ltrim($phone, '0')
            : '';
    }

    protected function agentName(): string
    {
        $user = auth()->user();
        return $user->nama ?? $user->name ?? 'Tim Kami';
    }

    protected function brand(): string
    {
        return config('app.name', 'HASAN Umrah');
    }
}

// namespace App\Services;

// use App\Models\Jamaah;

// class WhatsAppLinkService
// {
//     /**
//      * WhatsApp non-Business endpoint
//      */
//     protected string $baseUrl = 'https://api.whatsapp.com/send';

//     /* =====================================================
//      | PUBLIC API — TEMPLATE WA
//      ===================================================== */

//     /**
//      * Default follow up (setelah daftar)
//      */
//     public function followUp(Jamaah $jamaah): string
//     {
//         $message =
//             "Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},\n\n"
//           . "Saya {$this->agentName()} dari Tim {$this->brand()} 😊\n"
//           . "Kami menerima pendaftaran Umrah Bapak/Ibu melalui website kami.\n\n"
//           . "InsyaAllah saya akan membantu proses selanjutnya.";

//         return $this->build($jamaah->no_hp, $message);
//     }

//     /**
//      * Reminder (belum respon / follow up ulang)
//      */
//     public function reminder(Jamaah $jamaah): string
//     {
//         $message =
//             "Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},\n\n"
//           . "Kami dari Tim {$this->brand()} ingin menindaklanjuti "
//           . "pendaftaran Umrah Bapak/Ibu.\n\n"
//           . "Jika berkenan, kami siap membantu 🙏";

//         return $this->build($jamaah->no_hp, $message);
//     }

//     /**
//      * Closing (siap lanjut)
//      */
//     public function closing(Jamaah $jamaah): string
//     {
//         $paket = $jamaah->nama_paket ?: 'paket Umrah';

//         $message =
//             "Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},\n\n"
//           . "Alhamdulillah {$paket} sudah siap kami proseskan.\n\n"
//           . "Jika Bapak/Ibu berkenan, kami bisa lanjutkan "
//           . "pengamanan seat hari ini 😊";

//         return $this->build($jamaah->no_hp, $message);
//     }

//     /**
//      * Template dinamis (future-proof)
//      */
//     public function custom(Jamaah $jamaah, string $text): string
//     {
//         return $this->build($jamaah->no_hp, $text);
//     }

//     /* =====================================================
//      | INTERNAL HELPERS
//      ===================================================== */

//     protected function build(?string $phone, string $message): string
//     {
//         if (!$phone) {
//             abort(422, 'Nomor WhatsApp jamaah belum tersedia.');
//         }

//         return $this->baseUrl
//             . '?phone=' . $this->normalizePhone($phone)
//             . '&text=' . urlencode($message);
//     }

//     protected function normalizePhone(string $phone): string
//     {
//         // bersihkan spasi & simbol
//         $phone = preg_replace('/[^0-9]/', '', $phone);

//         // 08xxxx → 628xxxx
//         if (str_starts_with($phone, '08')) {
//             return '62' . substr($phone, 1);
//         }

//         return $phone;
//     }

//     protected function brand(): string
//     {
//         return config('app.name', 'HASAN Umrah');
//     }

//     protected function agentName(): string
//     {
//         $user = auth()->user();

//         if (!$user) {
//             return 'Tim Kami';
//         }

//         // Prioritas nama agent
//         return $user->nama
//             ?? $user->name
//             ?? 'Tim Kami';
//     }

// }


// namespace App\Services;

// use App\Models\Jamaah;

// class WhatsAppLinkService
// {
//     public function toJamaah(Jamaah $jamaah, string $context = 'followup'): string
//     {
//         $phone = $this->normalizePhone($jamaah->no_hp);

//         $message = match ($context) {
//             'closing' => "Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},\n"
//                 . "Kami dari tim Hasan Tour ingin konfirmasi pendaftaran Anda.",

//             default => "Assalamu’alaikum Bapak/Ibu {$jamaah->nama_lengkap},\n"
//                 . "Kami dari tim Umrah ingin follow up pendaftaran Anda.",
//         };

//         return "https://wa.me/{$phone}?text=" . urlencode($message);
//     }

//     private function normalizePhone(string $phone): string
//     {
//         $phone = preg_replace('/[^0-9]/', '', $phone);

//         if (str_starts_with($phone, '0')) {
//             return '62' . substr($phone, 1);
//         }

//         if (!str_starts_with($phone, '62')) {
//             return '62' . $phone;
//         }

//         return $phone;
//     }
// }
