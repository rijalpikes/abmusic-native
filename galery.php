<!-- Galeri Event Otomatis dari Folder -->
<section id="galeri" class="flex items-center justify-center bg-gray-50 scroll-mt-24">
    <div class="text-center">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Galeri Acara</h2>

        <!-- Scrollable Gallery -->
        <div class="overflow-y-auto max-h-[600px] border rounded-lg p-4 bg-white shadow-md">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php
                $folder = 'assets/galeri/';
                $files = array_diff(scandir($folder), array('.', '..'));

                foreach ($files as $file) {
                    $path = $folder . $file;
                    if (is_file($path) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                        echo '
                                <div class="relative overflow-hidden rounded-lg shadow-lg transform hover:-translate-y-2 hover:shadow-2xl transition duration-300 cursor-pointer group">
                                    <img src="' . $path . '" alt="" class="w-full h-32 object-cover rounded-lg">
                                </div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
    <!-- Lightbox Modal -->
    <div id="lightbox" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <img id="lightbox-img" src="" alt="Preview" class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg">
        <button onclick="closeLightbox()"
            class="absolute top-4 right-4 text-white text-3xl font-bold">&times;</button>
    </div>
    <!-- Modal (klik gambar untuk zoom) -->
    <div id="imageModal"
        class="fixed inset-0 bg-black bg-opacity-80 hidden items-center justify-center z-50 cursor-pointer">
        <img id="modalImage" class="max-w-3xl w-full rounded-xl">
    </div>
</section>