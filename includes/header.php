<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>ABmusic | Layanan Sound System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#fff8f0]">
    <nav class="bg-orange-500 text-white sticky top-0 z-50 shadow-md">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex-shrink-0 text-xl font-bold">
                    <a href="index.php">ABmusic</a>
                </div>
                <div class="hidden md:flex space-x-6">
                    <a href="index.php"
                        class="<?= ($menu_aktif == 'beranda') ? 'font-bold underline' : '' ?> hover:underline">Beranda</a>
                    <a href="#form-pemesanan"
                        class="<?= ($menu_aktif == 'pemesanan') ? 'font-bold underline' : '' ?> hover:underline">Pemesanan</a>
                    <a href="testimoni.php"
                        class="<?= ($menu_aktif == 'testimoni') ? 'font-bold underline' : '' ?> hover:underline">Testimoni</a>
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
            <a href="#form-pemesanan"
                class="block <?= ($menu_aktif == 'pemesanan') ? 'font-bold underline' : '' ?> hover:underline">Pemesanan</a>
            <a href="testimoni.php"
                class="block <?= ($menu_aktif == 'testimoni') ? 'font-bold underline' : '' ?> hover:underline">Testimoni</a>
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