<?php include '../config/database.php'; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Author: A.N. Author,
    Illustrator: P. Picture, Category: Books, Price: $17.99,
    Length: 784 pages">
    <title>Kalender Event - ABmusic</title>
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Mobile-card layout mirip edit_event */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f5f6fa;
        }


        /* Container agar terlihat seperti layar HP */
        .mobile-container {
            max-width: 800px;
            /* lebar seperti HP modern */
            margin: 18px auto;
            /* center */
            padding: 12px;
        }

        /* Card style */
        .card-calendar {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
            overflow: hidden;
            border: 0;
        }

        /* Header kecil ala aplikasi mobile */
        .card-calendar .card-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: linear-gradient(90deg, #ff7a18, #ffb199);
            color: #fff;
        }

        .card-calendar .card-header h3 {
            margin: 0;
            font-size: 16px;
        }

        /* Isian kalender */
        .calendar-wrapper {
            padding: 12px;
        }

        /* Make FullCalendar fill the card area */
        #calendar {
            width: 100%;
            border-radius: 10px;
        }

        /* Buat event title/badges lebih rapat */
        .fc .fc-daygrid-event {
            padding: 6px 8px;
            font-size: 0.85rem;
        }

        /* Responsive tweaks */
        @media (max-width: 520px) {
            .mobile-container {
                padding: 8px;
            }

            .card-calendar .card-header h3 {
                font-size: 15px;
            }
        }
    </style>
</head>

