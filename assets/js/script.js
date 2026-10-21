/**
 * CIMOHOT - CORE CLIENT-SIDE JAVASCRIPT
 * Modern, Interactive, Anti-Sloop E-Commerce Logic
 */

"use strict";

let allMenuItems = [];
let currentCategoryFilter = "all";
let currentSearchQuery = "";
let currentUser = null;
let quickViewItem = null;
let quickViewQuantity = 1;

// =====================================================================
// UTILITY & BADGE HELPERS
// =====================================================================

function updateCartBadge(count) {
  const cartBadges = document.querySelectorAll(".cart-badge, .dock-cart-badge");
  cartBadges.forEach((badge) => {
    if (count > 0) {
      badge.textContent = count > 99 ? "99+" : count;
      badge.style.display = "flex";
    } else {
      badge.style.display = "none";
    }
  });
}

function formatRupiah(num) {
  return "Rp " + Number(num).toLocaleString("id-ID");
}

function createRatingStars(rating) {
  let stars = "";
  rating = parseFloat(rating) || 5;
  for (let i = 1; i <= 5; i++) {
    if (i <= rating) stars += '<ion-icon name="star"></ion-icon>';
    else if (i - 0.5 <= rating) stars += '<ion-icon name="star-half-outline"></ion-icon>';
    else stars += '<ion-icon name="star-outline"></ion-icon>';
  }
  return `<span class="rating-stars">${stars} <span class="rating-score">${rating.toFixed(1)}</span></span>`;
}

// =====================================================================
// MENU CARD RENDERING
// =====================================================================

function createMenuItemHTML(item) {
  const isOutOfStock = !item.stock || item.stock <= 0;
  const isBestSeller = item.rating >= 4.9 || item.id === 9 || item.id === 25;
  const isPedas = (item.category && item.category.toLowerCase() === "pedas") || item.name.toLowerCase().includes("pedas") || item.name.toLowerCase().includes("mercon");

  let badgeHTML = "";
  if (item.category) {
    badgeHTML += `<span class="badge-tag category">${item.category}</span>`;
  }
  if (isPedas) {
    badgeHTML += `<span class="badge-tag pedas"><ion-icon name="flame"></ion-icon> Pedas</span>`;
  }
  if (isBestSeller) {
    badgeHTML += `<span class="badge-tag bestseller">⭐ Favorit</span>`;
  }

  let stockClass = "stock-pill tersedia";
  let stockText = `Stok: ${item.stock}`;
  if (item.stock <= 0) {
    stockClass = "stock-pill habis";
    stockText = "Stok Habis";
  } else if (item.stock <= 10) {
    stockClass = "stock-pill rendah";
    stockText = `Sisa ${item.stock}`;
  }

  let buttonHTML;
  if (window.currentUser && window.currentUser.role === "admin") {
    buttonHTML = `<button class="food-order-btn btn-disabled" disabled><ion-icon name="shield-checkmark-outline"></ion-icon> Admin</button>`;
  } else if (isOutOfStock) {
    buttonHTML = `<button class="food-order-btn btn-disabled" disabled><ion-icon name="close-circle-outline"></ion-icon> Habis</button>`;
  } else {
    buttonHTML = `<button class="food-order-btn add-to-cart-trigger" data-menu-id="${item.id}" aria-label="Pesan ${item.name}"><ion-icon name="cart-outline"></ion-icon> Pesan</button>`;
  }

  return `
    <li class="menu-item-wrapper" data-category="${(item.category || '').toLowerCase()}" data-id="${item.id}">
      <article class="food-menu-card ${isOutOfStock ? 'out-of-stock' : ''}">
        <div class="card-media-wrapper" onclick="openQuickViewModal(${item.id})">
          <img src="${item.image}" alt="${item.name}" class="card-food-img" loading="lazy" onerror="this.src='assets/images/food-menu-1.png'">
          <div class="card-badges-top">
            ${badgeHTML}
          </div>
          <button class="quick-view-overlay-btn" type="button" aria-label="Lihat detail ${item.name}">
            <ion-icon name="eye-outline"></ion-icon> Detail
          </button>
        </div>

        <div class="card-content-wrapper">
          <div class="card-meta-row">
            ${createRatingStars(item.rating)}
            <span class="${stockClass}">${stockText}</span>
          </div>

          <h3 class="card-food-title" onclick="openQuickViewModal(${item.id})">${item.name}</h3>
          <p class="card-food-desc">${item.description || 'Camilan cireng montok istimewa resep autentik Cimohot.'}</p>

          <div class="card-footer-row">
            <div class="price-box">
              <span class="price-label">Harga</span>
              <span class="price-val">${item.price_formatted}</span>
            </div>
            ${buttonHTML}
          </div>
        </div>
      </article>
    </li>
  `;
}

