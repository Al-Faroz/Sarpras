<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-info text-white me-2">
                    <i class="mdi mdi-qrcode-scan"></i>
                </span> Scan Aset (Stock Opname)
            </h3>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-7 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <h4 class="card-title mb-2">Scan Label QR Aset</h4>
                        <p class="text-muted small mb-3">
                            Arahkan kamera ke QR Code pada label aset.
                        </p>

                        <!-- Area video kamera: menggunakan div agar html5-qrcode merender dengan stabil -->
                        <div id="qr-box" style="position:relative; width:100%; max-width:420px; margin:0 auto;">
                            <div id="qr-video" style="width:100%; border-radius:12px; overflow:hidden; border:3px solid #17a2b8; background:#000;"></div>
                            
                            <!-- Overlay garis bidik -->
                            <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                                        width:200px;height:200px;border:3px solid #17e8b8;border-radius:8px;
                                        box-shadow:0 0 0 9999px rgba(0,0,0,0.35);pointer-events:none;z-index:10;"></div>
                        </div>

                        <!-- Feedback hasil scan -->
                        <div id="scan-result" class="alert alert-success mt-3 d-none">
                            <i class="mdi mdi-check-circle me-2"></i>
                            <strong>QR Terdeteksi!</strong> Mengalihkan halaman...
                        </div>

                        <!-- Status / error -->
                        <div id="scan-status" class="text-muted small mt-2"></div>

                        <!-- Tombol ganti kamera -->
                        <button id="btn-ganti-kamera" class="btn btn-outline-info btn-sm mt-2 d-none">
                            <i class="mdi mdi-camera-switch me-1"></i> Ganti Kamera
                        </button>

                        <!-- Fallback: upload foto QR -->
                        <div class="mt-3">
                            <p class="text-muted small mb-1">
                                <i class="mdi mdi-information-outline"></i>
                                Kamera tidak berfungsi? Upload foto QR dari galeri:
                            </p>
                            <input type="file" id="qr-file-input" accept="image/*"
                                   class="form-control form-control-sm" style="max-width:300px;margin:0 auto;">
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="container-fluid">
            <span class="text-muted small">Copyright &copy; <?= date('Y') ?> Portal Sarpras MTsN 4 Jombang.</span>
        </div>
    </footer>
</div>

<!-- Library -->
<script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
(function () {
    const statusEl   = document.getElementById('scan-status');
    const resultEl   = document.getElementById('scan-result');
    const btnGanti   = document.getElementById('btn-ganti-kamera');
    const fileInput  = document.getElementById('qr-file-input');

    // Inisialisasi langsung pada div 'qr-video'
    let qrScanner   = new Html5Qrcode("qr-video");
    let kameraList  = [];
    let kameraIdx   = 0;
    let sudahScan   = false;

    function onScanSuccess(decodedText) {
        if (sudahScan) return;
        sudahScan = true;
        qrScanner.stop().catch(() => {});
        resultEl.classList.remove('d-none');
        window.location.href = decodedText;
    }

    function mulaiKamera(deviceId) {
        const config = {
            fps: 10,
            qrbox: { width: 220, height: 220 },
            aspectRatio: 1.0,
            experimentalFeatures: { useBarCodeDetectorIfSupported: true }
        };

        const kameraConfig = deviceId 
            ? { deviceId: { exact: deviceId } } 
            : { facingMode: { ideal: "environment" } };

        statusEl.textContent = 'Memulai kamera...';
        
        qrScanner.start(kameraConfig, config, onScanSuccess, () => {})
            .then(() => statusEl.textContent = '')
            .catch(err => statusEl.textContent = '⚠ Gagal akses kamera: ' + err);
    }

    Html5Qrcode.getCameras().then(devices => {
        if (devices && devices.length > 0) {
            kameraList = devices;
            if (devices.length > 1) btnGanti.classList.remove('d-none');
            
            // Cari kamera belakang
            let targetIdx = devices.findIndex(d => 
                /back|rear|environment/i.test(d.label || '')
            );
            kameraIdx = targetIdx >= 0 ? targetIdx : devices.length - 1;
            mulaiKamera(devices[kameraIdx].id);
        }
    }).catch(() => mulaiKamera(null));

    btnGanti.addEventListener('click', () => {
        if (kameraList.length < 2) return;
        qrScanner.stop().then(() => {
            kameraIdx = (kameraIdx + 1) % kameraList.length;
            mulaiKamera(kameraList[kameraIdx].id);
        });
    });

    fileInput.addEventListener('change', e => {
        if (!e.target.files[0]) return;
        qrScanner.scanFile(e.target.files[0], true)
            .then(onScanSuccess)
            .catch(() => statusEl.textContent = '⚠ QR tidak terbaca.');
    });
})();
</script>
