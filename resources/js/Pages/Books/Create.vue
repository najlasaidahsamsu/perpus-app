
<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch, onBeforeUnmount } from 'vue';
import axios from 'axios';

// useForm membuat sebuah objek form yang otomatis mengelola nilai input dan pesan error
const form = useForm({
    kode_buku: '',
    judul: '',
    pengarang: '',
    penerbit: '',
    tahun: '',
    stok: 0,
});

// Status validasi kode buku
const statusKode = ref('');
let timer = null;
let controller = null;

// Mengecek kode buku secara real-time
watch(() => form.kode_buku, (kode) => {
    clearTimeout(timer);

    // Membatalkan request sebelumnya jika masih berjalan
    if (controller) {
        controller.abort();
    }

    // Jika input kosong, status dikembalikan
    if (!kode.trim()) {
        statusKode.value = '';
        return;
    }

    statusKode.value = 'mengecek';

    // Memberikan jeda 500ms sebelum mengirim request
    timer = setTimeout(async () => {
        controller = new AbortController();

        try {
            const response = await axios.get(route('books.cek-kode'), {
                params: {
                    kode_buku: kode
                },
                signal: controller.signal
            });

            statusKode.value = response.data.tersedia
                ? 'tersedia'
                : 'terpakai';

        } catch (error) {
            if (error.name !== 'CanceledError' && !axios.isCancel(error)) {
                statusKode.value = '';
                console.error('Gagal mengecek kode buku:', error);
            }
        }
    }, 500);
});

// Membersihkan timer dan request ketika komponen ditutup
onBeforeUnmount(() => {
    clearTimeout(timer);

    if (controller) {
        controller.abort();
    }
});

// Dipanggil ketika tombol submit diklik
function submit() {
    // Mengirim data form ke route books.store
    form.post(route('books.store'));
}
</script>

<template>
    <AuthenticatedLayout>
        <h1>Tambah Buku</h1>

        <form @submit.prevent="submit">
            <!-- @submit.prevent mencegah form reload halaman, lalu menjalankan function submit() -->

            <label>Kode Buku</label>
            <input v-model="form.kode_buku" type="text">

            <!-- Menampilkan status pengecekan kode buku -->
            <span v-if="statusKode === 'mengecek'">
                Sedang mengecek kode buku...
            </span>

            <span v-if="statusKode === 'tersedia'" style="color: green;">
                Kode buku tersedia
            </span>

            <span v-if="statusKode === 'terpakai'" style="color: red;">
                Kode buku sudah digunakan
            </span>

            <!-- Menampilkan pesan error dari validasi Laravel jika ada -->
            <span v-if="form.errors.kode_buku" style="color: red;">
                {{ form.errors.kode_buku }}
            </span>

            <label>Judul</label>
            <input v-model="form.judul" type="text">
            <span v-if="form.errors.judul">{{ form.errors.judul }}</span>

            <label>Pengarang</label>
            <input v-model="form.pengarang" type="text">
            <span v-if="form.errors.pengarang">{{ form.errors.pengarang }}</span>

            <label>Penerbit</label>
            <input v-model="form.penerbit" type="text">

            <label>Tahun</label>
            <input v-model="form.tahun" type="text">

            <label>Stok</label>
            <input v-model="form.stok" type="number">

            <button
                type="submit"
                :disabled="form.processing || statusKode === 'terpakai' || statusKode === 'mengecek'"
            >
                Simpan
            </button>
        </form>
    </AuthenticatedLayout>
</template>