/**
 * Filter menu berdasarkan kategori dan input pencarian secara instan
 */
function filterAndRenderMenu() {
  const container = document.querySelector(".food-menu-list");
  if (!container) return;

  const query = currentSearchQuery.trim().toLowerCase();
  const filtered = allMenuItems.filter((item) => {
    const matchCategory =
      currentCategoryFilter === "all" ||
      (item.category && item.category.toLowerCase() === currentCategoryFilter);

    const matchQuery =
      !query ||
      item.name.toLowerCase().includes(query) ||
      (item.description && item.description.toLowerCase().includes(query)) ||
      (item.category && item.category.toLowerCase().includes(query));

    return matchCategory && matchQuery;
  });

  if (filtered.length > 0) {
    container.innerHTML = filtered.map(createMenuItemHTML).join("");
  } else {
    container.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px; background: #fff; border-radius: 16px; border: 1px dashed var(--border-light);">
        <ion-icon name="search-outline" style="font-size: 3rem; color: var(--text-light); margin-bottom: 12px;"></ion-icon>
        <h4 style="font-size: 1.2rem; font-weight: 800; color: var(--text-dark); margin-bottom: 6px;">Menu Tidak Ditemukan</h4>
        <p style="color: var(--text-muted); font-size: 0.95rem;">Tidak ada menu yang cocok dengan kata kunci "<strong>${currentSearchQuery}</strong>". Silakan coba kata kunci lain.</p>
        <button class="btn btn-secondary" style="margin-top: 16px;" onclick="resetSearchAndFilter()">Reset Pencarian</button>
      </div>
    `;
  }

  attachOrderButtonEvents();
}

function resetSearchAndFilter() {
  currentCategoryFilter = "all";
  currentSearchQuery = "";
  const searchInput = document.getElementById("menuSearchInput");
  if (searchInput) searchInput.value = "";
  const clearBtn = document.getElementById("clearSearchBtn");
  if (clearBtn) clearBtn.style.display = "none";

  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.classList.toggle("active", btn.dataset.filter.toLowerCase() === "all");
  });

  filterAndRenderMenu();
}

/**
 * Memuat data menu dari API
 */
async function loadAndRenderMenu() {
  const menuListContainer = document.querySelector(".food-menu-list");
  if (!menuListContainer) return;

  menuListContainer.innerHTML = `
    <div style="grid-column: 1 / -1; text-align:center; padding: 60px 20px;">
      <div style="display:inline-block; width: 40px; height: 40px; border: 3px solid var(--primary-light); border-top-color: var(--primary); border-radius: 50%; animation: spin 0.8s linear infinite;"></div>
      <p style="margin-top: 14px; font-weight: 600; color: var(--text-muted);">Memuat menu pilihan Cimohot...</p>
    </div>
  `;

  try {
    const response = await fetch("/api.php?action=get_menu&category=all");
    if (!response.ok) throw new Error("Gagal memuat API");
    const result = await response.json();
    if (result.success && Array.isArray(result.data.menu)) {
      allMenuItems = result.data.menu;
      filterAndRenderMenu();
      initSpiceSimulatorMenu();
    } else {
      throw new Error(result.message || "Data menu tidak valid");
    }
  } catch (error) {
    console.error("Error loading menu:", error);
    menuListContainer.innerHTML = `
      <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px;">
        <p style="color: #DC2626; font-weight: 700;">Gagal memuat menu: ${error.message}</p>
        <button class="btn btn-primary" style="margin-top: 12px;" onclick="loadAndRenderMenu()">Coba Lagi</button>
      </div>
    `;
  }
}

// =====================================================================
// QUICK VIEW MODAL LOGIC
// =====================================================================

function openQuickViewModal(menuId) {
  const item = allMenuItems.find((m) => m.id === parseInt(menuId));
  if (!item) return;

  quickViewItem = item;
  quickViewQuantity = 1;

  const modal = document.getElementById("quickViewModal");
  if (!modal) return;

  document.getElementById("qvImg").src = item.image;
  document.getElementById("qvCategory").textContent = item.category || "Menu Cimohot";
  document.getElementById("qvTitle").textContent = item.name;
  document.getElementById("qvPrice").textContent = item.price_formatted;
  document.getElementById("qvDesc").textContent = item.description || "Camilan khas bertekstur krispi di luar dan kenyal montok di dalam, diracik dengan bumbu rempah autentik.";
  document.getElementById("qvQtyDisplay").textContent = quickViewQuantity;

  const qvAddBtn = document.getElementById("qvAddToCartBtn");
  if (item.stock <= 0) {
    qvAddBtn.disabled = true;
    qvAddBtn.innerHTML = "Stok Habis";
    qvAddBtn.classList.add("btn-disabled");
  } else {
    qvAddBtn.disabled = false;
    qvAddBtn.innerHTML = `<ion-icon name="cart-outline"></ion-icon> Tambahkan ke Keranjang`;
    qvAddBtn.classList.remove("btn-disabled");
  }

  modal.classList.add("show");
}

function closeQuickViewModal() {
  const modal = document.getElementById("quickViewModal");
  if (modal) modal.classList.remove("show");
  quickViewItem = null;
}

function changeQuickViewQuantity(delta) {
  if (!quickViewItem) return;
  const newQty = quickViewQuantity + delta;
  const maxStock = quickViewItem.stock || 99;
  if (newQty >= 1 && newQty <= maxStock) {
    quickViewQuantity = newQty;
    document.getElementById("qvQtyDisplay").textContent = quickViewQuantity;
  }
}

// =====================================================================
// KERANJANG BELANJA & CART DRAWER
// =====================================================================

class ShoppingCartManager {
  async addItem(menuId, quantity = 1, buttonElement = null) {
    if (window.currentUser && window.currentUser.role === "admin") {
      Swal.fire({
        icon: "warning",
        title: "Mode Admin Aktif",
        text: "Akun Admin bertugas mengelola restoran dan tidak dapat melakukan pemesanan makanan. Silakan beralih ke akun pelanggan untuk memesan.",
        confirmButtonColor: "#FF3D2E",
        background: "#1F1F1F",
        color: "#FAFAFA",
      });
      return;
    }
    if (!currentUser) {
      // Tampilkan opsi ramah: Login atau Pesan via WhatsApp
      const selectedItem = allMenuItems.find((m) => m.id === parseInt(menuId));
      const itemName = selectedItem ? selectedItem.name : "Cireng";

      Swal.fire({
        icon: "info",
        title: "Pilih Cara Memesan",
        text: `Mau pesan "${itemName}" melalui akun website atau langsung WhatsApp?`,
        showCancelButton: true,
        confirmButtonText: "🔑 Login Akun",
        cancelButtonText: "💬 Langsung WhatsApp",
        confirmButtonColor: "#FF4B26",
        cancelButtonColor: "#25D366",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "/login";
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          const waText = encodeURIComponent(
            `Halo Cimohot! Saya ingin memesan ${quantity}x ${itemName}. Mohon informasi total & ongkirnya ya!`
          );
          window.open(`https://wa.me/6285930194086?text=${waText}`, "_blank");
        }
      });
      return;
    }

    if (buttonElement) {
      const origText = buttonElement.innerHTML;
      buttonElement.innerHTML = `<ion-icon name="sync-outline" class="spin-icon"></ion-icon> Menambahkan...`;
      buttonElement.disabled = true;
    }

    try {
      const params = new URLSearchParams({
        action: "add_to_cart",
        menu_id: menuId,
        quantity: quantity,
      });

      const response = await fetch("/api.php", {
        method: "POST",
        body: params,
      });

      const result = await response.json();
      if (result.success) {
        if (result.data && typeof result.data.new_cart_count !== "undefined") {
          updateCartBadge(result.data.new_cart_count);
        }
        this.animateCartButton();

        // Tampilkan feedback sukses
        const Toast = Swal.mixin({
          toast: true,
          position: "top-end",
          showConfirmButton: false,
          showCloseButton: true,
          timer: 2200,
          timerProgressBar: true,
          showClass: {
            popup: "swal2-show",
          },
          hideClass: {
            popup: "swal2-hide",
          },
          customClass: {
            popup: "cimohot-toast",
          },
          didOpen: (toast) => {
            toast.addEventListener("click", () => Swal.close());
            setTimeout(() => {
              if (toast && document.body.contains(toast)) Swal.close();
            }, 2400);
          },
        });
        Toast.fire({
          icon: "success",
          title: "Berhasil ditambahkan ke keranjang!",
        });

        // Buka drawer cart untuk preview
        openCartDrawer();
      } else {
        throw new Error(result.message || "Gagal menambahkan item");
      }
    } catch (error) {
      if (error.message && (error.message.includes("login") || error.message.includes("Akses ditolak") || error.message.includes("401"))) {
        checkLoginStatus().then((user) => {
          if (!user) {
            Swal.fire({
              icon: "warning",
              title: "Sesi Belum Aktif / Berakhir",
              text: "Sesi login Anda tidak terdeteksi atau telah berakhir. Silakan masuk kembali ke akun Anda.",
              showCancelButton: true,
              confirmButtonText: "Masuk Sekarang",
              cancelButtonText: "Batal",
              confirmButtonColor: "#FF4B26",
              cancelButtonColor: "#6B7280",
            }).then((res) => {
              if (res.isConfirmed) {
                window.location.href = "/login?redirect=" + encodeURIComponent(window.location.pathname);
              }
            });
          }
        });
      } else {
        Swal.fire("Pemberitahuan", error.message, "error");
      }
    } finally {
      if (buttonElement) {
        buttonElement.innerHTML = `<ion-icon name="checkmark-outline"></ion-icon> Ditambahkan`;
        setTimeout(() => {
          buttonElement.innerHTML = `<ion-icon name="cart-outline"></ion-icon> Pesan`;
          buttonElement.disabled = false;
        }, 1200);
      }
    }
  }

  animateCartButton() {
    const cartBtns = document.querySelectorAll(".cart-btn, .dock-item:nth-child(3)");
    cartBtns.forEach((btn) => {
      btn.style.transform = "scale(1.25)";
      setTimeout(() => (btn.style.transform = ""), 250);
    });
  }
}

