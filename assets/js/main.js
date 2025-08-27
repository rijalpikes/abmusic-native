document.getElementById('paketcombo').addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let harga = selectedOption.getAttribute('data-harga');

    // tampilkan harga ke input yang readonly
    document.getElementById('harga_tampil').value = harga;

    // simpan harga ke input hidden agar ikut terkirim ke server
    document.getElementById('harga').value = harga;
});

window.addEventListener('load', function() {
    setTimeout(() => {
        const splash = document.getElementById('splash');
        splash.classList.add('opacity-0');
        setTimeout(() => {
            splash.style.display = 'none';
            document.getElementById('main-content').classList.remove('hidden');
        }, 1000); // delay sebelum hilang
    }, 2500); // tampil selama 2.5 detik
});

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

function scrollSlider(direction) {
    const slider = document.getElementById("slider");
    const scrollAmount = 300;
    if (direction === 'left') {
        slider.scrollBy({
            left: -scrollAmount,
            behavior: 'smooth'
        });
    } else {
        slider.scrollBy({
            left: scrollAmount,
            behavior: 'smooth'
        });
    }
}

function showImageModal(src) {
    const modal = document.getElementById("imageModal");
    const img = document.getElementById("modalImage");
    modal.classList.remove('hidden');
    img.src = src;
}

// Optional: Klik gambar galeri event => zoom
document.querySelectorAll('.group img').forEach(img => {
    img.addEventListener('click', function() {
        document.getElementById("lightbox-img").src = this.src;
        document.getElementById("lightbox").classList.remove('hidden');
    });
});

document.querySelectorAll('#galeri img').forEach(img => {
    img.addEventListener('click', () => {
        document.getElementById('modalImage').src = img.src;
        document.getElementById('imageModal').classList.remove('hidden');
        document.getElementById('imageModal').classList.add('flex');
    });
});

document.getElementById('imageModal').addEventListener('click', () => {
    document.getElementById('imageModal').classList.remove('flex');
    document.getElementById('imageModal').classList.add('hidden');
});