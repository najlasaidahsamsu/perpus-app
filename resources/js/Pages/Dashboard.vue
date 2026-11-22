<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
// BarChart = komponen grafik yang baru dibuat (Components/BarChart.vue)
import BarChart from '@/Components/BarChart.vue';
import { Head } from '@inertiajs/vue3';

// Data dari DashboardController@index:
// statistik = angka-angka kartu, grafik = label dan jumlah per bulan
defineProps({
    statistik: Object,
    grafik: Object,
});

// Mengubah angka 5000 menjadi teks "Rp5.000"
function formatRupiah(angka) {
    return 'Rp' + Number(angka).toLocaleString('id-ID');
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Dashboard
            </h2>
        </template>

        <div class="p-4">
            <!-- Kotak kartu: 1 kolom di layar kecil, 2 kolom di layar
                 sedang, 4 kolom di layar besar. gap-4 = jarak antar kartu. -->
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

                <!-- KARTU 1: Judul Buku.
                     border-l-4 border-blue-500 = garis biru di sisi kiri.
                     rounded-lg = sudut membulat, shadow-sm = bayangan tipis. -->
                <div
                    class="rounded-lg border-l-4 border-blue-500 bg-white p-5
                           shadow-sm"
                >
                    <!-- Teks judul kartu: kecil dan abu-abu -->
                    <div class="text-sm text-gray-500">Judul Buku</div>
                    <!-- Angka utama: besar dan tebal -->
                    <div class="text-3xl font-bold text-gray-800">
                        {{ statistik.total_buku }}
                    </div>
                    <!-- Keterangan tambahan di bawah angka -->
                    <div class="text-xs text-gray-500">
                        Total stok: {{ statistik.total_stok }} eksemplar
                    </div>
                </div>

                <!-- KARTU 2: Anggota. Pola sama dengan kartu 1, hanya warna
                     garis dan isinya yang berbeda. -->
                <div
                    class="rounded-lg border-l-4 border-green-500 bg-white p-5
                           shadow-sm"
                >
                    <div class="text-sm text-gray-500">Anggota</div>
                    <div class="text-3xl font-bold text-gray-800">
                        {{ statistik.total_anggota }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Terdaftar di perpustakaan
                    </div>
                </div>

                <!-- KARTU 3: Sedang Dipinjam -->
                <div
                    class="rounded-lg border-l-4 border-yellow-500 bg-white p-5
                           shadow-sm"
                >
                    <div class="text-sm text-gray-500">Sedang Dipinjam</div>
                    <div class="text-3xl font-bold text-gray-800">
                        {{ statistik.sedang_dipinjam }}
                    </div>
                    <!-- Merah jika ada yang terlambat, abu-abu jika tidak.
                         Tanda : di depan class = isinya ekspresi JavaScript. -->
                    <div
                        class="text-xs"
                        :class="statistik.terlambat > 0
                            ? 'text-red-600'
                            : 'text-gray-500'"
                    >
                        {{ statistik.terlambat }} terlambat
                    </div>
                </div>

                <!-- KARTU 4: Total Denda, diformat rupiah oleh formatRupiah() -->
                <div
                    class="rounded-lg border-l-4 border-red-500 bg-white p-5
                           shadow-sm"
                >
                    <div class="text-sm text-gray-500">Total Denda</div>
                    <div class="text-3xl font-bold text-gray-800">
                        {{ formatRupiah(statistik.total_denda) }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Dari buku yang sudah dikembalikan
                    </div>
                </div>
            </div>

            <!-- KARTU 5: Peminjaman Hari Ini -->
            <div
                class="rounded-lg border-l-4 border-purple-500 bg-white p-5shadow-sm">
                <div class="text-sm text-gray-500">Peminjaman Hari Ini</div>
                <div class="text-3xl font-bold text-gray-800">
                    {{ statistik.peminjaman_hari_ini }}
                </div>
                <div class="text-xs text-gray-500">
                    Jumlah peminjaman pada hari ini
                </div>
            </div>
            
            <!-- Kotak grafik. mt-4 = jarak dari kartu di atasnya. -->
            <div class="mt-4 rounded-lg bg-white p-5 shadow-sm">
                <h3 class="mb-3 font-semibold text-gray-800">
                    Peminjaman 6 Bulan Terakhir
                </h3>
                <!-- Mengirim label dan jumlah ke BarChart sebagai props.
                     Tanda : di depan nama atribut = nilainya variabel,
                     bukan teks biasa. -->
                <BarChart
                    :label="grafik.label"
                    :jumlah="grafik.jumlah"
                    judul="Jumlah peminjaman"
                />
            </div>
        </div>
    </AuthenticatedLayout>
</template>
