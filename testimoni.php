  <section id="testimoni" class="scroll-mt-24">
      <div class="row">
          <div class="col">
              <h2 class="text-2xl font-bold text-gray-800 mt-16 mb-8 text-center">Testimoni Pelanggan</h2>

              <div class="flex overflow-x-auto space-x-4 snap-x snap-mandatory px-4 pb-4 scroll-smooth">
                  <?php
                    $pdo = connect_db(); // <--- gunakan koneksi PDO yang benar
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
                        // $imgUrl = "https://api.dicebear.com/7.x/micah/svg?seed=" . urlencode($nama);

                        echo '<div class="bg-white p-4 rounded-lg shadow-md w-60 snap-start flex-shrink-0 relative">';

                        // Bungkus avatar + nama dalam flex row
                        echo '<div class="flex items-center justify-between mb-2">';
                        echo '<h3 class="text-base font-semibold text-gray-800">' . $nama . '</h3>';
                        echo '<img src="' . $imgUrl . '" alt="Avatar" class="w-10 h-10 rounded-full">';
                        echo '</div>';

                        echo '<p class="text-sm text-gray-600 mt-1 text-center">' . $pesan . '</p>';

                        // Bintang rating
                        echo '<div class="flex justify-center mt-3">';
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $rating
                                ? '<span class="text-yellow-400 text-xl">&#9733;</span>'
                                : '<span class="text-gray-300 text-xl">&#9733;</span>';
                        }
                        echo '</div>';

                        echo '<p class="text-gray-400 text-xs mt-2 text-center">' . $tanggal . '</p>';
                        echo '</div>';
                    }
                    ?>
              </div>
          </div>
      </div>
  </section>