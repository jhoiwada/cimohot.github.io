<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Website Cimohot</title>

    <link rel="shortcut icon" href="assets/images/favicon.png" type="image/x-icon" />

    <link rel="stylesheet" href="assets/css/style.css" />

    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&family=Rubik:wght@400;500;600;700&family=Shadows+Into+Light&display=swap" rel="stylesheet"/>

    <link rel="preload" as="image" href="assets/images/hero-banner.png" media="(min-width: 768px)"/>
    <link rel="preload" as="image" href="assets/images/hero-banner.png" />
  </head>

  <body id="top">
    <header class="header" data-header id="header">
      <div class="container">
        <a href="index.php" class="logo">
          <img src="assets/images/logo cimohot.png" alt="Cimohot Logo" class="logo-img"/>
        </a>

        <nav class="navbar" data-navbar>
          <ul class="navbar-list">
            <li class="nav-item">
              <a href="#home" class="navbar-link" data-nav-link>Home</a>
            </li>
            <li class="nav-item">
              <a href="#about" class="navbar-link" data-nav-link>About Us</a>
            </li>
            <li class="nav-item" id="nav-menu-link">
              <a href="#food-menu" class="navbar-link" data-nav-link>Menu</a>
            </li>
            <li class="nav-item">
              <a href="#blog" class="navbar-link" data-nav-link>Blog</a>
            </li>
            <li class="nav-item">
              <a href="#contact" class="navbar-link" data-nav-link>Contact Us</a>
            </li>
          </ul>
        </nav>

        
          
          <div class="cart-container">
            <button class="cart-btn" id="cartBtn" aria-label="Shopping Cart">
              <ion-icon name="bag-handle-outline"></ion-icon>
              <span class="cart-badge" id="cartBadge" style="display: none">0</span>
            </button>
          </div>

          <div class="order-btn-container">
            <button class="btn order-btn requires-login" id="orderBtn">Order</button>
            <span class="notification-badge" id="notificationBadge" style="display: none">3</span>
          </div>

          <div id="loginStatusContainer" class="login-status-container">
            <div class="status-indicator" id="statusIndicator"></div>
            <div id="loginStatusText" class="login-status-text">
              <span>Belum Login</span>
            </div>
            <button id="loginBtn" class="login-btn">Login</button>
            <button id="logoutBtn" class="logout-btn" style="display: none">Logout</button>
          </div>

          <button class="nav-toggle-btn" aria-label="Toggle Menu" data-menu-toggle-btn>
            <span class="line top"></span><span class="line middle"></span><span class="line bottom"></span>
          </button>
        </div>
      </div>
    </header>

    <!-- Toast Notification -->
    <div id="toast" class="toast"></div>

   
    
        <!-- 
        - #HERO
      -->

       <section class="hero" id="home" style="background-image: url('assets/images/hero-bg.png')">
    <div class="container">
        <div class="hero-content">
            <h2 class="h1 hero-title">
                <?php 
                // Logika untuk menampilkan sapaan personal jika sudah login
                if (isset($_SESSION['name'])) {
                    // Ambil hanya nama pertama dari nama lengkap
                    $firstName = explode(' ', htmlspecialchars($_SESSION['name']))[0];
                    echo "Selamat Datang, " . $firstName . "!";
                } else {
                    // Teks default jika belum login
                    echo "Cireng Cimohot, Pedasnya Nampol!";
                }
                ?>
            </h2>
            <p class="hero-text">
                <?php if (isset($_SESSION['name'])): ?>
                    Siap untuk menikmati cireng favoritmu lagi? Jelajahi menu kami di bawah ini.
                <?php else: ?>
                    Gurih di luar, kenyal di dalam — rasa asli cireng yang bikin nagih. Temukan sensasi pedasnya yang bikin lidah goyang!
                <?php endif; ?>
            </p>
            
            <!-- Tombol Hero dengan logika dinamis -->
            <?php if (isset($_SESSION['name'])): ?>
                <!-- Jika sudah login, tombol langsung ke menu -->
                <a href="#food-menu" class="btn hero-btn" id="heroBtn">Menu</a>
            <?php else: ?>
                <!-- Jika belum login, tombol mengarah ke login -->
                <button class="btn hero-btn" id="heroBtn">Order Now</button>
            <?php endif; ?>
        </div>
        
        <figure class="hero-banner">
            <img src="assets/images/hero-banner.png" width="700" height="900" loading="lazy" alt="cireng" class="w-200 hero-img"/>
        </figure>
    </div>
