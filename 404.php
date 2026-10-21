<?php http_response_code(404); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>404 • Halaman Nggak Ketemu | Cimohot</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    body{min-height:100vh;display:flex;align-items:center;justify-content:center;background:#0D0D0D;font-family:"Plus Jakarta Sans",sans-serif;color:#FAFAFA;text-align:center;padding:24px}
    .err-box{max-width:480px}
    .err-icon{font-size:4.5rem;color:#FF3D2E;margin-bottom:18px;display:inline-block;animation:bounce 2s ease-in-out infinite}
    @keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-12px)}}
    .err-code{font-size:1rem;font-weight:700;color:#F5A623;text-transform:uppercase;letter-spacing:2px;margin-bottom:12px}
    h1{font-size:1.8rem;font-weight:800;margin-bottom:12px;line-height:1.2}
    .err-text{color:#9CA3AF;font-size:0.95rem;line-height:1.65;margin-bottom:32px}
    .err-actions{display:flex;gap:14px;justify-content:center;flex-wrap:wrap}
    .btn{display:inline-flex;align-items:center;gap:8px;padding:12px 24px;border-radius:9999px;font-weight:700;font-size:0.9rem;transition:all 0.2s ease;text-decoration:none}
    .btn-primary{background:#FF3D2E;color:#fff}
    .btn-primary:hover{background:#e02d1e;box-shadow:0 0 20px rgba(255,61,46,0.4);transform:translateY(-2px)}
    .btn-secondary{background:#1F1F1F;color:#FAFAFA;border:1px solid #2A2A2A}
    .btn-secondary:hover{background:#2A2A2A;transform:translateY(-2px)}
    .brand{margin-top:48px;font-size:0.8rem;color:#4B5563}
    .brand span{color:#F5A623;font-weight:700}
  </style>
</head>
<body>
  <div class="err-box">
    <div class="err-icon"><ion-icon name="flame"></ion-icon></div>
    <p class="err-code">Error 404</p>
    <h1>Waduh, cirengnya nyasar nih!</h1>
    <p class="err-text">
      Halaman yang kamu cari nggak ada di sini. Mungkin udah dipindah atau emang nggak pernah ada.
      Tapi tenang, cireng montoknya masih ada kok!
    </p>
    <div class="err-actions">
      <a href="/" class="btn btn-primary"><ion-icon name="home-outline"></ion-icon> Balik ke Beranda</a>
      <a href="/#food-menu" class="btn btn-secondary"><ion-icon name="restaurant-outline"></ion-icon> Lihat Menu</a>
    </div>
    <p class="brand"><span>Cimohot</span> &middot; Cireng Isi Montok Hot</p>
  </div>
</body>
</html>
