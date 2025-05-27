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
<!-- Galeri Alat -->
<div class="container">
    <h2 class="text-2xl font-semibold text-center text-gray-800 mt-16 mb-8">Galeri Produk Sound System</h2>
    <div class="relative overflow-hidden group">
        <!-- Wrapper slider -->
        <div id="slider" class="flex space-x-4 overflow-x-auto scroll-smooth snap-x snap-mandatory px-4 pb-4">
            <?php for ($i = 1; $i <= 5; $i++): ?>
            <img src="assets/img/sound<?= $i ?>.jpg" alt="Sound <?= $i ?>"
                class="w-64 h-40 object-cover rounded-lg shadow-md snap-start flex-shrink-0 cursor-pointer hover:scale-105 transition"
                onclick="showImageModal('assets/img/sound<?= $i ?>.jpg')">
            <?php endfor; ?>
        </div>

        <!-- Tombol kiri -->
        <button onclick="scrollSlider('left')"
            class="absolute left-2 top-1/2 transform -translate-y-1/2 bg-gray-700 text-white p-2 rounded-full hidden group-hover:block z-10">
            &#10094;
        </button>
        <!-- Tombol kanan -->
        <button onclick="scrollSlider('right')"
            class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-gray-700 text-white p-2 rounded-full hidden group-hover:block z-10">
            &#10095;
        </button>
    </div>
</div>
<!-- Galeri Acara -->
<div class="container">
    <!-- Galeri Event Otomatis dari Folder -->
    <section class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-2xl font-bold text-gray-800 text-center mb-6">Galeri Acara</h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
            <?php
        $folder = 'assets/galeri/'; // folder gambar
        $files = array_diff(scandir($folder), array('.', '..'));

        foreach ($files as $file) {
            $path = $folder . $file;
            if (is_file($path) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                echo '
                <div class="relative overflow-hidden rounded-lg shadow-lg transform hover:-translate-y-2 hover:shadow-2xl transition duration-300 cursor-pointer group">
                    <img src="' . $path . '" alt="' . htmlspecialchars($file) . '" class="w-full h-64 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 flex items-center justify-center transition">
                        <p class="text-white font-semibold">Acara ' . pathinfo($file, PATHINFO_FILENAME) . '</p>
                    </div>
                </div>';
            }
        }
        ?>
        </div>
    </section>
    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <img id="lightbox-img" src="" alt="Preview" class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg">
        <button onclick="closeLightbox()" class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
    </div>
</div>

<!-- Modal (klik gambar untuk zoom) -->
<div id="imageModal"
    class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 cursor-pointer">
    <img id="modalImage" class="max-w-3xl w-full rounded-xl">
</div>

<div class="mt-16 flex justify-center">
    <div class="w-full max-w-4xl">
        <h3 class="text-2xl font-bold mb-4 text-center text-gray-800">
            Lokasi Kami di Kabupaten Maros
        </h3>
        <div class="w-full h-[400px] rounded-xl overflow-hidden shadow-lg">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d248.41227698804914!2d119.56763781920013!3d-5.00649182810335!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dbef939df7914d9%3A0x7558d785ba9071dc!2sABmusic!5e0!3m2!1sid!2sid!4v1748245886483!5m2!1sid!2sid"
                class="w-full h-full border-0" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col">
            <h2 class="text-2xl font-bold text-gray-800 mt-16 mb-8 text-center">Testimoni Pelanggan</h2>

            <div id="testimoni-carousel"
                class="flex overflow-x-auto space-x-4 snap-x snap-mandatory px-4 pb-4 scroll-smooth">
                <?php
                // Koneksi ke database
                $host = "localhost";
                $dbname = "abmusic_db"; // sesuaikan
                $user = "root";
                $pass = "";

                try {
                    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("Koneksi gagal: " . $e->getMessage());
                }

                // Ambil data testimoni
                $stmt = $pdo->query("SELECT * FROM testimoni ORDER BY tanggal DESC LIMIT 5");
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $nama = htmlspecialchars($row['nama']);
                    $pesan = htmlspecialchars($row['pesan']);
                    $rating = intval($row['rating']);
                    $tanggal = date('d M Y', strtotime($row['tanggal']));

                    // Deteksi gender berdasarkan nama sederhana (bisa diganti AI atau tabel gender nanti)
                    $isMale = substr($nama, -1) != 'a'; // contoh kasar: nama yg tidak diakhiri 'a' dianggap laki

                    // Avatar pakai API AI/Avatar Generator
                    $imgUrl = "https://api.dicebear.com/7.x/" . ($isMale ? "adventurer" : "adventurer-neutral") . "/svg?seed=" . urlencode($nama);

                    echo '<div class="bg-white p-6 rounded-lg shadow-md w-80 snap-start flex-shrink-0 relative">';
                    echo '<img src="' . $imgUrl . '" alt="Avatar" class="w-16 h-16 rounded-full mb-3 mx-auto">';
                    echo '<h3 class="text-lg font-semibold text-gray-800 text-center">' . $nama . '</h3>';
                    echo '<p class="text-gray-600 mt-2 text-center">' . $pesan . '</p>';

                    // Tampilkan bintang sesuai rating
                    echo '<div class="flex justify-center mt-3">';
                    for ($i = 1; $i <= 5; $i++) {
                        echo $i <= $rating ? '<span class="text-yellow-400 text-xl">&#9733;</span>' : '<span class="text-gray-300 text-xl">&#9733;</span>';
                    }
                    echo '</div>';

                    echo '<p class="text-gray-400 text-xs mt-2 text-center">' . $tanggal . '</p>';
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <div class="col-span-1">
            <h2 class="text-2xl font-bold text-gray-800 mt-16 mb-8 text-center">Pemesanan</h2>
            <p class="text-center text-gray-600 mb-6">
                Untuk melakukan pemesanan, silakan isi form di bawah ini. Kami akan segera menghubungi Anda untuk
                konfirmasi.
            </p>
            <div id="form-pemesanan" class="max-w-7xl mx-auto px-4 mt-10">
                <form action="simpan_pemesanan.php" method="post" class="grid grid-cols-1 sm:grid-cols-2 gap-6">
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
                    <div class="col-span-1">
                        <label for="tanggal_pesan" class="block text-gray-700 font-semibold mb-2">Tanggal
                            Pemesanan</label>
                        <input type="date" name="tanggal_pesan" id="tanggal_pesan"
                            class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            required>
                    </div>
                    <div class="col-span-2">
                        <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan"
                            class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            rows="4"></textarea>
                    </div>
                    <div class="col-span-2">
                        <button type="submit"
                            class="w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            Kirim Pemesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

