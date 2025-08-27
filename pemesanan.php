<section id="form-pemesanan" class="scroll-mt-24">
    <div class="max-w-7xl mx-auto px-4 mt-16 pb-10">
        <h2 class="text-2xl font-bold text-gray-800 mb-8 text-center">Pemesanan</h2>
        <p class="text-center text-gray-600 mb-10">
            Untuk melakukan pemesanan, silakan isi form di bawah ini. Kami akan segera menghubungi Anda untuk konfirmasi.
        </p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Kiri: Accordion (biarkan punyamu yang existing) -->
            <div>
                <!-- Judul tanpa fungsi toggle -->
                <div
                    class="w-full flex justify-between items-center bg-orange-500 text-white font-semibold px-4 py-3 rounded-t-xl shadow-md">
                    <span>📄 Syarat & Ketentuan Sewa Sound System</span>
                </div>

                <!-- Konten langsung tampil -->
                <div
                    class="bg-white border border-orange-200 rounded-b-xl px-6 py-4 text-gray-700 space-y-4">
                    <ol class="list-decimal list-inside space-y-2">
                        <li>
                            Biaya sewa sound system di atas sudah termasuk:
                            <ul class="list-disc list-inside ml-5 text-gray-600">
                                <li>→ Pengiriman (Maros Kota)</li>
                                <li>→ Loading & unloading</li>
                            </ul>
                        </li>
                        <li>
                            Durasi sewa maksimal 6 jam/hari
                            <ul class="list-disc list-inside ml-5 text-gray-600">
                                <li>
                                    → Jika lebih dari 6 jam, dikenakan biaya overtime sebesar
                                    <strong>Rp200.000/jam</strong>
                                </li>
                            </ul>
                        </li>
                        <li>
                            Untuk booking, diperlukan <strong>DP 50%</strong> dari harga paket.
                            Pelunasan dilakukan setelah event selesai (hari yang sama).
                        </li>
                    </ol>
                </div>
            </div>


            <!-- Kanan: Form -->
            <div>
                <form id="form-pemesanan1" action="simpan_pemesanan.php" method="post" class="grid grid-cols-1 gap-6">
                    <div>
                        <label for="nama" class="block text-gray-700 font-semibold mb-2">Nama</label>
                        <input type="text" name="nama" id="nama"
                            class="text-sm sm:text-base w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            required>
                    </div>
                    <div>
                        <label for="no_hp" class="block text-gray-700 font-semibold mb-2">No HP</label>
                        <input type="text" name="no_hp" id="no_hp"
                            class="text-sm sm:text-base w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            required>
                    </div>
                    <div>
                        <label for="paket" class="block text-gray-700 font-semibold mb-2">Pilih Paket</label>
                        <select name="paket" id="paketcombo" class="w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            required>
                            <option value="">-- Pilih Paket --</option>
                            <?php
                            $stmt = $pdo->query("SELECT * FROM paket ORDER BY id DESC");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $row['id'] . '" 
                       data-harga="' . $row['harga_paket'] . '" 
                       data-nama="' . $row['nama_paket'] . '">'
                                    . $row['nama_paket'] . ' - Rp' . number_format($row['harga_paket'], 0, ',', '.') .
                                    '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div>
                        <label for="harga_tampil" class="block text-sm font-medium text-gray-700">Harga</label>
                        <input type="number" id="harga_tampil" name="harga" class="w-full p-2 border rounded">
                        <input type="hidden" id="harga">
                    </div>

                    <div>
                        <label for="tanggal_pesan" class="block text-gray-700 font-semibold mb-2">Tanggal Pemesanan</label>
                        <input type="date" name="tanggal_pesan" id="tanggal_pesan"
                            class="text-sm sm:text-base w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            required>
                    </div>
                    <div>
                        <label for="lokasi" class="block text-gray-700 font-semibold mb-2">Lokasi</label>
                        <input type="text" name="lokasi" id="lokasi"
                            class="text-sm sm:text-base w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            required>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-gray-700 font-semibold mb-2">Keterangan</label>
                        <textarea name="keterangan" id="keterangan"
                            class="text-sm sm:text-base w-full p-3 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                            rows="4"></textarea>
                    </div>

                    <div>
                        <button type="submit"
                            class="text-sm sm:text-base w-full py-3 bg-orange-500 hover:bg-orange-600 text-white font-semibold rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-orange-500">
                            Kirim Pemesanan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>