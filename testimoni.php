<?php
session_start();
$menu_aktif = 'beranda';
include 'includes/header.php';

// Simpan session sederhana
if (!isset($_SESSION['welcome'])) {
    $_SESSION['welcome'] = "Selamat datang di ABmusic!";
}
?>
<style>
@keyframes scrollX {
    0% {
        transform: translateX(0%);
    }

    100% {
        transform: translateX(-50%);
    }
}
</style>

<!-- Jumbotron -->

<?php
include 'includes/jumbotron.php';
?>

<!-- Modal (klik gambar untuk zoom) -->
<div id="imageModal"
    class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 cursor-pointer">
    <img id="modalImage" class="max-w-3xl w-full rounded-xl">
</div>
<div class="container mx-auto px-4 mt-16">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Testimoni Pelanggan</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php
        // Koneksi ke database
        $host = "localhost";
        $dbname = "abmusic_db"; // ganti sesuai nama database kamu
        $user = "root";
        $pass = "";

        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Koneksi gagal: " . $e->getMessage());
        }

        // Ambil data testimoni
        $stmt = $pdo->query("SELECT * FROM testimoni ORDER BY tanggal DESC LIMIT 6");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div class="bg-white p-6 rounded-lg shadow-md">';
            echo '<h3 class="text-lg font-semibold text-gray-800">' . htmlspecialchars($row['nama']) . '</h3>';
            echo '<p class="text-yellow-400 text-xl">' . str_repeat('★', (int)$row['rating']) . '</p>';
            echo '<p class="text-gray-600 mt-2">' . htmlspecialchars($row['pesan']) . '</p>';
            echo '<p class="text-gray-500 text-sm mt-4">' . date('d M Y', strtotime($row['tanggal'])) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-span-1">
                <div id="form-testimoni" class="max-w-7xl mx-auto px-4 mt-10">
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Testimoni</h2>
                    <form action="simpan_testimoni.php" method="post" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="col-span-1">
                            <label for="nama" class="block text-gray-700 font-semibold mb-2">Nama</label>
                            <input type="text" name="nama" id="nama"
                                class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                required>
                        </div>
                        <div class="col-span-1">
                            <label for="no_hp" class="block text-gray-700 font-semibold mb-2">No HP</label>
                            <input type="text" name="no_hp" id="no_hp"
                                class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                required>
                        </div>

                        <!-- Rating Bintang -->
                        <div class="col-span-2">
                            <label class="block text-gray-700 font-semibold mb-2">Rating</label>
                            <div id="star-rating" class="flex space-x-2 text-3xl text-gray-300 cursor-pointer">
                                <span data-value="1">&#9733;</span>
                                <span data-value="2">&#9733;</span>
                                <span data-value="3">&#9733;</span>
                                <span data-value="4">&#9733;</span>
                                <span data-value="5">&#9733;</span>
                            </div>
                            <input type="hidden" name="rating" id="rating" required>
                        </div>

                        <div class="col-span-2">
                            <label for="pesan" class="block text-gray-700 font-semibold mb-2">Pesan</label>
                            <textarea name="pesan" id="pesan"
                                class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                                rows="4"></textarea>
                        </div>
                        <div class="col-span-2">
                            <button type="submit"
                                class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                                Kirim Testimoni
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>




</div>

<?php include 'includes/footer.php'; ?>


<!-- Rating -->
<script>
const stars = document.querySelectorAll('#star-rating span');
const ratingInput = document.getElementById('rating');
let currentRating = 0;

stars.forEach((star, index) => {
    star.addEventListener('mouseover', () => {
        highlightStars(index);
    });

    star.addEventListener('mouseout', () => {
        highlightStars(currentRating - 1);
    });

    star.addEventListener('click', () => {
        currentRating = index + 1;
        ratingInput.value = currentRating;
    });
});

function highlightStars(index) {
    stars.forEach((star, i) => {
        if (i <= index) {
            star.classList.add('text-yellow-400');
            star.classList.remove('text-gray-300');
        } else {
            star.classList.add('text-gray-300');
            star.classList.remove('text-yellow-400');
        }
    });
}
</script>