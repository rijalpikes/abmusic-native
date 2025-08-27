<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>ABmusic | Layanan Sound System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<style>
    @keyframes loading {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    .animate-[loading_2s_linear_infinite] {
        animation: loading 2s linear infinite;
    }
</style>

<body class="bg-[#fff8f0]">
    <!-- Splash Screen -->
    <!-- <div id="splash"
        class="fixed inset-0 bg-white flex items-center justify-center z-50 transition-opacity duration-1000 opacity-100">
        <img src="assets/img/ABmusic-Logo.png" alt="ABmusic" class="w-150  animate-pulse">
    </div> -->
    <div id="splash" class="fixed inset-0 z-50 flex flex-col items-center justify-center text-white text-center">

        <!-- LOGO -->
        <img src="assets/img/ABmusic-Logo.png" alt="ABmusic Logo"
            class="w-80 h-auto mb-4 animate-pulse drop-shadow-xl rounded-full">

        <!-- ANIMASI EQUALIZER -->
        <div class="flex space-x-1 mb-4">
            <div class="w-2 h-6 bg-orange-500 animate-bounce [animation-delay:0.1s]"></div>
            <div class="w-2 h-6 bg-orange-500 animate-bounce [animation-delay:0.2s]"></div>
            <div class="w-2 h-6 bg-orange-500 animate-bounce [animation-delay:0.3s]"></div>
            <div class="w-2 h-6 bg-orange-500 animate-bounce [animation-delay:0.4s]"></div>
        </div>

        <p class="text-lg text-orange-500 font-semibold animate-pulse">Memuat ABmusic...</p>

        <!-- PROGRESS BAR -->
        <div class="w-48 bg-orange-500 h-2 rounded-full mt-4 overflow-hidden">
            <div class="h-full bg-black animate-[loading_2s_linear_infinite]"></div>
        </div>
    </div>
    <!-- Konten halaman -->
    <div id="main-content" class="hidden">
        <!-- Form dan konten utama di sini -->

        <nav class="bg-orange-500 text-white sticky top-0 z-50 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex-shrink-0 text-xl font-bold">
                        <a href="index.php">ABmusic</a>
                    </div>
                    <div class="hidden md:flex space-x-6">
                        <a href="index.php"
                            class="<?= ($menu_aktif == 'beranda') ? 'font-bold underline' : '' ?> hover:underline">Beranda</a>
                        <a href="#galeri"
                            class="<?= ($menu_aktif == 'galeri') ? 'font-bold underline' : '' ?> hover:underline">Galeri</a>
                        <a href="#lokasi"
                            class="<?= ($menu_aktif == 'lokasi') ? 'font-bold underline' : '' ?> hover:underline">Lokasi</a>
                        <a href="#paket"
                            class="<?= ($menu_aktif == 'paket') ? 'font-bold underline' : '' ?> hover:underline">Paket</a>
                        <a href="#form-pemesanan"
                            class="<?= ($menu_aktif == 'pemesanan') ? 'font-bold underline' : '' ?> hover:underline">Pemesanan</a>
                        <a href="#testimoni"
                            class="<?= ($menu_aktif == 'testimoni') ? 'font-bold underline' : '' ?> hover:underline">Testimoni</a>
                        <a href="pay/index.php" class="block px-4 py-2 bg-yellow-500 text-black font-bold">💳 Pembayaran</a>
                    </div>
                    <div class="md:hidden">
                        <button id="menu-btn" class="text-white focus:outline-none">
                            ☰
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="md:hidden hidden px-4 pb-4 space-y-2">
                <a href="index.php"
                    class="block <?= ($menu_aktif == 'beranda') ? 'font-bold underline' : '' ?> hover:underline">Beranda</a>
                <a href="#galeri"
                    class="block <?= ($menu_aktif == 'galeri') ? 'font-bold underline' : '' ?> hover:underline">Galeri</a>
                <a href="#lokasi"
                    class="block <?= ($menu_aktif == 'lokasi') ? 'font-bold underline' : '' ?> hover:underline">Lokasi</a>
                <a href="#paket"
                    class="block <?= ($menu_aktif == 'paket') ? 'font-bold underline' : '' ?> hover:underline">Paket</a>
                <a href="#form-pemesanan"
                    class="block <?= ($menu_aktif == 'pemesanan') ? 'font-bold underline' : '' ?> hover:underline">Pemesanan</a>
                <a href="#testimoni"
                    class="block <?= ($menu_aktif == 'testimoni') ? 'font-bold underline' : '' ?> hover:underline">Testimoni</a>
                <a href="pay/index.php" class="hover:underline text-yellow-400 font-bold">💳 Pembayaran</a>
            </div>
        </nav>

        <script>
            const menuBtn = document.getElementById('menu-btn');
            const mobileMenu = document.getElementById('mobile-menu');

            menuBtn.addEventListener('click', () => {
                mobileMenu.classList.toggle('hidden');
            });
        </script>

        <div class="max-w-7xl mx-auto mt-6 px-4">