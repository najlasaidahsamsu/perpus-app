<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Loan;
use App\Models\Member;

class DashboardController extends Controller
{
    /**
     * Menyiapkan angka ringkasan dan data grafik untuk halaman Dashboard.
     */
    public function index()
    {
        // ---- Bagian 1: angka untuk kartu ringkasan ----

        // count() menghitung jumlah baris. sum('kolom') menjumlahkan satu
        // kolom. (int) memastikan hasilnya angka (di MySQL, sum() bisa
        // berupa teks).
        $totalBuku = Book::count();
        $totalStok = (int) Book::sum('stok');
        $totalAnggota = Member::count();

        // Peminjaman yang belum dikembalikan
        $sedangDipinjam = Loan::where('status', 'dipinjam')->count();

        $peminjamanHariIni = Loan::where(
        'tanggal_pinjam',
        today()->toDateString()
         )->count();

        // Dari yang belum dikembalikan, berapa yang jatuh temponya sebelum
        // hari ini. Tanggal berformat 'Y-m-d' sehingga bisa dibandingkan
        // dengan tanda '<'.
        $terlambat = Loan::where('status', 'dipinjam')
            ->where('tanggal_kembali_rencana', '<', today()->toDateString())
            ->count();

        // Jumlah seluruh denda yang tercatat pada kolom denda
        $totalDenda = (int) Loan::sum('denda');

       // ---- Bagian 2: data grafik peminjaman 12 bulan terakhir ----

        // Singkatan nama bulan. Indeks 0 = Januari, indeks 11 = Desember.
        $namaBulan = [
            'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun',
            'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'
];

        // Dua daftar kosong: $label untuk sumbu X, $jumlah untuk tinggi batang
        $label = [];
        $jumlah = [];

        // Ulangi dari 5 sampai 0: 5 bulan lalu, ..., bulan lalu, bulan ini.
        // Dengan begitu bulan terlama di kiri dan bulan ini di kanan.
        for ($i = 11; $i >= 0; $i--) {
            // Tanggal 1 pada bulan ke-$i sebelum bulan ini
            $awal = today()->startOfMonth()->subMonths($i);
            // Hari terakhir pada bulan yang sama
            $akhir = $awal->copy()->endOfMonth();

            // $awal->month berisi 1-12. Dikurangi 1 agar cocok dengan
            // nomor indeks array (mulai dari 0).
            $label[] = $namaBulan[$awal->month - 1].' '.$awal->year;

            // Hitung peminjaman yang tanggal pinjamnya berada di antara
            // dua tanggal tadi
            $jumlah[] = Loan::whereBetween('tanggal_pinjam', [
                $awal->toDateString(),
                $akhir->toDateString(),
            ])->count();
        }

        // ---- Bagian 3: kirim semuanya ke Dashboard.vue ----
        return inertia('Dashboard', [
            'statistik' => [
                'total_buku' => $totalBuku,
                'total_stok' => $totalStok,
                'total_anggota' => $totalAnggota,
                'sedang_dipinjam' => $sedangDipinjam,
                'terlambat' => $terlambat,
                'total_denda' => $totalDenda,
                'peminjaman_hari_ini' => $peminjamanHariIni,
            ],
            'grafik' => [
                'label' => $label,
                'jumlah' => $jumlah,
            ],
        ]);
    }
}
