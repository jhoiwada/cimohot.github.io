<?php session_start(); ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cimohot | Cireng Isi Montok Hot • No.1 Jajan Pedas Jakarta</title>
  <meta name="description" content="Dari dapur Marilyn sejak 2021, cireng isi montok krispi dengan daging berlimpah, cabai rawit segar, dan 6 level kepedasan. Lebih dari 15.000 porsi terjual. Order online sekarang!" />
  <meta name="keywords" content="cireng isi pedas, cireng montok, cireng ayam pedas, cimohot, cireng koja jakarta utara, jajanan pedas jakarta, kuliner pedas jakarta, cireng mercon, order cireng online" />
  <meta name="author" content="Cimohot" />
  <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1" />
  <link rel="canonical" href="https://cimohot.web.id/" />

  <!-- Open Graph (Social Sharing) -->
  <meta property="og:type" content="restaurant" />
  <meta property="og:title" content="Cimohot | Cireng Isi Montok Hot • No.1 Jajan Pedas Jakarta" />
  <meta property="og:description" content="Dari dapur Marilyn sejak 2021, cireng isi montok krispi dengan daging berlimpah & cabai rawit segar. 15.000+ porsi terjual, 4.9★ rating." />
  <meta property="og:image" content="https://cimohot.web.id/assets/images/menu/cireng-ayam-pedas.jpg" />
  <meta property="og:url" content="https://cimohot.web.id/" />
  <meta property="og:site_name" content="Cimohot" />
  <meta property="og:locale" content="id_ID" />
  <meta name="twitter:card" content="summary_large_image" />
  <meta name="twitter:title" content="Cimohot | Cireng Montok Pedas No.1 Jakarta" />
  <meta name="twitter:description" content="Cireng isi montok krispi: Pedas, gurih, dan nagih sejak 2021." />
  <meta name="twitter:image" content="https://cimohot.web.id/assets/images/menu/cireng-ayam-pedas.jpg" />

  <!-- JSON-LD Structured Data -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "FoodEstablishment",
    "name": "Cimohot • Cireng Isi Montok Hot",
    "description": "Pelopor cireng isi montok krispi premium. Tersedia 6 level kepedasan, aneka isian daging, keju, dan bakso. Buka setiap hari.",
    "url": "https://cimohot.web.id",
    "telephone": "+6285930194086",
    "image": "https://cimohot.web.id/assets/images/menu/cireng-ayam-pedas.jpg",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "Jl. Muncang Raya No. 55",
      "addressLocality": "Koja",
      "addressRegion": "Jakarta Utara",
      "postalCode": "14220",
      "addressCountry": "ID"
    },
    "geo": {
      "@type": "GeoCoordinates",
      "latitude": -6.1221,
      "longitude": 106.9105
    },
    "openingHoursSpecification": {
      "@type": "OpeningHoursSpecification",
      "dayOfWeek": ["Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday"],
      "opens": "09:00",
      "closes": "21:00"
    },
    "servesCuisine": "Indonesian Street Food",
    "priceRange": "Rp 3.500 - Rp 22.000",
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "4.9",
      "reviewCount": "2400"
    }
  }
  </script>

  <!-- Favicon -->
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon-32x32.png?v=2" />
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon-16x16.png?v=2" />
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/apple-touch-icon.png?v=2" />
  <link rel="shortcut icon" href="favicon.ico?v=2" type="image/x-icon" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link rel="dns-prefetch" href="https://unpkg.com" />
  <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@500;700&family=Inter:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

  <!-- SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Core CSS -->
  <link rel="stylesheet" href="assets/css/main.css?v=<?= time(); ?>" />

  <!-- Preload Hero Image -->
  <link rel="preload" as="image" href="assets/images/menu/cireng-ayam-pedas.jpg" />
</head>

