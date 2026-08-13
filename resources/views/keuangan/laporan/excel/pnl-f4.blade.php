<table>
    {{-- HEADER --}}
    <tr>
        <td colspan="2" style="text-align:center; font-weight:bold;">
            LAPORAN LABA RUGI (PNL) — JADIUMRAH
        </td>
    </tr>

    {{-- PERIODE --}}
    <tr>
        <td><b>Periode</b></td>
        <td>{{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</td>
    </tr>

    <tr><td></td></tr>

    {{-- TABLE HEADER --}}
    <tr>
        <th style="background:#E8F5E9; font-weight:bold;">Keterangan</th>
        <th style="background:#E8F5E9; font-weight:bold;">Jumlah</th>
    </tr>

    {{-- DATA --}}
    <tr>
        <td>Pendapatan (Valid)</td>
        <td>Rp {{ number_format($revenues, 0, ',', '.') }}</td>
    </tr>

    <tr>
        <td>Pengeluaran Trip</td>
        <td>Rp {{ number_format($tripExpenses, 0, ',', '.') }}</td>
    </tr>

    <tr>
        <td>Pengeluaran Operasional</td>
        <td>Rp {{ number_format($operational, 0, ',', '.') }}</td>
    </tr>

    {{-- TOTAL --}}
    <tr>
        <td><b>Laba / Rugi Bersih</b></td>
        <td><b>
            Rp {{ number_format($revenues - ($tripExpenses + $operational), 0, ',', '.') }}
        </b></td>
    </tr>
</table>
