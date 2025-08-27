<section id="paket" class="py-10 bg-gray-100">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-orange-600 mb-8">Paket Sound System ABmusic</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $pdo = connect_db();
            $stmt = $pdo->query("SELECT * FROM paket ORDER BY id DESC");
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) { ?>
                <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-orange-500 mb-2"><?= htmlspecialchars($row['nama_paket']) ?></h3>
                        <p class="text-gray-700 font-bold mb-2">
                            Rp <?= number_format((float)$row['harga_paket'], 0, ',', '.') ?>
                        </p>
                        <ul class="text-gray-600 list-disc list-inside mb-4">
                            <?php foreach (explode(",", (string)$row['deskripsi']) as $item): ?>
                                <li><?= htmlspecialchars(trim($item)) ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <button
                            onclick="pilihPaket('<?= (int)$row['id'] ?>','<?= htmlspecialchars($row['nama_paket'], ENT_QUOTES) ?>','<?= (float)$row['harga_paket'] ?>')"
                            class="bg-orange-500 hover:bg-orange-600 text-white font-bold py-2 px-4 rounded">
                            Pilih Paket Ini
                        </button>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</section>