<body id="top">

  <?php require_once __DIR__ . '/_admin_bar.php'; ?>

  <!-- Scroll Progress Bar -->
  <div class="scroll-progress" id="scrollProgress"></div>

  <!-- ===================================
       ANNOUNCEMENT TOP BAR
  ==================================== -->
  <div class="announcement-bar" id="topAnnouncementBar" style="display:none;">
    <div class="container announcement-content">
      <span class="badge-fire"><ion-icon name="flame"></ion-icon> SPESIAL</span>
      <span class="announcement-text">Beli Paket Combo Cimohot Lebih Hemat 20% • Cireng Montok No. 1 Sejak 2021!</span>
      <a href="#food-menu" class="announcement-link">Pesan Sekarang →</a>
      <button type="button" class="announcement-close-btn" id="closeAnnouncementBtn" aria-label="Tutup Pengumuman" title="Tutup Pengumuman">&times;</button>
    </div>
  </div>
  <script>
    (function() {
      try {
        localStorage.removeItem('cimohot_hide_announcement');
        sessionStorage.removeItem('cimohot_hide_announcement');
      } catch(e) {}
      document.body.classList.remove('no-announcement');
      var bar = document.getElementById('topAnnouncementBar');
      if (bar) bar.style.display = 'flex';
    })();
  </script>

  <!-- ===================================
       HEADER & NAVIGATION
  ==================================== -->
  <header class="header" data-header>
    <div class="container header-container">

      <!-- Brand Logo -->
      <a href="index.php" class="logo">
        <img src="assets/images/logo cimohot.png" alt="Cimohot Logo" class="logo-img" />
        <div class="logo-text">
          <span class="logo-brand">Cimohot<span>.</span></span>
          <span class="logo-tagline">Hot & Montok</span>
        </div>
      </a>

      <!-- Desktop Navbar -->
      <nav class="navbar" data-navbar>
        <ul class="navbar-list">
          <li class="nav-item"><a href="#hero" class="navbar-link" data-nav-link>Beranda</a></li>
          <li class="nav-item"><a href="#food-menu" class="navbar-link" data-nav-link>Menu</a></li>
          <li class="nav-item"><a href="#spice-simulator" class="navbar-link" data-nav-link><ion-icon name="flame" class="nav-hot-icon" style="color:var(--primary-red); vertical-align:-2px; margin-right:3px;"></ion-icon>Panduan Pedas</a></li>
          <li class="nav-item" id="nav-about-link"><a href="#about" class="navbar-link" data-nav-link>Cerita Kami</a></li>
          <li class="nav-item"><a href="#testimonials" class="navbar-link" data-nav-link>Ulasan</a></li>
          <li class="nav-item"><a href="#blog" class="navbar-link" data-nav-link>Artikel</a></li>
          <li class="nav-item"><a href="#contact" class="navbar-link" data-nav-link>Kontak</a></li>
        </ul>
      </nav>

      <!-- Header Button Group -->
      <div class="header-btn-group">
        <button id="cartBtn" class="cart-btn" aria-label="Buka Keranjang Belanja">
          <ion-icon name="cart-outline"></ion-icon>
          <span class="cart-badge">0</span>
        </button>

        <div id="loginStatusContainer" class="login-status-container">
          <button class="login-btn" onclick="window.location.href='login/login.php'">
            <ion-icon name="person-outline"></ion-icon> Masuk
          </button>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="nav-toggle-btn" aria-label="Toggle menu" data-menu-toggle-btn>
          <span class="line top"></span>
          <span class="line middle"></span>
          <span class="line bottom"></span>
        </button>
      </div>

    </div>
  </header>

  <main>
    <article>

      <!-- ===================================
           HERO : Cinematic Food Reveal
      ==================================== -->
      <section class="hero" id="hero">
        <!-- Background -->
        <div class="hero-bg">
          <img src="assets/images/menu/cireng-ayam-pedas.jpg" alt="" class="hero-bg-img" aria-hidden="true" />
          <div class="hero-bg-overlay"></div>
        </div>

        <!-- Ember particles (CSS only) -->
        <div class="hero-embers" aria-hidden="true">
          <div class="ember"></div>
          <div class="ember"></div>
          <div class="ember"></div>
          <div class="ember"></div>
          <div class="ember"></div>
          <div class="ember"></div>
          <div class="ember"></div>
          <div class="ember"></div>
        </div>

        <!-- Content -->
        <div class="hero-content">
          <div class="hero-eyebrow">
            <ion-icon name="flame"></ion-icon>
            <span>Pelopor Cireng Isi Montok No. 1 di Jakarta</span>
          </div>

          <h1 class="hero-title">
            <?php
            if (isset($_SESSION['name'])) {
                $firstName = explode(' ', htmlspecialchars($_SESSION['name']))[0];
                echo "Halo, " . $firstName . "! <br><span class='text-gradient'>Siap Nyemil Lagi?</span>";
            } else {
                echo "Cireng Isi Montok, <br><span class='text-gradient'>Pedasnya Nampol!</span>";
            }
            ?>
          </h1>

          <p class="hero-subtitle">
            Dari dapur keluarga Marilyn sejak 2021, cireng isi daging yang tiap gigitannya bikin nangis bahagia. Bukan lebay, ini emang seenak itu.
          </p>

          <div class="hero-actions">
            <a href="#food-menu" class="btn btn-primary" id="hero-order-btn">
              <ion-icon name="restaurant-outline"></ion-icon> Lihat Menu Sekarang
            </a>
            <a href="https://wa.me/6285930194086?text=Halo%20Cimohot!%20Saya%20mau%20pesan%20cireng%20isi%20montok%20dong!" target="_blank" class="btn btn-wa" rel="noopener" id="hero-wa-btn">
              <ion-icon name="logo-whatsapp"></ion-icon> Order via WA
            </a>
          </div>

          <!-- Stats -->
          <div class="hero-stats">
            <div class="stat-item">
              <span class="stat-num" data-count="15000">0<span>+</span></span>
              <span class="stat-label">Porsi Terjual</span>
            </div>
            <div class="stat-item">
              <span class="stat-num" data-count="4.9">0<span>★</span></span>
              <span class="stat-label">2.400+ Ulasan</span>
            </div>
            <div class="stat-item">
              <span class="stat-num" data-count="100">0<span>%</span></span>
              <span class="stat-label">Halal & Higienis</span>
            </div>
          </div>
        </div>

        <!-- Scroll indicator -->
        <div class="hero-scroll-indicator">
          <span>Scroll</span>
          <ion-icon name="chevron-down-outline"></ion-icon>
        </div>
      </section>

      <!-- ===================================
           LIVE ORDER SOCIAL PROOF TICKER
      ==================================== -->
      <section class="live-activity-section" aria-label="Aktivitas Pemesanan Terkini">
        <div class="container">
          <div class="live-activity-pill" id="liveTickerBar">
            <div class="live-pill-status">
              <span class="live-pulse-dot"></span>
              <span class="live-pill-tag">LIVE</span>
            </div>
            <div class="live-pill-divider"></div>
            <div class="live-pill-content">
              <ion-icon name="bag-check-outline" class="live-pill-icon"></ion-icon>
              <span class="live-ticker-text" id="liveTickerText">Memuat pesanan terkini...</span>
            </div>
            <span class="live-pill-badge">Real-time</span>
          </div>
        </div>
      </section>

      <!-- ===================================
           FOOD MENU : Menu Gallery
      ==================================== -->
      <section class="section food-menu" id="food-menu">
        <div class="container">

          <div class="section-header reveal">
            <span class="section-badge">KATALOG MENU</span>
            <h2 class="section-title">Menu Favorit <span class="text-gradient">Cimohot</span></h2>
            <p class="section-text">
              Dibuat fresh setiap hari dengan bahan berkualitas premium dan rempah alami tanpa pengawet.
            </p>
          </div>

          <!-- Menu Toolbar -->
          <div class="menu-toolbar reveal">
            <div class="menu-search-wrapper">
              <ion-icon name="search-outline" class="search-icon-left"></ion-icon>
              <input type="text" id="menuSearchInput" class="search-input-box" placeholder="Cari menu cireng, keju, atau minuman..." autocomplete="off" />
              <button id="clearSearchBtn" class="clear-search-btn" title="Hapus pencarian">&times;</button>
            </div>

            <ul class="filter-list">
              <li><button class="filter-btn active" data-filter="all"><ion-icon name="grid-outline"></ion-icon> Semua</button></li>
              <li><button class="filter-btn" data-filter="pedas"><ion-icon name="flame"></ion-icon> Pedas</button></li>
              <li><button class="filter-btn" data-filter="original"><ion-icon name="sparkles-outline"></ion-icon> Original</button></li>
              <li><button class="filter-btn" data-filter="paket hemat"><ion-icon name="gift-outline"></ion-icon> Paket</button></li>
              <li><button class="filter-btn" data-filter="minuman"><ion-icon name="wine-outline"></ion-icon> Minuman</button></li>
            </ul>
          </div>

          <!-- Menu Grid -->
          <ul class="food-menu-list">
            <!-- Dynamically populated by main.js -->
          </ul>

        </div>
      </section>

      <!-- ===================================
           SPICE LEVEL SIMULATOR
      ==================================== -->
      <section class="section spice-simulator-section" id="spice-simulator">
        <div class="container">

          <div class="section-header reveal">
            <span class="section-badge" style="background:rgba(255,61,46,0.12); color:#FF3D2E; border-color:rgba(255,61,46,0.25);">
              <ion-icon name="flame"></ion-icon> PANDUAN RASA INTERAKTIF
            </span>
            <h2 class="section-title">Cimohot <span class="text-gradient">Spice Simulator</span></h2>
            <p class="section-text">
              Berapa batas ketahanan pedasmu? Pilih level kepedasan dan temukan rekomendasi cireng yang paling pas untuk lidahmu!
            </p>
          </div>

          <div class="simulator-box reveal">
            <div class="spice-levels-tabs">
              <button class="spice-step-btn" data-level="0">
                <span class="spice-chili-icons"><ion-icon name="shield-checkmark-outline"></ion-icon></span>
                <span class="spice-step-title">Level 0</span>
                <span class="spice-step-tag">Manja</span>
              </button>
              <button class="spice-step-btn" data-level="1">
                <span class="spice-chili-icons"><ion-icon name="flame" style="color:#F5A623;"></ion-icon></span>
                <span class="spice-step-title">Level 1</span>
                <span class="spice-step-tag">Anget</span>
              </button>
              <button class="spice-step-btn" data-level="2">
                <span class="spice-chili-icons"><ion-icon name="flame" style="color:#FB923C;"></ion-icon><ion-icon name="flame" style="color:#FB923C;"></ion-icon></span>
                <span class="spice-step-title">Level 2</span>
                <span class="spice-step-tag">Standard</span>
              </button>
              <button class="spice-step-btn active" data-level="3">
                <span class="spice-chili-icons"><ion-icon name="flame" style="color:#FF3D2E;"></ion-icon><ion-icon name="flame" style="color:#FF3D2E;"></ion-icon><ion-icon name="flame" style="color:#FF3D2E;"></ion-icon></span>
                <span class="spice-step-title">Level 3</span>
                <span class="spice-step-tag">Nampol</span>
              </button>
              <button class="spice-step-btn" data-level="4">
                <span class="spice-chili-icons"><ion-icon name="flame" style="color:#DC2626;"></ion-icon><ion-icon name="flame" style="color:#DC2626;"></ion-icon><ion-icon name="flame" style="color:#DC2626;"></ion-icon><ion-icon name="flame" style="color:#DC2626;"></ion-icon></span>
                <span class="spice-step-title">Level 4</span>
                <span class="spice-step-tag">Brave</span>
              </button>
              <button class="spice-step-btn" data-level="5">
                <span class="spice-chili-icons"><ion-icon name="flame" style="color:#9333EA;"></ion-icon><ion-icon name="flame" style="color:#9333EA;"></ion-icon><ion-icon name="flame" style="color:#9333EA;"></ion-icon></span>
                <span class="spice-step-title">Level 5</span>
                <span class="spice-step-tag">Ekstrem</span>
              </button>
            </div>

            <div class="simulator-display">
              <div class="simulator-reaction-box">
                <span class="spice-emoji" id="simEmoji">🥵</span>
                <span class="reaction-level-badge" id="simLevelBadge">Level 3 - Pedas Nampol! • Spicy Lovers</span>
                <h3 class="reaction-quote" id="simQuote">"Nangis bahagia! Bibir merah merona, keringat bercucuran."</h3>
                <p class="reaction-desc" id="simDesc">Cabai rawit merah melimpah yang dioseng matang. Sangat cocok dinikmati bareng es teh manis dingin.</p>
              </div>

              <div class="simulator-recommendation-card" id="simRecCard">
                <img src="assets/images/menu/cireng-ayam-pedas.jpg" alt="Rekomendasi Cireng" class="rec-img" id="simRecImg" />
                <div class="rec-info">
                  <span class="rec-tag-label">Menu Pas Buat Kamu:</span>
                  <h4 class="rec-title" id="simRecTitle">Cireng Isi Ayam Pedas Mercon</h4>
                  <span class="rec-price" id="simRecPrice">Rp 6.000</span>
                  <div class="rec-actions-group">
                    <button class="rec-btn" id="simRecOrderBtn"><ion-icon name="cart-outline"></ion-icon> Pesan Menu Ini</button>
                    <button class="rec-filter-btn" id="simRecFilterBtn" type="button"><ion-icon name="restaurant-outline"></ion-icon> Lihat Menu Serupa</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

      <!-- ===================================
           ABOUT : Timeline Story
      ==================================== -->
      <section class="section about-section" id="about">
        <div class="container">

          <div class="about-intro">
            <!-- Image side -->
            <div class="about-image-wrapper reveal-left">
              <img src="assets/images/about-banner.jpg" alt="Toko Cimohot Sejak 2021" class="about-main-img" loading="lazy" />
              <div class="about-year-badge">
                <span class="year">2021</span>
                <span class="label">Pelopor</span>
              </div>
            </div>

            <!-- Text side -->
            <div class="about-text-content reveal-right">
              <span class="section-badge">CERITA DI BALIK CIMOHOT</span>
              <h2 class="section-title" style="text-align:left;">Bukan Cireng Biasa. <span class="text-gradient">Ini Lain Cerita.</span></h2>
              <p class="about-description">
                Semuanya mulai dari dapur kecil di Koja, 2021. <strong>Marilyn Helika</strong> yang bosan ngidam cireng dengan isian yang selalu tipis akhirnya berinisiatif bikin sendiri. Versi yang lebih montok, lebih krispi, dan lebih berani pedasnya. Teman-temannya minta dibungkus, lalu tetangganya, hingga pelanggan baru yang terus berdatangan. Dan itulah awal <strong>CIMOHOT</strong>, singkatan dari <em>"Cireng Isi Montok Hot"</em>.
              </p>

              <div class="pillars-grid">
                <div class="pillar-item">
                  <div class="pillar-icon"><ion-icon name="leaf-outline"></ion-icon></div>
                  <div>
                    <h4 class="pillar-title">Bahan Alami</h4>
                    <p class="pillar-desc">Tanpa pengawet & higienis</p>
                  </div>
                </div>
                <div class="pillar-item">
                  <div class="pillar-icon"><ion-icon name="shield-checkmark-outline"></ion-icon></div>
                  <div>
                    <h4 class="pillar-title">100% Halal</h4>
                    <p class="pillar-desc">Daging pilihan bersertifikasi</p>
                  </div>
                </div>
                <div class="pillar-item">
                  <div class="pillar-icon"><ion-icon name="flame-outline"></ion-icon></div>
                  <div>
                    <h4 class="pillar-title">Cabai Segar</h4>
                    <p class="pillar-desc">Bukan bubuk sintetis</p>
                  </div>
                </div>
                <div class="pillar-item">
                  <div class="pillar-icon"><ion-icon name="heart-outline"></ion-icon></div>
                  <div>
                    <h4 class="pillar-title">Isi Montok</h4>
                    <p class="pillar-desc">Penuh di setiap gigitan</p>
                  </div>
                </div>
              </div>

              <div class="about-founder-card">
                <div class="founder-badge-avatar"><ion-icon name="restaurant-outline"></ion-icon></div>
                <div class="founder-content">
                  <div class="founder-header">
                    <div>
                      <span class="founder-role-tag">PENGGAGAS RESEP AUTENTIK</span>
                      <h4 class="founder-name">Marilyn Helika R.</h4>
                    </div>
                    <span class="founder-origin-badge">Dapur Koja, 2021</span>
                  </div>
                  <p class="founder-quote">"Cireng harus montok dagingnya, renyah krispi kulitnya, dan cabai rawit merahnya asli segar tanpa bubuk sintetis."</p>
                  <div class="founder-subinfo">
                    <span><ion-icon name="heart"></ion-icon> Resep Dapur Sendiri</span>
                    <span><ion-icon name="shield-checkmark"></ion-icon> Mitra Bahan: Ibu Maria Rohayati</span>
                    <span><ion-icon name="checkmark-done-circle"></ion-icon> 100% Homemade Halal</span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Timeline -->
          <div class="section-header reveal">
            <span class="section-badge">PERJALANAN KAMI</span>
            <h2 class="section-title">Milestone <span class="text-gradient">Cimohot</span></h2>
          </div>

          <div class="timeline stagger-children">
            <div class="timeline-item reveal">
              <div class="timeline-dot"></div>
              <span class="timeline-year">Mei 2021</span>
              <h4 class="timeline-title">Lahirnya Cimohot</h4>
              <p class="timeline-desc">Resmi didirikan oleh Marilyn Helika R. dengan misi menghadirkan cireng isi montok premium pertama di Jakarta Utara.</p>
            </div>
            <div class="timeline-item reveal">
              <div class="timeline-dot"></div>
              <span class="timeline-year">Desember 2021</span>
              <h4 class="timeline-title">1.000 Porsi Pertama</h4>
              <p class="timeline-desc">Mencapai milestone seribu porsi terjual hanya dalam 7 bulan pertama melalui sistem pre-order WhatsApp.</p>
            </div>
            <div class="timeline-item reveal">
              <div class="timeline-dot"></div>
              <span class="timeline-year">2023</span>
              <h4 class="timeline-title">Ekspansi Menu & Online Store</h4>
              <p class="timeline-desc">Meluncurkan varian baru: Cireng Keju Mozzarella, Bakso Pedas, dan Tuna Kemangi. Website pemesanan online diluncurkan.</p>
            </div>
            <div class="timeline-item reveal">
              <div class="timeline-dot"></div>
              <span class="timeline-year">2024 hingga Sekarang</span>
              <h4 class="timeline-title">15.000+ Porsi & Terus Bertumbuh</h4>
              <p class="timeline-desc">Dipercaya ribuan keluarga Indonesia. Terus berinovasi dengan Paket Combo Hemat dan Level Pedas Kustom.</p>
            </div>
          </div>

        </div>
      </section>

      <!-- ===================================
           TESTIMONIALS : Social Wall Marquee
      ==================================== -->
      <section class="section testimonials-section" id="testimonials">
        <div class="container">
          <div class="section-header reveal">
            <span class="section-badge">TESTIMONI ASLI</span>
            <h2 class="section-title">Kata Mereka, <span class="text-gradient">Kamu Harus Coba</span></h2>
            <p class="section-text">Bukan kita yang bilang enak, mereka yang membuktikannya. Ini ceritanya.</p>
            <div class="testi-rating-summary">
              <div class="testi-big-rating">4.9</div>
              <div class="testi-rating-detail">
                <div class="testi-stars-row"><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon></div>
                <span>dari 2.400+ ulasan pelanggan</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Marquee (full-width, no container) -->
        <div class="testi-marquee-track">
          <!-- First set -->
          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Gak nyangka cireng bisa seenak dan semontok ini! Luarnya super garing renyah, dalemnya kenyal empuk, dan ayam pedas merconnya NANGISIN banget enaknya. Repeat order wajib!"
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-1.jpg" alt="Ade Mariani" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Ade Mariani</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Kami sekeluarga langganan Cimohot. Rasa dan kualitasnya selalu konsisten dari 2021! Cireng keju mozzarellanya juara molornya, anak-anak suka banget yang original."
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-2.jpg" alt="Cemara" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Cemara & Keluarga</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star-half-outline"></ion-icon>
            </div>
            <p class="testi-text">
              "Ai amang, cireng apalah ini! Pedas kali tapi gurih sedap. Mulai tadi mulut kepedasan tapi tangan tetep nyomot cireng terus. Cocok kali buat suguhan tamu kantor!"
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-3.jpg" alt="Duo Nyengir" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Duo Nyengir</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Pertama kali coba level 4, langsung ketagihan! Pedasnya beda, bukan cuma panas tapi ada gurih rempahnya yang bikin nagih. Best cireng ever sih ini!"
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #FF3D2E, #FF6B35); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">R</div>
              <div class="author-info">
                <span class="author-name">Rizky Ananda</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Order lewat website, prosesnya gampang banget. Cireng sampai masih hangat dan krispi. Packaging-nya rapi, ada label halalnya juga. Top markotop!"
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #22C55E, #10B981); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">D</div>
              <div class="author-info">
                <span class="author-name">Diana Putri</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <!-- Duplicate set for seamless marquee -->
          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Gak nyangka cireng bisa seenak dan semontok ini! Luarnya super garing renyah, dalemnya kenyal empuk, dan ayam pedas merconnya NANGISIN banget enaknya. Repeat order wajib!"
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-1.jpg" alt="Ade Mariani" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Ade Mariani</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Kami sekeluarga langganan Cimohot. Rasa dan kualitasnya selalu konsisten dari 2021! Cireng keju mozzarellanya juara molornya, anak-anak suka banget yang original."
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-2.jpg" alt="Cemara" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Cemara & Keluarga</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star-half-outline"></ion-icon>
            </div>
            <p class="testi-text">
              "Ai amang, cireng apalah ini! Pedas kali tapi gurih sedap. Mulai tadi mulut kepedasan tapi tangan tetep nyomot cireng terus. Cocok kali buat suguhan tamu kantor!"
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-3.jpg" alt="Duo Nyengir" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Duo Nyengir</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Pertama kali coba level 4, langsung ketagihan! Pedasnya beda, bukan cuma panas tapi ada gurih rempahnya yang bikin nagih. Best cireng ever sih ini!"
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #FF3D2E, #FF6B35); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">R</div>
              <div class="author-info">
                <span class="author-name">Rizky Ananda</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Order lewat website, prosesnya gampang banget. Cireng sampai masih hangat dan krispi. Packaging-nya rapi, ada label halalnya juga. Top markotop!"
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #22C55E, #10B981); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">D</div>
              <div class="author-info">
                <span class="author-name">Diana Putri</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <!-- 3 New Testimonials -->
          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Order buat arisan RT, langsung habis sebelum acara mulai. Terpaksa pesan lagi dadakan ke WA. Ibu-ibu sampai minta kontak tokonya."
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">B</div>
              <div class="author-info">
                <span class="author-name">Budi Santoso</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi • Koja</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Beli buat oleh-oleh ke Bandung. Teman-teman kirain beli di dapur branded. Aslinya dari Cimohot yang harganya cuma 6ribu per biji!"
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #EC4899, #DB2777); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">F</div>
              <div class="author-info">
                <span class="author-name">Fitri Ramadhani</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi • Kelapa Gading</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star-half-outline"></ion-icon>
            </div>
            <p class="testi-text">
              "Level 4 saya kira cuma drama-dramaan. Ternyata beda. Es teh 1 liter habis. Cirengnya habis duluan."
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #F59E0B, #D97706); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">H</div>
              <div class="author-info">
                <span class="author-name">Hendra Wijaya</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi • Tanjung Priok</span>
              </div>
            </div>
          </div>

          <!-- Duplicate set for seamless marquee -->
          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Gak nyangka cireng bisa seenak dan semontok ini! Luarnya super garing renyah, dalemnya kenyal empuk, dan ayam pedas merconnya NANGISIN banget enaknya. Repeat order wajib!"
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-1.jpg" alt="Ade Mariani" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Ade Mariani</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Kami sekeluarga langganan Cimohot. Rasa dan kualitasnya selalu konsisten dari 2021! Cireng keju mozzarellanya juara molornya, anak-anak suka banget yang original."
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-2.jpg" alt="Cemara" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Cemara &amp; Keluarga</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star-half-outline"></ion-icon>
            </div>
            <p class="testi-text">
              "Ai amang, cireng apalah ini! Pedas kali tapi gurih sedap. Mulai tadi mulut kepedasan tapi tangan tetep nyomot cireng terus. Cocok kali buat suguhan tamu kantor!"
            </p>
            <div class="testi-author-row">
              <img src="assets/images/avatar-3.jpg" alt="Duo Nyengir" class="author-avatar" loading="lazy" />
              <div class="author-info">
                <span class="author-name">Duo Nyengir</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Pertama kali coba level 4, langsung ketagihan! Pedasnya beda, bukan cuma panas tapi ada gurih rempahnya yang bikin nagih. Best cireng ever sih ini!"
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #FF3D2E, #FF6B35); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">R</div>
              <div class="author-info">
                <span class="author-name">Rizky Ananda</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi</span>
              </div>
            </div>
          </div>

          <div class="testi-card">
            <div class="testi-stars">
              <ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon><ion-icon name="star"></ion-icon>
            </div>
            <p class="testi-text">
              "Order buat arisan RT, langsung habis sebelum acara mulai. Terpaksa pesan lagi dadakan ke WA. Ibu-ibu sampai minta kontak tokonya."
            </p>
            <div class="testi-author-row">
              <div class="author-avatar" style="background: linear-gradient(135deg, #8B5CF6, #7C3AED); display:flex; align-items:center; justify-content:center; color:#fff; font-weight:800; font-size:0.9rem; width:42px; height:42px; border-radius:50%; border:2px solid var(--gold);">B</div>
              <div class="author-info">
                <span class="author-name">Budi Santoso</span>
                <span class="author-tag"><ion-icon name="checkmark-circle"></ion-icon> Pembeli Terverifikasi • Koja</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- ===================================
           BLOG : Spotlight Articles (Bento Grid)
      ==================================== -->
      <section class="section blog-section" id="blog">
        <div class="container">

          <div class="section-header reveal">
            <span class="section-badge">ARTIKEL & EDUKASI</span>
            <h2 class="section-title">Cerita Seputar <span class="text-gradient">Cimohot</span></h2>
            <p class="section-text">Bukan sekadar jajanan, ada kisah di balik setiap gigitan pedasnya.</p>
          </div>

          <div class="blog-bento-grid stagger-children">

            <!-- Featured (large) -->
            <article class="blog-card reveal">
              <div class="blog-thumb-wrapper">
                <img src="assets/images/blog-1.jpg" alt="Cara menggoreng cireng montok renyah" class="blog-thumb-img" loading="lazy" width="800" height="450" />
                <div class="blog-thumb-overlay"></div>
                <span class="blog-category-badge"><ion-icon name="flame"></ion-icon> Tips Dapur</span>
              </div>
              <div class="blog-body">
                <span class="blog-meta-date"><ion-icon name="flame-outline"></ion-icon> Panduan Menggoreng · <ion-icon name="time-outline"></ion-icon> 3 menit baca</span>
                <h3 class="blog-title">Cara Menggoreng Cireng Cimohot Agar Montok, Renyah, & Tidak Meletup</h3>
                <p class="blog-snippet">Rahasia mengatur api sedang dan suhu minyak yang tepat agar cireng mengembang montok maksimal, garing di luar, dan empuk lumer di dalam.</p>
                <button class="blog-read-btn" onclick="openModal('modal-1')">
                  Baca Panduan Lengkap <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
              </div>
            </article>

            <!-- Small cards -->
            <article class="blog-card reveal">
              <div class="blog-thumb-wrapper">
                <img src="assets/images/blog-2.jpg" alt="Panduan simpan cireng frozen" class="blog-thumb-img" loading="lazy" width="600" height="400" />
                <div class="blog-thumb-overlay"></div>
                <span class="blog-category-badge"><ion-icon name="snow"></ion-icon> Simpan Frozen</span>
              </div>
              <div class="blog-body">
                <span class="blog-meta-date"><ion-icon name="snow-outline"></ion-icon> Tips Kulkas · <ion-icon name="time-outline"></ion-icon> 2 menit baca</span>
                <h3 class="blog-title">Berapa Lama Cireng Tahan di Chiller & Freezer? Panduan Simpan Aman</h3>
                <p class="blog-snippet">Panduan masa simpan cireng frozen: tahan 3-4 hari di kulkas bawah atau hingga 1 bulan di freezer beku tanpa merusak rasa gurih bumbunya.</p>
                <button class="blog-read-btn" onclick="openModal('modal-2')">
                  Baca Panduan Lengkap <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
              </div>
            </article>

            <article class="blog-card reveal">
              <div class="blog-thumb-wrapper">
                <img src="assets/images/blog-3.jpg" alt="Cabai rawit segar alami Cimohot" class="blog-thumb-img" loading="lazy" width="600" height="400" />
                <div class="blog-thumb-overlay"></div>
                <span class="blog-category-badge"><ion-icon name="leaf"></ion-icon> Mutu Bahan</span>
              </div>
              <div class="blog-body">
                <span class="blog-meta-date"><ion-icon name="leaf-outline"></ion-icon> Komitmen Bahan · <ion-icon name="time-outline"></ion-icon> 2 menit baca</span>
                <h3 class="blog-title">100% Cabai Rawit Segar: Kenapa Kami Menolak Bubuk Cabai Kimia Sintetis</h3>
                <p class="blog-snippet">Alasan pedasnya Cimohot terasa gurih segar di lidah, tidak pahit di tenggorokan, dan ramah di lambung karena memakai racikan cabai segar asli.</p>
                <button class="blog-read-btn" onclick="openModal('modal-3')">
                  Baca Panduan Lengkap <ion-icon name="arrow-forward-outline"></ion-icon>
                </button>
              </div>
            </article>

          </div>

        </div>
      </section>

      <!-- ===================================
           CTA BANNER : Final Call
      ==================================== -->
      <section class="cta-section">
        <div class="container">
          <div class="cta-banner-box reveal-scale">
            <p class="cta-eyebrow"><ion-icon name="flame" style="vertical-align:-2px; margin-right:4px;"></ion-icon> Udah lihat menunya. Udah tahu levelnya.</p>
            <h2 class="cta-title">Tinggal satu langkah lagi. <span class="text-gradient">Pesan sekarang.</span></h2>
            <p class="cta-text">
              Ribuan pelanggan udah cobain duluan. Sekarang giliran kamu.
            </p>
            <div class="cta-social-proof">
              <span class="cta-live-dot"></span>
              <span><?php echo rand(8, 23); ?> orang sedang lihat menu ini sekarang</span>
            </div>
            <div class="cta-actions">
              <a href="#food-menu" class="btn btn-primary" id="cta-order-btn"><ion-icon name="restaurant-outline"></ion-icon> Pesan Sekarang</a>
              <a href="https://wa.me/6285930194086?text=Halo%20Cimohot!%20Mau%20order%20cireng%20montok%20dong!" target="_blank" class="btn btn-wa" rel="noopener" id="cta-wa-btn">
                <ion-icon name="logo-whatsapp"></ion-icon> Chat WA
              </a>
            </div>
          </div>
        </div>
      </section>


    </article>
  </main>

  <!-- ===================================
       FOOTER
  ==================================== -->
  <footer class="footer" id="contact">
    <div class="container">

      <div class="footer-grid footer-grid-4">

        <!-- Col 1: Brand -->
        <div>
          <a href="index.php" class="logo" style="margin-bottom: 6px;">
            <img src="assets/images/logo cimohot.png" alt="Cimohot Logo" class="logo-img" width="40" height="40" />
            <div class="logo-text">
              <span class="logo-brand">Cimohot<span>.</span></span>
              <span class="logo-tagline">Hot & Montok</span>
            </div>
          </a>
          <p class="footer-bio">
            Dari dapur kecil di Koja ke ribuan meja makan keluarga Indonesia. Resep autentik, bahan higienis, pedas nampol sejak 2021.
          </p>
          <div class="footer-social-links">
            <a href="https://www.instagram.com/cireng_isi_montok_hot_official/" target="_blank" class="footer-social-link" aria-label="Instagram" rel="noopener">
              <ion-icon name="logo-instagram"></ion-icon>
            </a>
            <a href="https://www.tiktok.com/@cimohot.official" target="_blank" class="footer-social-link" aria-label="TikTok" rel="noopener">
              <ion-icon name="logo-tiktok"></ion-icon>
            </a>
            <a href="https://wa.me/6285930194086" target="_blank" class="footer-social-link" aria-label="WhatsApp" rel="noopener">
              <ion-icon name="logo-whatsapp"></ion-icon>
            </a>
          </div>
        </div>

        <!-- Col 2: Nav -->
        <div>
          <h4 class="footer-col-title">Navigasi</h4>
          <ul class="footer-nav-list">
            <li class="footer-nav-item"><a href="#hero">Beranda</a></li>
            <li class="footer-nav-item"><a href="#food-menu">Katalog Menu</a></li>
            <li class="footer-nav-item"><a href="#spice-simulator">Level Pedas</a></li>
            <li class="footer-nav-item"><a href="#about">Cerita & Sejarah</a></li>
            <li class="footer-nav-item"><a href="#testimonials">Ulasan Pembeli</a></li>
            <li class="footer-nav-item"><a href="cart/cart.php">Keranjang Belanja</a></li>
          </ul>
        </div>

        <!-- Col 3: Contact -->
        <div>
          <h4 class="footer-col-title">Lokasi & Kontak</h4>
          <div class="footer-contact-item">
            <ion-icon name="location-outline"></ion-icon>
            <span>Jl. Muncang Raya No. 55, Koja, Jakarta Utara 14220</span>
          </div>
          <div class="footer-contact-item">
            <ion-icon name="call-outline"></ion-icon>
            <a href="tel:+6285930194086" style="color:inherit;">+62 859-3019-4086</a>
          </div>
          <div class="footer-contact-item">
            <ion-icon name="mail-outline"></ion-icon>
            <span>jhoi.wada324@gmail.com</span>
          </div>
          <!-- Google Maps embed -->
          <div class="footer-map-wrapper">
            <iframe
              src="https://maps.google.com/maps?q=Jl.+Muncang+Raya+No.+55,+Koja,+Jakarta+Utara&output=embed&z=15"
              width="100%" height="160" style="border:0; border-radius:12px; margin-top:12px;"
              allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"
              title="Lokasi Cimohot">
            </iframe>
          </div>
        </div>

        <!-- Col 4: Hours + Trust -->
        <div>
          <h4 class="footer-col-title">Jam Buka</h4>
          <?php
            $hour = (int)date('G', strtotime('+7 hours', strtotime(gmdate('Y-m-d H:i:s'))));
            // Ambil jam WIB (UTC+7)
            $hourWIB = (int)date('G') + 7;
            if ($hourWIB >= 24) $hourWIB -= 24;
            $isOpen = ($hourWIB >= 9 && $hourWIB < 21);
          ?>
          <div class="footer-hours-badge <?= $isOpen ? 'badge-open' : 'badge-closed' ?>">
            <span class="hours-dot"></span>
            <?= $isOpen ? 'BUKA SEKARANG' : 'TUTUP SEKARANG' ?>
          </div>
          <p style="font-size:0.85rem; color:var(--text-muted); margin-top:8px;">Setiap Hari: 09:00 – 21:00 WIB</p>

          <div class="footer-trust-badges">
            <div class="trust-badge-item"><ion-icon name="checkmark-circle"></ion-icon> 100% Halal</div>
            <div class="trust-badge-item"><ion-icon name="leaf-outline"></ion-icon> Fresh Daily</div>
            <div class="trust-badge-item"><ion-icon name="shield-checkmark-outline"></ion-icon> Bayar Aman</div>
            <div class="trust-badge-item"><ion-icon name="logo-whatsapp"></ion-icon> WA Responsif</div>
          </div>

          <a href="https://wa.me/6285930194086?text=Halo%20Cimohot!%20Mau%20order%20dong!" target="_blank" class="footer-wa-cta" rel="noopener">
            <ion-icon name="logo-whatsapp"></ion-icon> Order via WhatsApp
          </a>
        </div>

      </div>

      <div class="footer-bottom-row">
        <p>&copy; <?php echo date("Y"); ?> Toko Cimohot • Cireng Isi Montok Hot. Dibuat dengan <ion-icon name="heart" style="color:var(--red); vertical-align:middle;"></ion-icon> untuk para pencinta jajanan Indonesia.</p>
        <div class="footer-bottom-links">
          <a href="#hero">Kembali ke atas</a>
          <a href="cart/cart.php">Keranjang</a>
          <a href="login/login.php">Masuk</a>
        </div>
      </div>

    </div>
  </footer>

  <!-- ===================================
       QUICK VIEW MODAL
  ==================================== -->
  <div id="quickViewModal" class="modal">
    <div class="modal-content-box">
      <button class="modal-close-icon" onclick="closeQuickViewModal()" aria-label="Tutup">&times;</button>

      <div class="quickview-grid">
        <div class="quickview-media">
          <img src="" id="qvImg" alt="Foto Makanan" class="quickview-img" />
        </div>
        <div class="quickview-body">
          <span class="qv-category-badge" id="qvCategory">Cireng Pedas</span>
          <h3 class="qv-title" id="qvTitle">Nama Makanan</h3>
          <span class="qv-price" id="qvPrice">Rp 6.000</span>
          <p class="qv-desc" id="qvDesc">Deskripsi lezat makanan...</p>

          <div class="qv-qty-row">
            <span>Jumlah:</span>
            <div class="qty-stepper">
              <button class="qty-step-btn" id="qvQtyMinus">-</button>
              <span class="qty-num-display" id="qvQtyDisplay">1</span>
              <button class="qty-step-btn" id="qvQtyPlus">+</button>
            </div>
          </div>

          <button id="qvAddToCartBtn" class="btn btn-primary" style="width: 100%;">
            <ion-icon name="cart-outline"></ion-icon> Tambahkan ke Keranjang
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================================
       FLOATING CART BAR (Bottom Sticky Pill)
  ==================================== -->
  <div id="floatingCartBar" class="floating-cart-bar" style="display: none;">
    <div class="floating-cart-inner">
      <div class="floating-cart-info" onclick="openCartDrawer()" role="button" tabindex="0" title="Buka Keranjang Belanja">
        <div class="floating-cart-icon-wrap">
          <ion-icon name="bag-handle"></ion-icon>
          <span class="floating-cart-count" id="floatingCartCount">0</span>
        </div>
        <div class="floating-cart-text">
          <span class="floating-cart-label">Total Pesanan</span>
          <span class="floating-cart-total" id="floatingCartTotal">Rp 0</span>
        </div>
      </div>
      <button type="button" class="floating-cart-action-btn" id="openDrawerFromBarBtn" onclick="openCartDrawer()">
        <span>Lihat Keranjang</span>
        <ion-icon name="arrow-forward-outline"></ion-icon>
      </button>
    </div>
  </div>

  <!-- ===================================
       CART DRAWER
  ==================================== -->
  <div id="cartDrawerOverlay" class="cart-drawer-overlay">
    <div class="cart-drawer">
      <div class="cart-drawer-header">
        <div class="cart-drawer-header-left">
          <h3 class="cart-drawer-title">
            <ion-icon name="bag-handle-outline"></ion-icon> Keranjang Belanja
          </h3>
          <span class="drawer-cart-count-badge" id="drawerCartCountBadge">0 item</span>
        </div>
        <button class="cart-drawer-close" id="closeCartDrawerBtn" onclick="closeCartDrawer()" aria-label="Tutup">&times;</button>
      </div>

      <div class="cart-drawer-items-list" id="cartDrawerItemsList">
        <!-- Rendered by JavaScript -->
      </div>

      <div class="cart-drawer-footer" id="cartDrawerFooter">
        <div class="drawer-subtotal-row">
          <span class="drawer-subtotal-label">Subtotal</span>
          <span class="drawer-subtotal-val" id="drawerSubtotalVal">Rp 0</span>
        </div>
        <div class="drawer-actions">
          <a href="/cart" class="btn btn-primary btn-drawer-checkout" id="drawerCheckoutBtn">
            <span>Lanjut ke Checkout</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </a>
          <a href="https://wa.me/6285930194086?text=Halo%20Cimohot!%20Saya%20mau%20checkout%20pesanan%20saya" target="_blank" class="btn btn-wa" rel="noopener">
            <ion-icon name="logo-whatsapp"></ion-icon> Pesan via WhatsApp
          </a>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================================
       RICH MAGAZINE ARTICLE MODALS
  ==================================== -->

  <!-- Modal 1: Tips Menggoreng Cireng Montok -->
  <div id="modal-1" class="modal">
    <div class="modal-content-box article-modal-box">
      <button class="modal-close-icon" onclick="closeModal('modal-1')" aria-label="Tutup">&times;</button>
      <div class="modal-article-content">
        <div class="article-header-meta">
          <span class="article-cat-badge"><ion-icon name="flame"></ion-icon> Tips Dapur Cimohot</span>
          <span class="article-read-time"><ion-icon name="time-outline"></ion-icon> 3 menit baca</span>
        </div>
        <h2 class="modal-article-title">Cara Menggoreng Cireng Cimohot Agar Montok, Renyah, & Tidak Meletup</h2>
        <img src="assets/images/blog-1.jpg" alt="Menggoreng Cireng Montok" class="modal-article-banner" />
        
        <p>
          Banyak penikmat cireng mengeluhkan cireng yang keras saat dingin atau meletup saat digoreng di wajan. Cireng Cimohot dibuat dari adonan tapioka premium dengan takaran kelembapan presisi. Agar tekstur krispi di luar dan kenyal empuk di dalam keluar sempurna, ikuti 4 langkah rahasia dapur kami berikut:
        </p>

        <div class="article-steps-list">
          <div class="article-step-item">
            <span class="article-step-num">1</span>
            <div class="article-step-content">
              <h5>Jangan Goreng Dalam Kondisi Beku Batu</h5>
              <p>Jika baru dikeluarkan dari freezer, diamkan 10 - 15 menit di suhu ruang sampai adonan tidak kaku beku. Ini mencegah perbedaan suhu drastis yang bisa memicu letupan minyak.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">2</span>
            <div class="article-step-content">
              <h5>Gunakan Minyak Bersih & Cukup Banyak (Metode Semi-Deep Fry)</h5>
              <p>Pastikan minyak merendam minimal setengah badan cireng agar panas merata ke seluruh permukaan adonan tapioka.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">3</span>
            <div class="article-step-content">
              <h5>Kunci Utama: Api Sedang Cenderung Kecil</h5>
              <p>Masukkan cireng saat minyak sudah cukup hangat (belum mendidih berasap). Goreng dengan api sedang-kecil agar bagian dalam matang dan kulit luar mengembang montok tanpa gosong.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">4</span>
            <div class="article-step-content">
              <h5>Waktu Menggoreng Cukup 3 - 4 Menit</h5>
              <p>Bolak-balik perlahan hingga kedua sisi berwarna putih keemasan dan mengapung ringan. Angkat dan tiriskan tegak agar minyak berlebih turun tuntas.</p>
            </div>
          </div>
        </div>

        <div class="article-protip-box">
          <ion-icon name="bulb" class="article-protip-icon"></ion-icon>
          <div class="article-protip-text">
            <strong>Kitchen Pro-Tip untuk Varian Keju Mozzarella:</strong> Goreng maksimal 3 menit saja! Jika digoreng terlalu lama, keju mozzarella di dalam akan mencair maksimal dan mendesak keluar dari kulit cireng.
          </div>
        </div>

        <div class="article-cta-card">
          <div class="article-cta-text">
            <h4>Mau Coba Goreng Cireng Montok di Rumah?</h4>
            <p>Pesan paket cireng siap goreng atau siap santap hangat sekarang.</p>
          </div>
          <button type="button" class="btn-article-cta" onclick="closeModal('modal-1'); window.location.hash = 'food-menu';">
            <span>Pilih Menu Sekarang</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 2: Panduan Penyimpanan Frozen -->
  <div id="modal-2" class="modal">
    <div class="modal-content-box article-modal-box">
      <button class="modal-close-icon" onclick="closeModal('modal-2')" aria-label="Tutup">&times;</button>
      <div class="modal-article-content">
        <div class="article-header-meta">
          <span class="article-cat-badge"><ion-icon name="snow"></ion-icon> Panduan Penyimpanan</span>
          <span class="article-read-time"><ion-icon name="time-outline"></ion-icon> 2 menit baca</span>
        </div>
        <h2 class="modal-article-title">Berapa Lama Cireng Tahan di Chiller & Freezer? Panduan Simpan Aman</h2>
        <img src="assets/images/blog-2.jpg" alt="Penyimpanan Cireng Frozen" class="modal-article-banner" />

        <p>
          Cimohot diproduksi <strong>100% tanpa bahan pengawet kimia buatan</strong>. Karena itu, cara penyimpanan yang tepat sangat krusial agar kualitas isian daging, aroma daun jeruk, dan kelembutan adonan tetap terjaga segar seperti baru dibuat.
        </p>

        <div class="article-steps-list">
          <div class="article-step-item">
            <span class="article-step-num">1</span>
            <div class="article-step-content">
              <h5>Suhu Ruangan (25°C - 30°C): Maksimal 24 Jam</h5>
              <p>Cocok untuk pengiriman instan/sameday. Segera goreng atau langsung simpan di dalam lemari es setelah paket diterima.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">2</span>
            <div class="article-step-content">
              <h5>Kulkas Bawah / Chiller (2°C - 5°C): Tahan 3 - 4 Hari</h5>
              <p>Ideal jika Anda berencana menggoreng cireng dalam 1-3 hari ke depan. Cireng tetap lembut dan siap digoreng tanpa perlu proses defrost lama.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">3</span>
            <div class="article-step-content">
              <h5>Freezer Beku (-18°C): Tahan Hingga 1 Bulan</h5>
              <p>Dalam kondisi beku beku sempurna dan kemasan tertutup rapat, cireng awet hingga 30 hari tanpa penurunan cita rasa bumbu rempahnya.</p>
            </div>
          </div>
        </div>

        <div class="article-protip-box">
          <ion-icon name="snow" class="article-protip-icon"></ion-icon>
          <div class="article-protip-text">
            <strong>Storage Pro-Tip:</strong> Pastikan kemasan tertutup rapat (airtight). Jangan biarkan cireng terkena udara dingin langsung di freezer agar permukaan tapioka tidak kering (*freezer burn*).
          </div>
        </div>

        <div class="article-cta-card">
          <div class="article-cta-text">
            <h4>Stok Cemilan Praktis untuk Keluarga</h4>
            <p>Simpan di freezer, goreng kapan saja saat lapar melanda!</p>
          </div>
          <button type="button" class="btn-article-cta" onclick="closeModal('modal-2'); window.location.hash = 'food-menu';">
            <span>Stok Cireng Sekarang</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal 3: Transparansi Cabai Segar -->
  <div id="modal-3" class="modal">
    <div class="modal-content-box article-modal-box">
      <button class="modal-close-icon" onclick="closeModal('modal-3')" aria-label="Tutup">&times;</button>
      <div class="modal-article-content">
        <div class="article-header-meta">
          <span class="article-cat-badge"><ion-icon name="leaf"></ion-icon> Komitmen Mutu Bahan</span>
          <span class="article-read-time"><ion-icon name="time-outline"></ion-icon> 2 menit baca</span>
        </div>
        <h2 class="modal-article-title">100% Cabai Rawit Segar: Kenapa Kami Menolak Bubuk Cabai Kimia Sintetis</h2>
        <img src="assets/images/blog-3.jpg" alt="Cabai Segar Alami Cimohot" class="modal-article-banner" />

        <p>
          Pernahkah Anda memakan camilan pedas lalu tenggorokan terasa gatal batuk dan perut terasa perih panas? Hal tersebut umumnya disebabkan oleh penggunaan <em>chili powder</em> curah murah dengan zat pewarna dan zat penguat pedas sintetis (*capsaicin extract*).
        </p>

        <div class="article-steps-list">
          <div class="article-step-item">
            <span class="article-step-num">1</span>
            <div class="article-step-content">
              <h5>Hanya Cabai Rawit Merah Segar Petik</h5>
              <p>Dapur Cimohot setiap hari mengolah cabai rawit merah asli pilihan dari mitra petani lokal, dicuci higienis dan ditumis hingga aroma langu hilang sempurna.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">2</span>
            <div class="article-step-content">
              <h5>Aroma Tumisan Daun Jeruk & Bawang Alami</h5>
              <p>Rasa pedas Cimohot bukan sekadar membakar lidah, melainkan menyatu dengan gurihnya tumisan bawang merah, bawang putih, dan daun jeruk purut segar.</p>
            </div>
          </div>

          <div class="article-step-item">
            <span class="article-step-num">3</span>
            <div class="article-step-content">
              <h5>Lebih Ramah di Pencernaan</h5>
              <p>Kandungan minyak cabai alami dari tumisan segar lebih mudah dicerna oleh tubuh, tidak meninggalkan rasa asam getir di kerongkongan.</p>
            </div>
          </div>
        </div>

        <div class="article-protip-box">
          <ion-icon name="heart-circle" class="article-protip-icon"></ion-icon>
          <div class="article-protip-text">
            <strong>Komitmen Dapur Marilyn:</strong> "Keluarga kami memakan apa yang kami jual. Jika kami tidak rela anak dan keluarga kami memakannya, kami tidak akan pernah menjualnya ke pelanggan."
          </div>
        </div>

        <div class="article-cta-card">
          <div class="article-cta-text">
            <h4>Buktikan Kesegaran Pedasnya Sendiri</h4>
            <p>Pilih level pedas favoritmu dari Level 1 sampai Level 5.</p>
          </div>
          <button type="button" class="btn-article-cta" onclick="closeModal('modal-3'); window.location.hash = 'food-menu';">
            <span>Coba Cireng Pedas</span>
            <ion-icon name="arrow-forward-outline"></ion-icon>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- ===================================
       FLOATING WHATSAPP BUTTON
  ==================================== -->
  <a href="https://wa.me/6285930194086?text=Halo%20Cimohot!%20Saya%20mau%20pesan%20cireng%20isi%20montok" target="_blank" class="floating-wa-btn" aria-label="Chat WhatsApp" rel="noopener">
    <ion-icon name="logo-whatsapp"></ion-icon>
  </a>

  <!-- ===================================
       MOBILE BOTTOM DOCK
  ==================================== -->
  <nav class="mobile-bottom-dock">
    <a href="#hero" class="dock-item active">
      <ion-icon name="home-outline"></ion-icon>
      <span>Beranda</span>
    </a>
    <a href="#food-menu" class="dock-item">
      <ion-icon name="restaurant-outline"></ion-icon>
      <span>Menu</span>
    </a>
    <a href="#" class="dock-item" id="dockCartLink">
      <ion-icon name="cart-outline"></ion-icon>
      <span>Keranjang</span>
      <span class="dock-cart-badge">0</span>
    </a>
    <a href="login/login.php" class="dock-item" id="dockAccountLink">
      <ion-icon name="person-outline"></ion-icon>
      <span>Akun</span>
    </a>
  </nav>

  <!-- ===================================
       BACK TO TOP
  ==================================== -->
  <a href="#top" class="back-top-btn" aria-label="Kembali ke atas" data-back-top-btn>
    <ion-icon name="chevron-up-outline"></ion-icon>
  </a>

  <!-- Core JS -->
  <script src="assets/js/main.js?v=<?= time(); ?>"></script>

  <!-- Ionicons -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
</body>
</html>
