<?php
// session_start();
$menu_aktif = 'beranda';
include 'config/database.php';
include 'includes/header.php';

// Simpan session sederhana
if (!isset($_SESSION['welcome'])) {
    $_SESSION['welcome'] = "Selamat datang di ABmusic!";
}
?>
<div class="container">
    <!-- Jumbotron -->
    <div class="bg-orange-100 py-10 px-4 rounded-xl shadow-inner mb-10">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl sm:text-5xl font-extrabold text-orange-600 mb-4">Selamat Datang di ABmusic</h2>
            <p class="text-lg text-gray-700 mb-6">
                Karaoke dimana saja, kapan saja, Anda Tunjukkan tempatnya kami sediakan
            </p>
            <a href="#form-pemesanan"
                class="inline-block bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-8 rounded-full transition duration-300">
                Pesan Sekarang
            </a>
        </div>
    </div>

    <!-- Galeri Acara -->

    <?php
    include 'galery.php';
    include 'lokasi.php';
    include 'paket.php';
    include 'testimoni.php';
    include 'pemesanan.php';
    ?>

</div>
<script src="assets/js/main.js"></script>
</body>
<?php include 'includes/footer.php'; ?>