<script>
const prevBtn = document.getElementById('prevBtn');
const nextBtn = document.getElementById('nextBtn');
const carousel = document.querySelector('.flex');
const items = document.querySelectorAll('.flex > div');
let currentIndex = 0;

// Fungsi untuk geser ke kiri
prevBtn.addEventListener('click', () => {
    if (currentIndex > 0) {
        currentIndex--;
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
});

// Fungsi untuk geser ke kanan
nextBtn.addEventListener('click', () => {
    if (currentIndex < items.length - 1) {
        currentIndex++;
        carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
    }
});
</script>

<!-- GALERI -->
<script>
let slider = document.getElementById("slider");
let autoScroll;

function startAutoScroll() {
    autoScroll = setInterval(() => {
        slider.scrollLeft += 2;
        if (slider.scrollLeft >= slider.scrollWidth - slider.clientWidth) {
            slider.scrollLeft = 0;
        }
    }, 30); // lebih kecil = lebih cepat
}

function stopAutoScroll() {
    clearInterval(autoScroll);
}

// Start auto-scroll saat halaman dibuka
startAutoScroll();

// Pause saat hover
slider.addEventListener("mouseenter", stopAutoScroll);
slider.addEventListener("mouseleave", startAutoScroll);

// Tombol manual geser
function scrollSlider(direction) {
    const scrollAmount = 300;
    slider.scrollBy({
        left: direction === "left" ? -scrollAmount : scrollAmount,
        behavior: "smooth"
    });
}

// Modal zoom image
function showImageModal(src) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");
    modalImg.src = src;
    modal.classList.remove("hidden");
}
document.getElementById("imageModal").addEventListener("click", function() {
    this.classList.add("hidden");
});
</script>

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

<!-- Auto Scroll JS -->
<script>
const container = document.getElementById('testimoni-carousel');

let scrollAmount = 0;
setInterval(() => {
    scrollAmount += 1;
    if (scrollAmount >= container.scrollWidth - container.clientWidth) {
        scrollAmount = 0; // reset ke awal
    }
    container.scrollTo({
        left: scrollAmount,
        behavior: 'smooth'
    });
}, 30); // kecepatan scroll
</script>

<!-- Script Lightbox -->
<script>
const lightbox = document.getElementById("lightbox");
const lightboxImg = document.getElementById("lightbox-img");

document.querySelectorAll(".group img").forEach(img => {
    img.addEventListener("click", () => {
        lightboxImg.src = img.src;
        lightbox.classList.remove("hidden");
    });
});

function closeLightbox() {
    lightbox.classList.add("hidden");
    lightboxImg.src = "";
}

// Klik di luar gambar untuk menutup
lightbox.addEventListener("click", (e) => {
    if (e.target === lightbox) closeLightbox();
});
</script>