const shoppingCart = new ShoppingCartManager();

async function openCartDrawer() {
  const overlay = document.getElementById("cartDrawerOverlay");
  if (!overlay) return;

  overlay.classList.add("open");
  await loadCartDrawerContents();
}

function closeCartDrawer() {
  const overlay = document.getElementById("cartDrawerOverlay");
  if (overlay) overlay.classList.remove("open");
}

async function loadCartDrawerContents() {
  const listEl = document.getElementById("cartDrawerItemsList");
  const subtotalEl = document.getElementById("drawerSubtotalAmount");
  if (!listEl) return;

  if (!currentUser) {
    listEl.innerHTML = `
      <div class="cart-drawer-empty">
        <ion-icon name="bag-handle-outline"></ion-icon>
        <h4 style="font-weight: 800; color: var(--text-dark);">Belum Ada Pesanan</h4>
        <p style="font-size: 0.9rem;">Silakan login untuk menyimpan pesanan ke keranjang belanja Anda.</p>
        <a href="/login" class="btn btn-primary" style="margin-top: 10px;">Login Sekarang</a>
      </div>
    `;
    if (subtotalEl) subtotalEl.textContent = "Rp 0";
    return;
  }

  listEl.innerHTML = `<p style="text-align:center; padding: 30px; color: var(--text-muted);">Memuat item keranjang...</p>`;

  try {
    const response = await fetch("/cart");
    // Untuk simplifikasi, kita dapat redirect ke cart.php saat klik checkout
    // Namun kita juga bisa fetch status item langsung via api jika ada endpoint
    // Sementara kita render fallback yang mulus:
    listEl.innerHTML = `
      <div style="padding: 10px; text-align: center;">
        <ion-icon name="cart" style="font-size: 3rem; color: var(--primary); margin-bottom: 10px;"></ion-icon>
        <h4 style="font-weight: 800; color: var(--text-dark); margin-bottom: 6px;">Keranjang Aktif</h4>
        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 20px;">Item Anda tersimpan dengan aman di server.</p>
        <a href="/cart" class="btn btn-primary" style="width: 100%;">Buka Halaman Checkout Penuh →</a>
      </div>
    `;
  } catch (e) {
    listEl.innerHTML = `<p style="text-align:center; color: #DC2626;">Gagal memuat keranjang.</p>`;
  }
}