<body class="p-4">

    <?php if (isset($_GET['msg'])): ?>
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
            <div id="liveToast" class="toast align-items-center text-white 
             <?php echo ($_GET['msg'] == 'updated') ? 'bg-success' : 'bg-danger'; ?> 
             border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <?php if ($_GET['msg'] == 'updated'): ?>
                            ✅ Data berhasil diupdate!
                        <?php else: ?>
                            ❌ Terjadi kesalahan saat update!
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

        <script>
            var toastLiveExample = document.getElementById('liveToast')
            var toast = new bootstrap.Toast(toastLiveExample)
            toast.show()
        </script>
    <?php endif; ?>

    <div class="row align-items-center">
        <!-- di mobile tampil dulu (order-1), di desktop tampil kiri (order-md-1) -->
        <div class="col-12 col-md-4 order-1 order-md-1 text-center mb-3 mb-md-0">
            <img src="/assets/img/ABmusic-Logo.png" class="img-fluid" style="max-height:180px;">
        </div>

        <div class="col-12 col-md-8 order-2 order-md-2">
            <div class="mobile-container">
                <div class="card card-calendar">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="mb-0">📅 Kalender Event</h3>
                    </div>
                    <div class="card-body p-2">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    $pdo = connect_db();
    $sql = "
    SELECT p.id, p.nama, p.lokasi, p.tanggal_pesan, p.tarif, p.keterangan,
           COALESCE(SUM(b.nominal),0) as total_bayar,
           CASE 
                WHEN COALESCE(SUM(b.nominal),0) >= p.tarif THEN 'Lunas'
                WHEN COALESCE(SUM(b.nominal),0) = 0 THEN 'Belum Bayar'
                ELSE 'Panjar'
           END as status_pembayaran
    FROM pemesanan p
    LEFT JOIN pembayaran b ON b.pemesanan_id = p.id
    GROUP BY p.id, p.nama, p.lokasi, p.tanggal_pesan, p.tarif
    ORDER BY p.tanggal_pesan DESC";
    $stmt = $pdo->query($sql);
    $events = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $color = ($row['status_pembayaran'] === 'Lunas') ? '#28a745' : '#ffc107';
        $events[] = [
            'id'    => $row['id'], // pastikan ada kolom id
            'title' => $row['nama'] . " (" . $row['status_pembayaran'] . ")",
            'start' => $row['tanggal_pesan'],
            'color' => $color,
            'extendedProps' => [
                'id_pemesanan' => $row['id'],
                'lokasi'     => $row['lokasi'],
                'tarif' => number_format($row['tarif']),
                'deskripsi'  => $row['keterangan'] ?? '-',
                'total_bayar' => number_format($row['total_bayar']),
                'status_pembayaran' => $row['status_pembayaran']
            ]
        ];
    }
    ?>

    <!-- Modal Bootstrap untuk detail event -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content shadow-lg border-0 rounded-4">
                <div class="modal-header bg-primary text-white rounded-top-4">
                    <h5 class="modal-title"><i class="bi bi-calendar-event"></i> Detail Event</h5>
                    <div class="ms-auto d-flex gap-2">
                        <!-- Tombol Edit -->
                        <button id="btnEditEvent" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <!-- Tombol Hapus -->
                        <button id="btnDeleteEvent" class="btn btn-danger btn-sm">
                            <i class="bi bi-trash"></i> Hapus
                        </button>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                <div class="modal-body">
                    <!-- Informasi Event -->
                    <div class="card mb-3 border-0 shadow-sm">
                        <div class="card-body">
                            <p><strong>📛 Nama:</strong> <span id="eventTitle"></span></p>
                            <p><strong>📅 Tanggal:</strong> <span id="eventDate"></span></p>
                            <p><strong>📍 Lokasi:</strong> <span id="eventLokasi"></span></p>
                            <p><strong>📝 Deskripsi:</strong> <span id="eventDeskripsi"></span></p>
                            <p><strong>💰 Tarif:</strong> Rp <span id="eventTarif"></span></p>
                            <p><strong>💳 Total Bayar:</strong> Rp <span id="eventTotalBayar"></span></p>
                            <p><strong>✅ Status:</strong> <span id="eventStatus" class="badge bg-success"></span></p>
                        </div>
                    </div>

                    <!-- Galeri -->
                    <div class="card border-0 shadow-sm mt-3">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-images"></i> Upload Gambar / Video Event</h6>

                            <div class="row align-items-center">
                                <!-- Gambar / Video Preview -->
                                <div class="col-md-12">
                                    <div id="galeriPreview" class="d-flex flex-wrap gap-2"></div>
                                    <div id="previewImages" class="d-flex flex-wrap gap-2"></div>
                                </div>

                                <!-- Input File + Tombol Upload -->
                                <div class="col-md-12">
                                    <form id="uploadForm" enctype="multipart/form-data" class="d-flex gap-2">
                                        <input type="hidden" name="event_id" id="event_id">
                                        <input type="file" name="images[]" id="eventImages"
                                            class="form-control" multiple accept="image/*,video/*">
                                        <button type="submit" class="btn btn-success">
                                            <i class="bi bi-upload"></i> Upload
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Riwayat Pembayaran -->
                    <div class="card mt-4 border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="mb-3"><i class="bi bi-cash-stack"></i> Riwayat Pembayaran</h6>
                            <table class="table table-bordered table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Jumlah</th>
                                        <th>Metode</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentHistory">
                                    <!-- Data pembayaran via JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
        <div id="uploadToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    Upload selesai! 🎉
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="mediaModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body" id="mediaPreview"></div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'id', // bahasa Indonesia
                events: <?php echo json_encode($events); ?>,
                eventClick: function(info) {
                    let eventObj = info.event;

                    // Isi modal dengan detail event
                    document.getElementById('eventTitle').textContent = info.event.title;
                    document.getElementById('eventDate').textContent = info.event.start.toLocaleDateString('id-ID');
                    document.getElementById('eventLokasi').textContent = info.event.extendedProps.lokasi;
                    document.getElementById('eventDeskripsi').textContent = info.event.extendedProps.deskripsi;
                    document.getElementById('eventTarif').textContent = info.event.extendedProps.tarif;
                    document.getElementById('eventTotalBayar').textContent = info.event.extendedProps.total_bayar;
                    document.getElementById('eventStatus').textContent = info.event.extendedProps.status_pembayaran;

                    // Status pembayaran dengan badge warna
                    let status = info.event.extendedProps.status_pembayaran;
                    let statusSpan = document.getElementById('eventStatus');
                    statusSpan.textContent = status;
                    if (status === "Lunas") {
                        statusSpan.className = "badge bg-success";
                    } else {
                        statusSpan.className = "badge bg-warning text-dark";
                    }

                    // 👉 set hidden input event_id untuk upload gambar
                    document.getElementById("event_id").value = eventObj.extendedProps.id_pemesanan;

                    // 👉 load galeri berdasarkan id_pemesanan
                    loadGaleri(eventObj.extendedProps.id_pemesanan);
                    loadPaymentHistory(eventObj.extendedProps.id_pemesanan);
                    // loadGallery(eventId);
                    // Tampilkan modal
                    var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                    modal.show();
                }
            });
            calendar.render();
        });

        function openEventModal(event) {
            document.getElementById("event_id_hidden").value = event.id;
        }
        // Preview gambar sebelum upload
        document.getElementById("eventImages").addEventListener("change", function(e) {
            const preview = document.getElementById("previewImages");
            preview.innerHTML = "";
            [...e.target.files].forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    img.classList.add("rounded", "shadow", "img-thumbnail");
                    img.style.width = "120px";
                    img.style.height = "100px";
                    preview.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        });

        // Upload AJAX
        document.getElementById("uploadForm").addEventListener("submit", function(e) {
            e.preventDefault();
            const formData = new FormData(this);

            fetch("upload_galeri.php", {
                    method: "POST",
                    body: formData
                })
                .then(res => res.json())
                .then(data => {
                    document.getElementById("eventImages").value = "";
                    document.getElementById("previewImages").innerHTML = "";
                    // Ambil event_id dari hidden input (misal ada input hidden di form)
                    const event_id = document.getElementById("event_id").value;

                    // Reload galeri agar gambar baru tampil
                    loadGaleri(event_id);

                    // ✅ Tampilkan Toast
                    const toastEl = document.getElementById("uploadToast");
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                })
                .catch(err => alert("Error: " + err));
        });

        function loadGaleri(event_id) {
            fetch("get_galeri.php?event_id=" + event_id)
                .then(res => res.json())
                .then(data => {
                    const galeriPreview = document.getElementById("galeriPreview");
                    galeriPreview.innerHTML = "";

                    data.forEach(item => {
                        let el;
                        if (item.type === "image") {
                            el = `<img src="../assets/galeri/${item.file_name}" class="img-thumbnail m-1" style="width:120px;cursor:pointer" onclick="showMedia('../assets/galeri/${item.file_name}','image')">`;
                        } else {
                            el = `<video src="../assets/galeri/${item.file_name}" class="img-thumbnail m-1" style="width:120px;cursor:pointer" onclick="showMedia('${item.file_name}','video')"></video>`;
                        }
                        galeriPreview.innerHTML += el;
                    });
                });
        }

        // Modal untuk preview
        function showMedia(path, type) {
            const modalBody = document.getElementById("mediaPreview");
            if (type === "image") {
                modalBody.innerHTML = `<img src="${path}" class="img-fluid">`;
            } else {
                modalBody.innerHTML = `<video src="${path}" class="w-100" controls autoplay></video>`;
            }
            new bootstrap.Modal(document.getElementById("mediaModal")).show();
        }


        // Tombol Edit Event
        document.getElementById('btnEditEvent').addEventListener('click', function() {
            const eventId = document.getElementById('event_id').value;
            // arahkan ke halaman edit
            window.location.href = "edit_event.php?id=" + eventId;
        });

        // Tombol Hapus Event
        document.getElementById('btnDeleteEvent').addEventListener('click', function() {
            const eventId = document.getElementById('event_id').value;
            if (confirm("Yakin ingin hapus event ini?")) {
                fetch("delete_event.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "id=" + eventId
                    })
                    .then(res => res.text())
                    .then(res => {
                        alert("Event berhasil dihapus");
                        location.reload();
                    });
            }
        });
        // Load riwayat pembayaran via AJAX
        function loadPaymentHistory(eventId) {
            fetch("get_payment_history.php?event_id=" + eventId)
                .then(res => res.json())
                .then(data => {
                    let html = "";
                    data.forEach(item => {
                        html += `
          <tr>
            <td>${item.created_at}</td>
            <td>${item.nominal}</td>
            <td>${item.jenis}</td>
            <td><span class="badge bg-${item.status == 'success' ? 'success':'warning'}">${item.status}</span></td>
          </tr>
        `;
                    });
                    document.getElementById("paymentHistory").innerHTML = html;
                });
        }
    </script>
</body>

</html>