</section>

        <!-- 
        - #ABOUT
      -->

        <section class="section section-divider gray about" id="about">
          <div class="container">
            <div class="about-banner">
              <img
                src="./assets/images/about-banner.jpg"
                width="509"
                height="459"
                loading="lazy"
                alt="Cireng Isi Montok Hot"
                class="about-img"
              />
              <div class="abs-img">
                <svg
                  width="120"
                  height="120"
                  viewBox="0 0 120 120"
                  class="since-badge"
                >
                  <text
                    x="60"
                    y="45"
                    text-anchor="middle"
                    fill="white"
                    font-size="12"
                    font-weight="bold"
                  >
                    SEJAK
                  </text>
                  <text
                    x="60"
                    y="65"
                    text-anchor="middle"
                    fill="white"
                    font-size="18"
                    font-weight="bold"
                  >
                    2021
                  </text>
                  <text
                    x="60"
                    y="85"
                    text-anchor="middle"
                    fill="white"
                    font-size="10"
                  >
                    TERPERCAYA
                  </text>
                </svg>
              </div>
            </div>

            <div class="about-content">
              <h2 class="h2 section-title">
                Cimohot, Cireng Isi Montok
                <span class="span">Terenak di Kota!</span>
              </h2>

              <p class="section-text">
                Sejak 2021, Cimohot telah menjadi pilihan utama para pecinta
                cemilan tradisional Indonesia. Kami bangga menghadirkan cireng
                isi dengan cita rasa autentik yang montok dan pedas menggoda,
                dibuat dengan resep rahasia dan bahan-bahan berkualitas tinggi
                pilihan.
              </p>

              <ul class="about-list">
                <li class="about-item">
                  <ion-icon name="checkmark-outline"></ion-icon>
                  <span class="span">Cireng Isi Montok & Lezat</span>
                </li>

                <li class="about-item">
                  <ion-icon name="checkmark-outline"></ion-icon>
                  <span class="span">Bahan Berkualitas & Higienis</span>
                </li>

                <li class="about-item">
                  <ion-icon name="checkmark-outline"></ion-icon>
                  <span class="span">Pelayanan Cepat & Ramah</span>
                </li>
              </ul>

              <button
              class="btn btn-secondary more-info-btn"
              onclick="openModal('aboutModal')"
            >About Us</button>
          </div>
        </div>
      </section>

        <!-- MODAL -->
        <div id="aboutModal" class="modal">
          <div class="modal-content">
            <span class="close" onclick="closeAboutModal()">&times;</span>

            <h3 class="modal-title">🏪 Sejarah Cimohot</h3>
            <p class="modal-text">
              <strong>Toko Cimohot</strong> resmi didirikan pada tanggal
              <strong>28 Mei 2021</strong> oleh
              <strong>Marilyn Helika R.</strong> yang memiliki passion mendalam
              terhadap cemilan khas Indonesia. Berawal dari kecintaan terhadap
              jajanan tradisional yang sederhana namun kaya rasa, Cimohot hadir
              sebagai jawaban bagi para pecinta cireng isi yang montok dan pedas
              menggoda.
            </p>

            <div class="info-grid">
              <div class="info-card">
                <h4>👩‍💼 Pemilik</h4>
                <p>
                  <strong>Marilyn Helika R.</strong><br />Penggagas utama dengan
                  visi kuat dalam pengembangan produk cemilan khas Indonesia
                </p>
              </div>
              <div class="info-card">
                <h4>🤝 Pemasok</h4>
                <p>
                  <strong>Ibu Maria Rohayati</strong><br />Mitra strategis
                  penyedia bahan baku berkualitas tinggi
                </p>
              </div>
              <div class="info-card">
                <h4>📅 Didirikan</h4>
                <p>
                  <strong>28 Mei 2021</strong><br />Lebih dari 3 tahun melayani
                  dengan kualitas terbaik
                </p>
              </div>
            </div>

            <h3 class="modal-title">🎯 Visi & Misi</h3>
            <p class="modal-text">
              <strong>Visi:</strong> Menjadi pelopor cemilan cireng isi terbaik
              dengan tetap melestarikan cita rasa tradisional Indonesia yang
              autentik.
            </p>
            <p class="modal-text">
              <strong>Misi:</strong> Menghadirkan cemilan berkualitas tinggi,
              memberdayakan produk lokal, dan menciptakan peluang ekonomi
              positif bagi masyarakat sekitar.
            </p>

            <h3 class="modal-title">🔥 Arti Nama "CIMOHOT"</h3>
            <p class="modal-text">
              <strong>CIMOHOT</strong> adalah singkatan dari
              <strong>"Cireng Isi Montok Hot"</strong> yang memiliki makna:
            </p>
            <ul class="modal-list">
              <li>
                <strong>Cireng Isi:</strong> Cemilan legendaris yang disukai
                semua kalangan
              </li>
              <li><strong>Montok:</strong> Isi yang melimpah dan memuaskan</li>
              <li>
                <strong>Hot:</strong> Cita rasa pedas khas yang menggoda selera
              </li>
            </ul>

            <h3 class="modal-title">✨ Keunggulan Kami</h3>
            <div class="info-grid">
              <div class="info-card">
                <h4>🍴 Rasa Autentik</h4>
                <p>
                  Resep rahasia dengan cita rasa yang konsisten dan menggoda
                </p>
              </div>
              <div class="info-card">
                <h4>🥟 Isi Montok</h4>
                <p>Porsi isi yang berlimpah dalam setiap gigitan</p>
              </div>
              <div class="info-card">
                <h4>🌶️ Level Pedas</h4>
                <p>Berbagai pilihan tingkat kepedasan sesuai selera</p>
              </div>
              <div class="info-card">
                <h4>🏆 Kualitas Terjamin</h4>
                <p>Bahan berkualitas tinggi dan proses pembuatan higienis</p>
              </div>
            </div>
          </div>
        </div>

        <section class="section food-menu" id="food-menu">
                <div class="container">
                    <h2 class="h2 section-title">Menu <span class="span">Cimohot</span></h2>
                    <p class="section-text">Cimohot adalah camilan gurih berbahan dasar tepung tapioka yang  disajikan panas dengan bumbu khas.</p>
                    <ul class="fiter-list">
                        <li><button class="filter-btn active" data-filter="all">All</button></li>
                        <li><button class="filter-btn" data-filter="original">Original</button></li>
                        <li><button class="filter-btn" data-filter="pedas">Pedas</button></li>
                        <li><button class="filter-btn" data-filter="minuman">Minuman</button></li>
                    </ul>
                    <ul class="food-menu-list"></ul>
                </div>
            </section>
        <!-- 
        - #CTA
      -->

        <section
          class="section section-divider white cta"
          style="background-image: url('./assets/images/hero-bg-cta.svg')"
        >
          <div class="container">
            <div class="cta-content">
              <h2 class="h2 section-title">
                Jajanan Lokal Rasa
                <span class="span">Juara</span>
              </h2>

              <p class="section-text">
                Dibuat dari resep rumahan dengan bahan berkualitas, Cimohot siap
                manjain lidah kamu kapan pun lapar atau lagi pengen camilan
                berbumbu tajam!
              </p>

              <a href="#food-menu" class="btn btn-hover">Order Now</a>
            </div>

            <figure class="cta-banner">
              <img
                src="./assets/images/cta-banner.svg"
                width="700"
                height="637"
                loading="lazy"
                alt="cireng"
                class="w-100 cta-img"
              />

              <img
                src="./assets/images/sale-shape.png"
                width="216"
                height="226"
                loading="lazy"
                alt="get up to 50% off now"
                class="abs-img scale-up-anim"
              />
            </figure>
          </div>
        </section>

        <!-- 
        - #TESTIMONIALS
      -->

        <section class="section section-divider white testi">
          <div class="container">
            <p class="section-subtitle">Testimonials</p>

            <h2 class="h2 section-title">
              Our Customers <span class="span">Reviews</span>
            </h2>

            <p class="section-text">
              Gurihnya cimol, pedasnya sambal level akhirat, bikin kamu gak cuma
              kenyang, tapi ketagihan.
            </p>

            <ul class="testi-list has-scrollbar">
              <li class="testi-item">
                <div class="testi-card">
                  <div class="profile-wrapper">
                    <figure class="avatar">
                      <img
                        src="./assets/images/avatar-1.jpg"
                        width="80"
                        height="80"
                        loading="lazy"
                        alt="Ade Mariani"
                      />
                    </figure>

                    <div>
                      <h3 class="h4 testi-name">Ade Mariani</h3>

                      <p class="testi-title">Pacar Jhoi</p>
                    </div>
                  </div>

                  <blockquote class="testi-text">
                    "Gak nyangka cimol bisa seenak ini. Luarnya krenyes,
                    dalemnya empuk, sambalnya NANGISIN 😭🔥. Repeat order?
                    Wajib!."
                  </blockquote>

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>
              </li>

              <li class="testi-item">
                <div class="testi-card">
                  <div class="profile-wrapper">
                    <figure class="avatar">
                      <img
                        src="./assets/images/avatar-2.jpg"
                        width="80"
                        height="80"
                        loading="lazy"
                        alt="Cemara"
                      />
                    </figure>

                    <div>
                      <h3 class="h4 testi-name">Cemara</h3>

                      <p class="testi-title">Customer</p>
                    </div>
                  </div>

                  <blockquote class="testi-text">
                    "Kami sangat puas dengan produk Cimohot. Rasa dan
                    kualitasnya konsisten, pengemasannya rapi, dan pelayanan
                    juga cepat. Recommended! "
                  </blockquote>

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>
              </li>

              <li class="testi-item">
                <div class="testi-card">
                  <div class="profile-wrapper">
                    <figure class="avatar">
                      <img
                        src="./assets/images/avatar-3.jpg"
                        width="80"
                        height="80"
                        loading="lazy"
                        alt="Orang Tua"
                      />
                    </figure>

                    <div>
                      <h3 class="h4 testi-name">Duo Nyengir</h3>

                      <p class="testi-title">Customer</p>
                    </div>
                  </div>

                  <blockquote class="testi-text">
                    "Ai amang, cimol apalah ini. Pedas kali, tapi enak! Mulai
                    tadi mulutku marobat, tapi tangan tetap makan aja. Boleh
                    juga ini, cocok kali buat tamu"
                  </blockquote>

                  <div class="rating-wrapper">
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                    <ion-icon name="star"></ion-icon>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>

        <!-- 
        - #BLOG
      -->
        <section class="section blog" id="blog">
          <div class="container">
            <p class="section-subtitle">Blog</p>

            <h2 class="section-title">
              Semua Tentang <span class="span">Cimohot</span>
            </h2>

            <p class="section-text">
              Cireng Isi Montok Hot - Camilan pedas yang bikin nagih dengan
              isian melimpah dan cita rasa yang tak terlupakan.
            </p>

            <ul class="blog-list">
              <li>
                <div class="blog-card">
                  <div class="card-banner">
                    <img src="assets/images/blog-1.png"
                      width="600"
                      height="390"
                      loading="lazy"
                      alt="Apa Itu Cimohot?"
                    />
                    <div class="badge">Perkenalan</div>
                  </div>

                  <div class="card-content">
                    <div class="card-meta-wrapper">
                      <a href="#" class="card-meta-link">
                        <svg class="icon" viewBox="0 0 24 24">
                          <path
                            d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"
                          />
                        </svg>
                        <time class="meta-info" datetime="2024-12-01"
                          >Des 01 2024</time
                        >
                      </a>

                      <a href="#" class="card-meta-link">
                        <svg class="icon" viewBox="0 0 24 24">
                          <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                          />
                        </svg>
                        <p class="meta-info">Tim Cimohot</p>
                      </a>
                    </div>

                    <h3>
                      <a href="#" class="card-title"
                        >Apa Itu Cimohot? Kenalan Yuk dengan Cireng Isi Montok
                        Hot!</a
                      >
                    </h3>

                    <p class="card-text">
                      Cimohot adalah singkatan dari "Cireng Isi Montok Hot" -
                      camilan khas yang menggabungkan kelezatan cireng
                      tradisional dengan isian melimpah dan sensasi pedas yang
                      bikin ketagihan...
                    </p>

                    <button class="btn-link" onclick="openModal('modal-1')">
                      <span>Baca Selengkapnya</span>
                      <svg class="icon" viewBox="0 0 24 24">
                        <path
                          d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
              </li>

              <li>
                <div class="blog-card">
                  <div class="card-banner">
                    <img src="assets/images/blog-2.png"
                      width="600"
                      height="390"
                      loading="lazy"
                      alt="Kenapa Cimohot Bisa Bikin Nagih?"
                    />
                    <div class="badge">Review</div>
                  </div>

                  <div class="card-content">
                    <div class="card-meta-wrapper">
                      <a href="#" class="card-meta-link">
                        <svg class="icon" viewBox="0 0 24 24">
                          <path
                            d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"
                          />
                        </svg>
                        <time class="meta-info" datetime="2024-12-02"
                          >Des 02 2024</time
                        >
                      </a>

                      <a href="#" class="card-meta-link">
                        <svg class="icon" viewBox="0 0 24 24">
                          <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                          />
                        </svg>
                        <p class="meta-info">Tim Cimohot</p>
                      </a>
                    </div>

                    <h3>
                      <a href="#" class="card-title"
                        >Kenapa Cimohot Bisa Bikin Nagih? Ini Rahasianya!</a
                      >
                    </h3>

                    <p class="card-text">
                      Ada alasan ilmiah kenapa sekali nyoba Cimohot, pasti balik
                      lagi. Kombinasi tekstur, rasa, dan sensasi pedasnya
                      menciptakan pengalaman yang sulit dilupakan...
                    </p>

                    <button class="btn-link" onclick="openModal('modal-2')">
                      <span>Baca Selengkapnya</span>
                      <svg class="icon" viewBox="0 0 24 24">
                        <path
                          d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
              </li>

              <li>
                <div class="blog-card">
                  <div class="card-banner">
                    <img src="assets/images/blog-3.png"
                      width="600"
                      height="390"
                      loading="lazy"
                      alt="Level Pedas Cimohot"
                    />
                    <div class="badge">Guide</div>
                  </div>

                  <div class="card-content">
                    <div class="card-meta-wrapper">
                      <a href="#" class="card-meta-link">
                        <svg class="icon" viewBox="0 0 24 24">
                          <path
                            d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z"
                          />
                        </svg>
                        <time class="meta-info" datetime="2024-12-03"
                          >Des 03 2024</time
                        >
                      </a>

                      <a href="#" class="card-meta-link">
                        <svg class="icon" viewBox="0 0 24 24">
                          <path
                            d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"
                          />
                        </svg>
                        <p class="meta-info">Tim Cimohot</p>
                      </a>
                    </div>

                    <h3>
                      <a href="#" class="card-title"
                        >Level Pedas Cimohot: Pilih yang Cocok Buat Kamu!</a
                      >
                    </h3>

                    <p class="card-text">
                      Dari yang lembut untuk anak-anak sampai yang extreme untuk
                      para pemberani! Temukan level pedas Cimohot yang pas
                      dengan kepribadian dan toleransi pedasmu...
                    </p>

                    <button class="btn-link" onclick="openModal('modal-3')">
                      <span>Baca Selengkapnya</span>
                      <svg class="icon" viewBox="0 0 24 24">
                        <path
                          d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"
                        />
                      </svg>
                    </button>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </section>
      </article>
    </main>

    <div id="modal-1" class="modal">
  <div class="modal-content">
    <div class="modal-header">
      <h2 class="modal-title">Mengenal CIMOHOT: Cireng Isi Montok & Hot!</h2>
      <button class="close-btn" onclick="closeModal('modal-1')">&times;</button>
    </div>
    <div class="modal-body">
      <div class="highlight-box">
        <h3>🔥 Lupakan Cireng Biasa!</h3>
        <p>
          Cimohot adalah revolusi cireng isi yang kenyal di dalam dan super renyah di luar. Diciptakan dengan resep rahasia dan isian 'montok' yang melimpah, setiap gigitan memberikan sensasi pedas khas yang dijamin bikin nagih. Inilah arti dari <strong>"Pedasnya Nampol!"</strong>.
        </p>
      </div>

      <h3>✨ Kenapa Cimohot Spesial?</h3>
      <ul class="modal-list" style="list-style: none; padding-left: 0;">
        <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <ion-icon name="layers-outline" style="color: var(--deep-saffron); font-size: 1.5rem;"></ion-icon>
          <span><strong>Tekstur Sempurna:</strong> Kombinasi kulit crispy dengan adonan kenyal dan isian yang lembut.</span>
        </li>
        <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <ion-icon name="cube-outline" style="color: var(--deep-saffron); font-size: 1.5rem;"></ion-icon>
          <span><strong>Isian Melimpah & Beragam:</strong> Dari ayam pedas hingga lelehan keju premium, isian kami selalu penuh rasa.</span>
        </li>
        <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <ion-icon name="key-outline" style="color: var(--deep-saffron); font-size: 1.5rem;"></ion-icon>
          <span><strong>Bumbu Rahasia:</strong> Racikan bumbu spesial turun-temurun yang menciptakan cita rasa unik.</span>
        </li>
         <li style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
          <ion-icon name="expand-outline" style="color: var(--deep-saffron); font-size: 1.5rem;"></ion-icon>
          <span><strong>Ukuran Memuaskan:</strong> Didesain agar setiap cirengnya pas untuk memuaskan hasrat ngemil.</span>
        </li>
      </ul>

      <div class="highlight-box">
        <h3>🏆 Varian Isian Juara Kami</h3>
        <ul class="modal-list" style="padding-left: 20px;">
            <li><strong>Ayam Suwir Pedas:</strong> Suwiran ayam lembut dibalut bumbu pedas manis yang meresap sempurna.</li>
            <li><strong>Keju Mozarella:</strong> Lelehan keju premium yang gurih dan lumer di setiap gigitan.</li>
            <li><strong>Sosis Pedas:</strong> Potongan sosis berkualitas dengan tendangan bumbu pedas yang mantap.</li>
            <li><strong>Kombinasi Spesial:</strong> Perpaduan semua isian favorit dalam satu gigitan ultimate!</li>
        </ul>
      </div>

      <p style="text-align: center; font-size: 1.2rem; color: var(--deep-saffron); font-weight: 600; margin-top: 30px;">
        Cimohot lebih dari sekadar camilan; ini adalah pengalaman rasa yang akan selalu kamu ingat. Siap ketagihan dari gigitan pertama? 🤤
      </p>
    </div>
  </div>