// =====================================================================
// SPICE LEVEL SIMULATOR LOGIC
// =====================================================================

const SPICE_LEVEL_DATA = [
  {
    level: 0,
    title: "Level 0 - Manja & Gurih",
    tag: "Aman Buat Anak-Anak",
    quote: '"Gurih manis renyah tanpa pedas sama sekali, nagih abis!"',
    desc: "Cocok untuk yang tidak tahan pedas, anak-anak, atau yang lagi ingin menikmati keaslian rasa daging ayam dan keju gurih.",
    chilis: "⚪⚪⚪⚪⚪",
    recommendedItemName: "Cireng Isi Ayam Original",
    recommendedDrink: "Es Teh Manis",
  },
  {
    level: 1,
    title: "Level 1 - Anget-anget Kuku",
    tag: "Pemula Pedas",
    quote: '"Ada hangat-hangatnya di lidah, bikin mulut gak bisa diem."',
    desc: "Sentuhan lada putih dan cabai merah tipis. Pas untuk yang mau rasa pedas santai tanpa keringat dingin.",
    chilis: "🌶️⚪⚪⚪⚪",
    recommendedItemName: "Cireng Isi Keju Mozzarella",
    recommendedDrink: "Es Lemon Tea Segar",
  },
  {
    level: 2,
    title: "Level 2 - Standard Kick",
    tag: "Paling Pas & Populer",
    quote: '"Pedasnya pas banget! Bikin mata melek tapi tetap nikmat."',
    desc: "Pedas bumbu balado dengan aroma kaffir lime (daun jeruk). Level favorit pecinta jajanan gurih.",
    chilis: "🌶️🌶️⚪⚪⚪",
    recommendedItemName: "Cireng Isi Bakso Pedas Daun Jeruk",
    recommendedDrink: "Kopi Susu Aren Cimohot",
  },
  {
    level: 3,
    title: "Level 3 - Pedas Nampol!",
    tag: "Spicy Lovers",
    quote: '"Nangis bahagia! Bibir merah merona, keringat bercucuran."',
    desc: "Cabai rawit merah melimpah yang dioseng matang. Sangat cocok dinikmati bareng es teh manis dingin.",
    chilis: "🌶️🌶️🌶️⚪⚪",
    recommendedItemName: "Cireng Isi Ayam Pedas Mercon",
    recommendedDrink: "Es Lemon Tea Segar",
  },
  {
    level: 4,
    title: "Level 4 - Brave Souls",
    tag: "Tantangan Serius",
    quote: '"Beneran gila pedasnya, lidah bergetar tapi tangan tetep nyomot!"',
    desc: "Kombinasi cabai rawit setan giling kasar. Hanya untuk pendekar cabai sejati!",
    chilis: "🌶️🌶️🌶️🌶️⚪",
    recommendedItemName: "Cireng Mercon Sapi Gurih",
    recommendedDrink: "Kopi Susu Aren Cimohot",
  },
  {
    level: 5,
    title: "Level 5 - Uji Nyali Akhirat",
    tag: "EXTREME CHALLENGE",
    quote: '"BRUTAL! Air mata keluar, nafas ngos-ngosan, tapi PUAS BANGET!"',
    desc: "Cabai rawit murni level ekstrem! Siapkan minuman dingin sebelum mulai bertarung.",
    chilis: "🌶️🌶️🌶️🌶️🌶️",
    recommendedItemName: "Paket Combo Cimohot Kenyang",
    recommendedDrink: "Es Lemon Tea Segar",
  },
];

