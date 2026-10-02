<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Scan Aset - Portal Sarpras MTsN 4 Jombang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    * { box-sizing: border-box; }
    body {
      background: linear-gradient(135deg, #e0f2fe 0%, #f0fdf4 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: system-ui, -apple-system, sans-serif;
      margin: 0;
      padding: 16px;
    }
    .card {
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 440px;
      padding: 28px 22px;
      text-align: center;
      background: #fff;
    }
    .icon-circle {
      width: 64px; height: 64px;
      background: linear-gradient(135deg,#0ea5e9,#38bdf8);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 16px;
      font-size: 1.8rem; color: #fff;
      box-shadow: 0 4px 16px rgba(14,165,233,0.3);
    }
    #qr-video {
      width: 100%;
      border-radius: 12px;
      border: 3px solid #0ea5e9;
      background: #000;
      display: block;
    }
    .viewfinder {
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      width: 190px; height: 190px;
      border: 3px solid #22d3ee;
      border-radius: 10px;
      box-shadow: 0 0 0 9999px rgba(0,0,0,0.38);
      pointer-events: none;
    }
    .hint-box {
      background: #f0f9ff;
      border: 1px solid #bae6fd;
      border-radius: 10px;
      padding: 10px 14px;
      font-size: 0.82rem;
      color: #0369a1;
      text-align: left;
      margin: 12px 0;
    }
    #scan-ok {
      display: none;
      background: #f0fdf4;
      border: 2px solid #86efac;
      border-radius: 12px;
      padding: 14px;
      margin-top: 12px;
      color: #166534;
      font-weight: 600;
    }
    #status-msg { font-size: 0.82rem; color: #666; margin-top: 8px; min-height: 20px; }
    .btn-back {
      width: 100%;
      border-radius: 12px;
      font-weight: 700;
      padding: 12px;
      margin-top: 14px;
    }
    .btn-ganti {
      border-radius: 10px;
      font-size: 0.82rem;
      margin-top: 8px;
    }
  </style>
</head>
<body>
  <div class="card">
    <div class="icon-circle">📷</div>
    <h5 class="fw-bold mb-1">Scan QR Code Aset</h5>
    <p class="text-muted small mb-3">Arahkan kamera ke stiker QR pada barang/fasilitas madrasah.</p>

    <!-- Video kamera -->
    <div style="position:relative;">
      <video id="qr-video" autoplay muted playsinline></video>
      <div class="viewfinder"></div>
    </div>

    <div id="scan-ok">✅ QR Terdeteksi! Mengalihkan...</div>
    <div id="status-msg"></div>

    <button id="btn-ganti" class="btn btn-outline-info btn-sm btn-ganti d-none">
      🔄 Ganti Kamera
    </button>

    <div class="hint-box">
      💡 <strong>Tips:</strong> Jaga jarak 15–25cm, pastikan cahaya cukup.
      QR Code harus berada di dalam kotak biru.
    </div>

    <!-- Fallback upload foto -->
    <div class="text-muted small mb-1">Kamera tidak berfungsi? Pilih foto QR dari galeri:</div>
    <input type="file" id="qr-file" accept="image/*" class="form-control form-control-sm mb-2">

    <a href="<?= base_url('/') ?>" class="btn btn-light btn-back">← Kembali ke Beranda</a>
  </div>

  <div id="qr-hidden" style="display:none;position:absolute;"></div>
  <div id="qr-file-hidden" style="display:none;position:absolute;"></div>

  <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
  <script>
  (function(){
    const video    = document.getElementById('qr-video');
    const statusEl = document.getElementById('status-msg');
    const okEl     = document.getElementById('scan-ok');
    const btnGanti = document.getElementById('btn-ganti');
    const fileInput= document.getElementById('qr-file');

    let scanner   = null;
    let cameras   = [];
    let camIdx    = 0;
    let done      = false;

    function setStatus(msg){ statusEl.textContent = msg; }

    function onSuccess(text){
      if(done) return;
      done = true;
      if(scanner) scanner.stop().catch(()=>{});
      okEl.style.display = 'block';
      setStatus('');
      try {
        const ac = new (window.AudioContext||window.webkitAudioContext)();
        const o  = ac.createOscillator();
        o.connect(ac.destination);
        o.frequency.value = 880;
        o.start(); o.stop(ac.currentTime + 0.12);
      } catch(e){}
      setTimeout(()=>{ window.location.href = text; }, 700);
    }

    function startCamera(deviceId){
      if(scanner) scanner.stop().catch(()=>{});
      scanner = new Html5Qrcode('qr-hidden');
      const cfg = { fps:10, qrbox:{width:200,height:200}, aspectRatio:1.0,
                    experimentalFeatures:{ useBarCodeDetectorIfSupported:true } };
      const cam = deviceId
        ? { deviceId: { exact: deviceId } }
        : { facingMode: { ideal: 'environment' } }; // ideal bukan exact → kompatibel Android lama

      setStatus('Memulai kamera...');
      scanner.start(cam, cfg, onSuccess, ()=>{})
        .then(()=>{
          setStatus('');
          // inject stream ke video kita
          const v = document.querySelector('#qr-hidden video');
          if(v) video.srcObject = v.srcObject;
        })
        .catch(err=>{
          setStatus('⚠ Kamera gagal: ' + err);
        });
    }

    function init(){
      Html5Qrcode.getCameras()
        .then(devs=>{
          if(!devs||!devs.length){ setStatus('⚠ Tidak ada kamera.'); return; }
          cameras = devs;
          if(devs.length > 1) btnGanti.classList.remove('d-none');

          // Cari kamera belakang
          let idx = devs.length - 1;
          devs.forEach((d,i)=>{
            const l = (d.label||'').toLowerCase();
            if(l.includes('back')||l.includes('rear')||l.includes('environment')) idx=i;
          });
          camIdx = idx;
          startCamera(devs[camIdx].id);
        })
        .catch(()=>{
          // getCameras gagal → coba langsung tanpa deviceId
          startCamera(null);
        });
    }

    btnGanti.addEventListener('click',()=>{
      if(cameras.length < 2) return;
      done = false;
      camIdx = (camIdx+1) % cameras.length;
      startCamera(cameras[camIdx].id);
    });

    fileInput.addEventListener('change',e=>{
      const f = e.target.files[0];
      if(!f) return;
      const s = new Html5Qrcode('qr-file-hidden');
      s.scanFile(f, true)
        .then(onSuccess)
        .catch(()=> setStatus('⚠ QR tidak terbaca. Coba foto lebih jelas.'));
    });

    if(typeof Html5Qrcode !== 'undefined'){
      init();
    } else {
      setStatus('⚠ Library gagal dimuat. Periksa koneksi.');
    }
  })();
  </script>
</body>
</html>