</div>

    <!-- Modal 2 - Kenapa Bikin Nagih -->
    <div id="modal-2" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">Kenapa Cimohot Bikin Nagih?</h2>
          <button class="close-btn" onclick="closeModal('modal-2')">
            &times;
          </button>
          
        </div>
        <div class="modal-body">
          <div class="highlight-box">
            <h3>🧠 The Science Behind Addiction</h3>
            <p>
              Cimohot dirancang khusus untuk memberikan "perfect bite
              experience" - kombinasi tekstur, rasa, dan sensasi yang merangsang
              semua indra sekaligus!
            </p>
          </div>

          <h3>🎯 1. Tekstur Multi-Layer yang Bikin Penasaran:</h3>
          <p>
            <strong>• Crunch Factor:</strong> Lapisan luar yang crispy
            memberikan kepuasan mengunyah
          </p>
          <p>
            <strong>• Chewy Inside:</strong> Bagian dalam yang kenyal bikin kamu
            terus ngunyah
          </p>
          <p>
            <strong>• Molten Center:</strong> Isian yang meleleh memberikan
            surprise di setiap gigitan
          </p>

          <h3>🌶️ 2. Sensasi Level Pedas yang Nendang:</h3>
          <p>
            Bukan pedas yang bikin sakit, tapi pedas yang bikin nagih! Kombinasi
            cabai pilihan dengan bumbu rempah menciptakan "pedas manis" yang
            unik - awalnya panas, lalu ada rasa manis yang menenangkan, dan
            ujung-ujungnya bikin pengen lagi!
          </p>

          <div class="highlight-box">
            <h3>⏱️ 3. Reaksi Orang Pertama Kali Nyoba:</h3>
            <p><strong>• Detik 1-5:</strong> "Wah, crispy banget ini!"</p>
            <p>
              <strong>• Detik 6-10:</strong> "Eh, isiannya melimpah banget!"
            </p>
            <p><strong>• Detik 11-15:</strong> "Pedasnya pas, enak!"</p>
            <p>
              <strong>• Detik 16-20:</strong> "Habis satu, pengen lagi deh!"
            </p>
          </div>

          <h3>💭 4. Faktor Psikologis:</h3>
          <p>
            Cimohot memberikan "comfort food feeling" - sensasi hangat, kenyang,
            dan puas yang bikin mood jadi baik. Plus, ukurannya yang jumbo
            memberikan "value satisfaction" - berasa dapet banyak dengan harga
            yang reasonable.
          </p>

          <div
            style="
              background: #f0f8ff;
              padding: 20px;
              border-radius: 10px;
              margin: 20px 0;
            "
          >
            <h3>💬 Testimoni Real Customer:</h3>
            <p>
              <em
                >"Gila sih ini cireng, pertama kali nyoba langsung pesen 10 biji
                lagi. Ketagihan parah!"</em
              >
              - Sarah, 23
            </p>
            <p>
              <em
                >"Udah seminggu ini tiap hari beli Cimohot. Help me please!"</em
              >
              - Andi, 28
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal 3 - Level Pedas -->
    <div id="modal-3" class="modal">
      <div class="modal-content">
        <div class="modal-header">
          <h2 class="modal-title">Level Pedas Cimohot Guide</h2>
          <button class="close-btn" onclick="closeModal('modal-3')">
            &times;
          </button>
        </div>
        <div class="modal-body">
          <div
            style="
              background: linear-gradient(135deg, #fff3cd, #ffeaa7);
              padding: 20px;
              border-radius: 10px;
              margin-bottom: 20px;
            "
          >
            <h3>🌶️ LEVEL 0 - "BABY FRIENDLY"</h3>
            <p>
              <strong>Target:</strong> Anak-anak, ibu hamil, yang lagi sakit
              tenggorokan
            </p>
            <p><strong>Sensasi:</strong> Gurih manis tanpa pedas sama sekali</p>
            <p>
              <strong>Reaksi:</strong> "Enak banget! Anak saya suka banget nih!"
            </p>
          </div>

          <div
            style="
              background: linear-gradient(135deg, #d1ecf1, #74b9ff);
              padding: 20px;
              border-radius: 10px;
              margin-bottom: 20px;
            "
          >
            <h3>🌶️ LEVEL 1 - "GENTLE HEAT"</h3>
            <p>
              <strong>Target:</strong> Pemula pedas, yang biasa makan indomi
              goreng
            </p>
            <p>
              <strong>Sensasi:</strong> Hangat di lidah, bikin berkeringat tipis
            </p>
            <p><strong>Reaksi:</strong> "Ini sih enak, gak terlalu pedas"</p>
          </div>

          <div
            style="
              background: linear-gradient(135deg, #d4fdd4, #00b894);
              padding: 20px;
              border-radius: 10px;
              margin-bottom: 20px;
            "
          >
            <h3>🌶️🌶️ LEVEL 2 - "STANDARD KICK"</h3>
            <p>
              <strong>Target:</strong> Penggemar mie ayam pakai cabe, fans
              rendang
            </p>
            <p><strong>Sensasi:</strong> Pedas sedang, bikin bibir bengkrang</p>
            <p><strong>Reaksi:</strong> "Pas banget nih pedasnya!"</p>
          </div>

          <div
            style="
              background: linear-gradient(135deg, #fab1a0, #e17055);
              padding: 20px;
              border-radius: 10px;
              margin-bottom: 20px;
            "
          >
            <h3>🌶️🌶️🌶️ LEVEL 3 - "SPICY LOVERS"</h3>
            <p>
              <strong>Target:</strong> Yang doyan seblak level 3, penggemar ayam
              geprek
            </p>
            <p><strong>Sensasi:</strong> Pedas nendang, hidung meler dikit</p>
            <p><strong>Reaksi:</strong> "Wah mantap nih, berasa banget!"</p>
          </div>

          <div
            style="
              background: linear-gradient(135deg, #fd79a8, #e84393);
              padding: 20px;
              border-radius: 10px;
              margin-bottom: 20px;
            "
          >
            <h3>🌶️🌶️🌶️🌶️ LEVEL 4 - "BRAVE SOULS"</h3>
            <p>
              <strong>Target:</strong> Yang biasa makan mie super pedas, pecinta
              sambel
            </p>
            <p><strong>Sensasi:</strong> Pedas banget, keringat bercucuran</p>
            <p><strong>Reaksi:</strong> "Gila nih pedas, tapi enak!"</p>
          </div>

          <div
            style="
              background: linear-gradient(135deg, #ff7675, #d63031);
              color: white;
              padding: 20px;
              border-radius: 10px;
              margin-bottom: 20px;
            "
          >
            <h3>🌶️🌶️🌶️🌶️🌶️ LEVEL 5 - "EXTREME WARRIORS"</h3>
            <p>
              <strong>Target:</strong> Pecinta tantangan, food challenger, yang
              immune sama pedas
            </p>
            <p>
              <strong>Sensasi:</strong> BRUTAL! Lidah kebas, air mata keluar
            </p>
            <p>
              <strong>Reaksi:</strong> "ANJIR PEDAS BANGET! *sambil nangis tapi
              tetep makan*"
            </p>
          </div>

          <div class="highlight-box">
            <h3>📝 Tips Memilih Level:</h3>
            <p><strong>• Pemula:</strong> Mulai dari Level 0-1</p>
            <p><strong>• Penyuka pedas biasa:</strong> Level 2-3</p>
            <p><strong>• Hardcore spicy lovers:</strong> Level 4-5</p>
            <p><strong>• Lagi sakit/hamil:</strong> Stick to Level 0</p>
            <p>
              <strong>• Mau coba challenge:</strong> Langsung Level 5 (siap-siap
              viral!)
            </p>
          </div>

          <div
            style="
              background: #ffebee;
              border: 2px solid #f44336;
              padding: 15px;
              border-radius: 8px;
              margin-top: 20px;
            "
          >
            <h3>⚠️ Warning:</h3>
            <p>
              Level 4-5 tidak disarankan untuk penderita maag, ibu hamil, atau
              yang punya masalah pencernaan. Makan dengan risiko sendiri ya! 😄
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- 
    - #FOOTER
  -->

    <footer class="footer" id="contact">
    <div class="footer-top">
        <div class="container">
            <div class="footer-brand">
                <a href="index.php" class="logo">
                    <img src="assets/images/logo cimohot.png" alt="Cimohot Logo" class="logo-img" style="height: 90px; width: 90px;">
                </a>
                <p class="footer-text">
                    Camilan cireng premium dengan isian melimpah dan rasa autentik yang bikin ketagihan.
                </p>
            </div>

            <ul class="footer-list">
                <li><p class="footer-list-title">Navigasi</p></li>
                <li><a href="#home" class="footer-link">Home</a></li>
                <li><a href="#about" class="footer-link">About Us</a></li>
                <li><a href="#food-menu" class="footer-link">Menu</a></li>
                <li><a href="#blog" class="footer-link">Blog</a></li>
            </ul>

            <ul class="footer-list">
                <li><p class="footer-list-title">Kontak & Lokasi</p></li>
                <li class="footer-list-item">
                    <ion-icon name="location-outline"></ion-icon>
                    <address>Jl Muncang Raya No 55, Koja, Jakarta Utara</address>
                </li>
                <li class="footer-list-item">
                    <ion-icon name="call-outline"></ion-icon>
                    <span>+62-893-019-4086</span>
                </li>
                <li class="footer-list-item">
                    <ion-icon name="mail-outline"></ion-icon>
                    <span>jhoi.wada324@gmail.com</span>
                </li>
            </ul>

            <div class="footer-list">
                <p class="footer-list-title">Terhubung Dengan Kami</p>
                <p class="footer-text">Dapatkan info promo dan menu baru langsung dari kami!</p>
                <ul class="social-list">
                <li>
                    <a href="https://www.instagram.com/cireng_isi_montok_hot_official/" target="_blank" class="social-link" aria-label="Instagram">
                        <ion-icon name="logo-instagram"></ion-icon>
                    </a>
                  </li>
                <li>
                    <a href="https://www.tiktok.com/@cimohot.official" target="_blank" class="social-link" aria-label="TikTok">
                        <ion-icon name="logo-tiktok"></ion-icon>
                    </a>
                  </li>
                <li>
                    <a href="https://wa.me/6285930194086" target="_blank" class="social-link" aria-label="WhatsApp">
                        <ion-icon name="logo-whatsapp"></ion-icon>
                    </a>
                  </li>
              </ul>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p class="copyright">&copy; 2025 Cimohot. Dibuat dengan 🔥 oleh Jhoiwada Simanjuntak.</p>
        </div>
    </div>
</footer>

    <!-- 
    - #BACK TO TOP
  -->

    <a
      href="#top"
      class="back-top-btn"
      aria-label="Back to top"
      data-back-top-btn
    >
      <ion-icon name="chevron-up"></ion-icon>
    </a>

    <!-- 
    - custom js link
  -->
   <script src="assets/js/auth-check.js"></script>
    <script src="assets/js/script.js"></script>

    <!-- 
    - ionicon link
  -->
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
  </body>
</html>