function setSpiceSimulatorLevel(levelIndex) {
  const data = SPICE_LEVEL_DATA[levelIndex];
  if (!data) return;

  document.querySelectorAll(".spice-step-btn").forEach((btn, idx) => {
    btn.classList.toggle("active", idx === levelIndex);
  });

  const levelBadge = document.getElementById("simLevelBadge");
  const quote = document.getElementById("simQuote");
  const desc = document.getElementById("simDesc");
  const recTitle = document.getElementById("simRecTitle");
  const recPrice = document.getElementById("simRecPrice");
  const recImg = document.getElementById("simRecImg");
  const recBtn = document.getElementById("simRecOrderBtn");

  if (levelBadge) levelBadge.textContent = `${data.title} • ${data.tag}`;
  if (quote) quote.textContent = data.quote;
  if (desc) desc.textContent = data.desc;

  // Cari item rekomendasi di database
  const matchingItem = allMenuItems.find((m) =>
    m.name.toLowerCase().includes(data.recommendedItemName.toLowerCase())
  ) || allMenuItems[0];

  if (matchingItem) {
    if (recTitle) recTitle.textContent = matchingItem.name;
    if (recPrice) recPrice.textContent = matchingItem.price_formatted;
    if (recImg) recImg.src = matchingItem.image;
    if (recBtn) {
      recBtn.onclick = () => {
        shoppingCart.addItem(matchingItem.id, 1, recBtn);
      };
    }
  }
}

