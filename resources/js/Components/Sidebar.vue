<template>
    <aside class="w-64 bg-gray-800 text-white min-h-screen p-4 flex-shrink-0">
        <h2 class="text-lg font-bold">
            Menu
        </h2>

        <nav class="space-y-2 mt-4">
            <Link href="/dashboard" class="block hover:text-gray-300">
                Dashboard
            </Link>

            <Link href="/" class="block hover:text-gray-300">
                Beranda
            </Link>
            <Link href="/" class="block hover:text-gray-300">
                Data Buku
            </Link>

            <Link href="/" class="block hover:text-gray-300">
                Data Anggota
            </Link>
        </nav>
    </aside>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>