function initSpiceSimulatorMenu() {
  setSpiceSimulatorLevel(3); // Default ke Level 3 Nampol!
}

// =====================================================================
// MANAJEMEN SESI & USER UI
// =====================================================================

function updateLoginStatusUI() {
  const container = document.getElementById("loginStatusContainer");
  const dockAccount = document.getElementById("dockAccountLink");

  if (currentUser) {
    const firstName = currentUser.name.split(" ")[0];
    const initial = currentUser.name.charAt(0).toUpperCase();

    if (container) {
      const existingExternalNavBtn = document.getElementById("adminDashboardNavBtn");
      if (currentUser.role === "admin") {
        if (existingExternalNavBtn) {
          existingExternalNavBtn.style.display = "inline-flex";
        }
        container.innerHTML = `
          ${!existingExternalNavBtn ? `
          <a href="/admin/dashboard" class="btn-dashboard-nav" id="adminDashboardNavBtn" title="Kembali ke Dashboard Admin">
            <ion-icon name="speedometer-outline"></ion-icon>
            <span>Dashboard</span>
          </a>` : ''}
          <div class="user-info-link" style="border: 1px solid rgba(255,61,46,0.35); background: rgba(255,61,46,0.12); cursor:default;">
            <div class="user-avatar" style="background:#FF3D2E;">A</div>
            <span style="color:#FF3D2E;font-weight:800;">Admin</span>
          </div>
          <button class="logout-btn" onclick="handleLogout()" title="Keluar"><ion-icon name="log-out-outline"></ion-icon></button>
        `;
      } else {
        if (existingExternalNavBtn) {
          existingExternalNavBtn.style.display = "none";
        }
        container.innerHTML = `
          <a href="/akun" class="user-info-link" title="Buka Akun Saya">
            <div class="user-avatar">${initial}</div>
            <span>${firstName}</span>
          </a>
          <button class="logout-btn" onclick="handleLogout()" title="Keluar"><ion-icon name="log-out-outline"></ion-icon></button>
        `;
      }
    }
    if (dockAccount) {
      dockAccount.href = currentUser.role === "admin" ? "/admin/dashboard" : "/akun";
      dockAccount.querySelector("span").textContent = currentUser.role === "admin" ? "Admin" : firstName;
    }
  } else if (container && !container.querySelector(".user-info-link")) {
    const existingExternalNavBtn = document.getElementById("adminDashboardNavBtn");
    if (existingExternalNavBtn) {
      existingExternalNavBtn.style.display = "none";
    }
    container.innerHTML = `
      <button class="login-btn" onclick="window.location.href='/login'">
        <ion-icon name="person-outline"></ion-icon> Masuk
      </button>
    `;
    if (dockAccount) {
      dockAccount.href = "/login";
      dockAccount.querySelector("span").textContent = "Masuk";
    }
  }
}

async function checkLoginStatus() {
  try {
    const res = await fetch("/api.php?action=check_session");
    const json = await res.json();
    if (json.data && json.data.loggedIn) {
      currentUser = json.data.user;
      window.currentUser = currentUser;
      updateCartBadge(json.data.cart_count || 0);
    } else {
      currentUser = null;
      window.currentUser = null;
      updateCartBadge(0);
    }
  } catch (e) {
    console.warn("Session check failed", e);
    currentUser = null;
  }
  updateLoginStatusUI();
  return currentUser;
}

function handleLogout() {
  Swal.fire({
    title: "Ingin keluar akun?",
    text: "Pesanan di keranjang Anda tetap tersimpan.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#FF4B26",
    cancelButtonColor: "#6B7280",
    confirmButtonText: "Ya, Keluar",
    cancelButtonText: "Batal",
  }).then((res) => {
    if (res.isConfirmed) {
      window.location.href = "/logout";
    }
  });
}

// =====================================================================
// EVENT LISTENERS & WIRING
// =====================================================================

function attachOrderButtonEvents() {
  document.querySelectorAll(".add-to-cart-trigger").forEach((btn) => {
    btn.onclick = (e) => {
      e.stopPropagation();
      const menuId = btn.dataset.menuId;
      if (menuId) shoppingCart.addItem(parseInt(menuId), 1, btn);
    };
  });
}

function initializeAllEventListeners() {
  // Mobile Nav Toggle
  const toggleBtn = document.querySelector("[data-menu-toggle-btn]");
  const navbar = document.querySelector("[data-navbar]");
  if (toggleBtn && navbar) {
    toggleBtn.addEventListener("click", () => {
      toggleBtn.classList.toggle("active");
      navbar.classList.toggle("active");
    });

    document.querySelectorAll(".navbar-link").forEach((link) => {
      link.addEventListener("click", () => {
        toggleBtn.classList.remove("active");
        navbar.classList.remove("active");
      });
    });
  }

  // Cart Button Click (Desktop & Mobile)
  const cartBtn = document.getElementById("cartBtn");
  if (cartBtn) {
    cartBtn.onclick = (e) => {
      e.preventDefault();
      openCartDrawer();
    };
  }

  const dockCart = document.getElementById("dockCartLink");
  if (dockCart) {
    dockCart.onclick = (e) => {
      e.preventDefault();
      openCartDrawer();
    };
  }

  // Quick View Quantity Buttons
  document.getElementById("qvQtyMinus")?.addEventListener("click", () => changeQuickViewQuantity(-1));
  document.getElementById("qvQtyPlus")?.addEventListener("click", () => changeQuickViewQuantity(1));
  document.getElementById("qvAddToCartBtn")?.addEventListener("click", () => {
    if (quickViewItem) {
      shoppingCart.addItem(quickViewItem.id, quickViewQuantity);
      closeQuickViewModal();
    }
  });

  // Filter Buttons
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentCategoryFilter = btn.dataset.filter.toLowerCase();
      filterAndRenderMenu();
    });
  });

  // Search Input
  const searchInput = document.getElementById("menuSearchInput");
  const clearSearchBtn = document.getElementById("clearSearchBtn");
  if (searchInput) {
    searchInput.addEventListener("input", (e) => {
      currentSearchQuery = e.target.value;
      if (clearSearchBtn) {
        clearSearchBtn.style.display = currentSearchQuery.length > 0 ? "flex" : "none";
      }
      filterAndRenderMenu();
    });
  }

  if (clearSearchBtn) {
    clearSearchBtn.addEventListener("click", () => {
      resetSearchAndFilter();
    });
  }

  // Spice Simulator Step Buttons
  document.querySelectorAll(".spice-step-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const lvl = parseInt(btn.dataset.level);
      setSpiceSimulatorLevel(lvl);
    });
  });

  // Back To Top
  const backTopBtn = document.querySelector("[data-back-top-btn]");
  window.addEventListener("scroll", () => {
    if (backTopBtn) {
      backTopBtn.classList.toggle("active", window.scrollY > 400);
    }
    const header = document.querySelector(".header");
    if (header) {
      header.classList.toggle("active", window.scrollY > 60);
    }
  });

  backTopBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });
}

// Modal generic open/close
function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.add("show");
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.remove("show");
}

// Global modal close on click backdrop
window.addEventListener("click", (e) => {
  if (e.target.classList.contains("modal")) {
    e.target.classList.remove("show");
  }
});

// =====================================================================
// INIT
// =====================================================================

document.addEventListener("DOMContentLoaded", async () => {
  await checkLoginStatus();
  await loadAndRenderMenu();
  initializeAllEventListeners();
  console.log("Cimohot 2026 Redesign Engine Loaded Successfully! 🔥");
});
