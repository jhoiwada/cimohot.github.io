/**
 * CIMOHOT v2 : "Street Kitchen Premium" Interactive Engine
 * Scroll animations, 3D tilt, counter animation, spice simulator, cart
 */

"use strict";

let allMenuItems = [];
let currentCategoryFilter = "all";
let currentSearchQuery = "";
let currentUser = null;
let quickViewItem = null;
let quickViewQuantity = 1;

// =====================================================================
// UTILITIES
// =====================================================================

function updateCartBadge(count) {
  document.querySelectorAll(".cart-badge, .dock-cart-badge").forEach((badge) => {
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
// SCROLL REVEAL ANIMATIONS (IntersectionObserver)
// =====================================================================

function initScrollReveal() {
  const revealElements = document.querySelectorAll(
    ".reveal, .reveal-left, .reveal-right, .reveal-scale"
  );

  if (!revealElements.length) return;

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("revealed");
          observer.unobserve(entry.target);
        }
      });
    },
    {
      threshold: 0.12,
      rootMargin: "0px 0px -40px 0px",
    }
  );

  revealElements.forEach((el) => observer.observe(el));
}

// =====================================================================
// COUNTER ANIMATION (Stats)
// =====================================================================

function animateCounters() {
  const counters = document.querySelectorAll("[data-count]");

  const observer = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const el = entry.target;
          const target = parseFloat(el.dataset.count);
          const suffix = el.querySelector("span")?.textContent || "";
          const isDecimal = target % 1 !== 0;
          const duration = 2000;
          const startTime = performance.now();

          function updateCount(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const eased = 1 - Math.pow(1 - progress, 3);
            const current = eased * target;

            if (isDecimal) {
              el.childNodes[0].textContent = current.toFixed(1);
            } else {
              el.childNodes[0].textContent = Math.floor(current).toLocaleString("id-ID");
            }

            if (progress < 1) {
              requestAnimationFrame(updateCount);
            }
          }

          requestAnimationFrame(updateCount);
          observer.unobserve(el);
        }
      });
    },
    { threshold: 0.5 }
  );

  counters.forEach((counter) => observer.observe(counter));
}

// =====================================================================
// SCROLL PROGRESS BAR
// =====================================================================

function initScrollProgress() {
  const progressBar = document.getElementById("scrollProgress");
  if (!progressBar) return;

  window.addEventListener("scroll", () => {
    const scrollTop = window.scrollY;
    const docHeight = document.documentElement.scrollHeight - window.innerHeight;
    const scrollPercent = docHeight > 0 ? (scrollTop / docHeight) * 100 : 0;
    progressBar.style.width = scrollPercent + "%";
  });
}

// =====================================================================
// NAVBAR SCROLL BEHAVIOR
// =====================================================================

function initNavbarScroll() {
  const header = document.querySelector(".header");
  const closeBtn = document.getElementById("closeAnnouncementBtn");
  const announcementBar = document.getElementById("topAnnouncementBar");

  if (closeBtn && announcementBar) {
    closeBtn.addEventListener("click", () => {
      announcementBar.classList.add("closed");
      document.body.classList.add("no-announcement");
    });
  }

  if (!header) return;

  window.addEventListener("scroll", () => {
    header.classList.toggle("scrolled", window.scrollY > 40);
  });
}

// =====================================================================
// MENU CARD RENDERING
// =====================================================================

function createMenuItemHTML(item) {
  const isOutOfStock = !item.stock || item.stock <= 0;
  const isBestSeller = item.rating >= 4.9 || item.id === 9 || item.id === 25;
  const categoryLower = (item.category || "").toLowerCase();
  // Only show flame badge if the CATEGORY is not already "pedas" (prevents double badge)
  const isPedasByName =
    item.name.toLowerCase().includes("pedas") ||
    item.name.toLowerCase().includes("mercon");
  const showPedasBadge = isPedasByName && categoryLower !== "pedas";

  let badgeHTML = "";
  if (item.category) {
    const catDisplay = item.category.charAt(0).toUpperCase() + item.category.slice(1);
    badgeHTML += `<span class="badge-tag category">${catDisplay}</span>`;
  }
  if (showPedasBadge) {
    badgeHTML += `<span class="badge-tag pedas"><ion-icon name="flame"></ion-icon> Pedas</span>`;
  }
  if (isBestSeller) {
    badgeHTML += `<span class="badge-tag bestseller">⭐ Favorit</span>`;
  }

  let stockClass = "stock-pill tersedia";
  let stockText = `Stok: ${item.stock}`;
  if (item.stock <= 0) {
    stockClass = "stock-pill habis";
    stockText = "Habis";
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
    <li class="menu-item-wrapper" data-category="${categoryLower}" data-id="${item.id}">
      <article class="food-menu-card ${isOutOfStock ? "out-of-stock" : ""}">
        <div class="card-media-wrapper" onclick="openQuickViewModal(${item.id})">
          <img src="${item.image}" alt="${item.name}" class="card-food-img" loading="lazy" onerror="this.src='assets/images/food-menu-1.png'">
          <div class="card-img-gradient"></div>
          <div class="card-badges-top">${badgeHTML}</div>
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
          <p class="card-food-desc">${item.description || "Camilan cireng montok istimewa resep autentik Cimohot."}</p>
          <div class="card-footer-row">
            <div class="price-box">
              <span class="price-val">${item.price_formatted}</span>
            </div>
            ${buttonHTML}
          </div>
        </div>
      </article>
    </li>
  `;
}

// =====================================================================
// MENU FILTER & RENDER
// =====================================================================


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
      <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: var(--r-lg); border: 1px dashed var(--border-dark);">
        <ion-icon name="search-outline" style="font-size: 3rem; color: var(--text-dim); margin-bottom: 12px;"></ion-icon>
        <h4 style="font-size: 1.1rem; font-weight: 800; color: var(--text-white); margin-bottom: 6px;">Menu Tidak Ditemukan</h4>
        <p style="color: var(--text-muted); font-size: 0.92rem;">Tidak ada menu yang cocok dengan "<strong>${currentSearchQuery}</strong>".</p>
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

// =====================================================================
// LOAD MENU FROM API
// =====================================================================

async function loadAndRenderMenu() {
  const menuListContainer = document.querySelector(".food-menu-list");
  if (!menuListContainer) return;

  // Skeleton loading
  menuListContainer.innerHTML = Array(6)
    .fill(
      `<li class="menu-item-wrapper">
        <div style="background: var(--bg-card); border-radius: var(--r-lg); border: 1px solid var(--border-dark); overflow:hidden;">
          <div class="skeleton" style="height: 220px;"></div>
          <div style="padding: 18px;">
            <div class="skeleton" style="height: 14px; width: 60%; margin-bottom: 10px; border-radius: 6px;"></div>
            <div class="skeleton" style="height: 18px; width: 80%; margin-bottom: 8px; border-radius: 6px;"></div>
            <div class="skeleton" style="height: 12px; width: 100%; margin-bottom: 6px; border-radius: 6px;"></div>
            <div class="skeleton" style="height: 12px; width: 70%; margin-bottom: 16px; border-radius: 6px;"></div>
            <div style="display:flex; justify-content:space-between; align-items:center; padding-top:14px; border-top: 1px solid var(--border-dark);">
              <div class="skeleton" style="height: 20px; width: 80px; border-radius: 6px;"></div>
              <div class="skeleton" style="height: 36px; width: 90px; border-radius: 999px;"></div>
            </div>
          </div>
        </div>
      </li>`
    )
    .join("");

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
      <div style="grid-column: 1 / -1; text-align: center; padding: 50px 20px;">
        <p style="color: var(--red); font-weight: 700; margin-bottom: 12px;">Gagal memuat menu: ${error.message}</p>
        <button class="btn btn-primary" onclick="loadAndRenderMenu()">Coba Lagi</button>
      </div>
    `;
  }
}

// =====================================================================
// QUICK VIEW MODAL
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
  document.getElementById("qvDesc").textContent =
    item.description || "Camilan khas bertekstur krispi di luar dan kenyal montok di dalam, diracik dengan bumbu rempah autentik.";
  document.getElementById("qvQtyDisplay").textContent = quickViewQuantity;

  const qvAddBtn = document.getElementById("qvAddToCartBtn");
  if (item.stock <= 0) {
    qvAddBtn.disabled = true;
    qvAddBtn.innerHTML = "Stok Habis";
    qvAddBtn.classList.add("btn-disabled");
  } else if (window.currentUser && window.currentUser.role === "admin") {
    qvAddBtn.disabled = true;
    qvAddBtn.innerHTML = `<ion-icon name="shield-checkmark"></ion-icon> Mode Admin (Hanya Kelola)`;
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
// SHOPPING CART & CART DRAWER
// =====================================================================

class ShoppingCartManager {
  async addItem(menuId, quantity = 1, buttonElement = null) {
    if (window.currentUser && window.currentUser.role === "admin") {
      Swal.fire({
        icon: "info",
        title: "Mode Administrator",
        text: "Akun Administrator bertugas memantau operasional restoran. Silakan beralih ke akun pelanggan untuk membuat pesanan belanja.",
        confirmButtonColor: "#FF3D2E",
        confirmButtonText: "Mengerti",
      });
      return;
    }
    if (!currentUser) {
      const selectedItem = allMenuItems.find((m) => m.id === parseInt(menuId));
      const itemName = selectedItem ? selectedItem.name : "Menu Pilihan";

      Swal.fire({
        title: "Mulai Pemesanan",
        html: `
          <div class="swal-auth-prompt">
            <div class="swal-auth-badge"><ion-icon name="bag-handle"></ion-icon></div>
            <span class="swal-auth-item-tag">${itemName}</span>
            <p>Masuk ke akun Anda untuk menyimpan riwayat pesanan & promo, atau pesan kilat langsung via WhatsApp.</p>
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: "Masuk ke Akun",
        cancelButtonText: "Pesan via WhatsApp",
        confirmButtonColor: "#FF3D2E",
        cancelButtonColor: "#22C55E",
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = "/login?redirect=" + encodeURIComponent(window.location.pathname + window.location.hash);
        } else if (result.dismiss === Swal.DismissReason.cancel) {
          const waText = encodeURIComponent(
            `Halo Cimohot! Saya ingin memesan ${quantity}x ${itemName}. Mohon info total pesanan ya!`
          );
          window.open(`https://wa.me/6285930194086?text=${waText}`, "_blank");
        }
      });
      return;
    }

    if (buttonElement) {
      buttonElement.innerHTML = `<ion-icon name="sync-outline"></ion-icon> Menambahkan...`;
      buttonElement.disabled = true;
    }

    try {
      const params = new URLSearchParams({
        action: "add_to_cart",
        menu_id: menuId,
        quantity: quantity,
      });

      const response = await fetch("/api.php", { method: "POST", body: params });
      const result = await response.json();

      if (result.success) {
        const count = result.data?.cart_count ?? result.data?.new_cart_count ?? 0;
        const subtotalFormatted = result.data?.cart_subtotal_formatted || "Rp 0";
        updateCartBadge(count);
        updateFloatingCartBar(count, subtotalFormatted);
        this.animateCartButton();

        const matchedItem = allMenuItems.find((m) => m.id === parseInt(menuId));
        const itemName = matchedItem ? matchedItem.name : "Menu pilihan";

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
            // Klik pada toast untuk langsung menutup
            toast.addEventListener("click", () => {
              Swal.close();
            });
            // Fallback pengaman agar notifikasi dipastikan hilang
            setTimeout(() => {
              if (toast && document.body.contains(toast)) {
                Swal.close();
              }
            }, 2400);
          },
        });
        Toast.fire({
          icon: "success",
          title: "Ditambahkan ke Keranjang",
          html: `<strong>${itemName}</strong> berhasil masuk ke pesanan`,
        });

        // NOTE: Browsing is not interrupted; drawer does not open automatically!
      } else {
        throw new Error(result.message || "Gagal menambahkan item");
      }
    } catch (error) {
      if (error.message && (error.message.includes("login") || error.message.includes("Akses ditolak") || error.message.includes("401"))) {
        checkLoginStatus().then((user) => {
          if (!user) {
            Swal.fire({
              title: "Sesi Berakhir",
              html: `
                <div class="swal-auth-prompt">
                  <div class="swal-auth-badge" style="color:#F59E0B;border-color:rgba(245,158,11,0.3);background:rgba(245,158,11,0.12);"><ion-icon name="log-in-outline"></ion-icon></div>
                  <p>Sesi login Anda tidak terdeteksi atau telah berakhir. Silakan masuk kembali ke akun Anda.</p>
                </div>
              `,
              showCancelButton: true,
              confirmButtonText: "Masuk Sekarang",
              cancelButtonText: "Batal",
              confirmButtonColor: "#FF3D2E",
              cancelButtonColor: "#6B7280",
            }).then((res) => {
              if (res.isConfirmed) {
                window.location.href = "/login?redirect=" + encodeURIComponent(window.location.pathname);
              }
            });
          }
        });
      } else {
        Swal.fire({
          title: "Pemberitahuan",
          text: error.message,
          icon: "error",
          confirmButtonColor: "#FF3D2E",
          confirmButtonText: "Tutup",
        });
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
    const cartBtns = document.querySelectorAll(".cart-btn");
    cartBtns.forEach((btn) => {
      btn.style.transform = "scale(1.25)";
      setTimeout(() => (btn.style.transform = ""), 250);
    });
  }
}

const shoppingCart = new ShoppingCartManager();

// =====================================================================
// FLOATING BOTTOM CART BAR & INTERACTIVE CART DRAWER
// =====================================================================

function updateFloatingCartBar(count, subtotalFormatted) {
  const bar = document.getElementById("floatingCartBar");
  if (!bar) return;

  if (currentUser && currentUser.role === "admin") {
    bar.classList.remove("visible");
    bar.style.display = "none";
    return;
  }

  const countNum = parseInt(count) || 0;
  if (countNum > 0) {
    const countEl = document.getElementById("floatingCartCount");
    const totalEl = document.getElementById("floatingCartTotal");
    if (countEl) countEl.textContent = countNum;
    if (totalEl) totalEl.textContent = subtotalFormatted || "Rp 0";
    bar.style.display = "block";
    document.body.classList.add("has-floating-cart");
    requestAnimationFrame(() => {
      bar.classList.add("visible");
    });
  } else {
    bar.classList.remove("visible");
    document.body.classList.remove("has-floating-cart");
    setTimeout(() => {
      if (!bar.classList.contains("visible")) {
        bar.style.display = "none";
      }
    }, 400);
  }
}

function openCartDrawer() {
  const overlay = document.getElementById("cartDrawerOverlay");
  if (!overlay) return;
  overlay.classList.add("open");
  document.body.style.overflow = "hidden";
  loadCartDrawerContents();
}

function closeCartDrawer() {
  const overlay = document.getElementById("cartDrawerOverlay");
  if (overlay) {
    overlay.classList.remove("open");
    document.body.style.overflow = "";
  }
}

async function loadCartDrawerContents() {
  const listEl = document.getElementById("cartDrawerItemsList");
  const countBadge = document.getElementById("drawerCartCountBadge");
  const subtotalVal = document.getElementById("drawerSubtotalVal");
  const drawerFooter = document.getElementById("cartDrawerFooter");
  if (!listEl) return;

  if (!currentUser) {
    listEl.innerHTML = `
      <div class="cart-drawer-empty">
        <ion-icon name="person-outline"></ion-icon>
        <h4>Masuk Terlebih Dahulu</h4>
        <p>Silakan login untuk menyimpan dan mengelola pesanan cireng Anda.</p>
        <a href="/login?redirect=${encodeURIComponent(window.location.pathname)}" class="btn btn-primary" style="margin-top: 10px;"><ion-icon name="log-in-outline"></ion-icon> Masuk Sekarang</a>
      </div>
    `;
    if (drawerFooter) drawerFooter.style.display = "none";
    if (countBadge) countBadge.textContent = "0 item";
    return;
  }

  // Loading skeleton state
  listEl.innerHTML = `
    <div style="display:flex; flex-direction:column; gap:12px; padding:10px 0;">
      <div class="skeleton" style="height: 72px; border-radius: 12px;"></div>
      <div class="skeleton" style="height: 72px; border-radius: 12px;"></div>
    </div>
  `;

  try {
    const res = await fetch("/api.php?action=get_cart");
    const json = await res.json();
    if (!json.success || !json.data) {
      throw new Error(json.message || "Gagal memuat keranjang.");
    }

    const { items, count, subtotal_formatted } = json.data;
    updateCartBadge(count);
    updateFloatingCartBar(count, subtotal_formatted);

    if (countBadge) {
      countBadge.textContent = `${count} item`;
    }
    if (subtotalVal) {
      subtotalVal.textContent = subtotal_formatted || "Rp 0";
    }

    if (!items || items.length === 0) {
      listEl.innerHTML = `
        <div class="cart-drawer-empty">
          <ion-icon name="bag-handle-outline"></ion-icon>
          <h4>Keranjang Anda Kosong</h4>
          <p>Belum ada cireng montok di keranjang Anda. Yuk pilih menu favorit sekarang!</p>
          <button type="button" class="btn btn-primary" onclick="closeCartDrawer(); window.location.hash = 'food-menu';">
            <ion-icon name="restaurant-outline"></ion-icon> Pilih Menu Sekarang
          </button>
        </div>
      `;
      if (drawerFooter) drawerFooter.style.display = "none";
      return;
    }

    if (drawerFooter) drawerFooter.style.display = "flex";

    listEl.innerHTML = items.map((item) => `
      <div class="drawer-item-card" data-menu-id="${item.menu_id}">
        <img src="${item.image || 'assets/images/menu/cireng-ayam-pedas.jpg'}" alt="${item.name}" class="drawer-item-thumb" onerror="this.src='assets/images/menu/cireng-ayam-pedas.jpg'">
        <div class="drawer-item-info">
          <h4 class="drawer-item-name" title="${item.name}">${item.name}</h4>
          <div class="drawer-item-meta">
            <span class="drawer-item-category">${item.category || 'Menu'}</span>
            <span class="drawer-item-price">${item.price_formatted}</span>
          </div>
          <div class="drawer-item-stepper-wrap">
            <div class="drawer-qty-stepper">
              <button type="button" class="drawer-qty-btn" onclick="updateCartDrawerItemQty(${item.menu_id}, ${item.quantity - 1})" title="Kurangi">−</button>
              <span class="drawer-qty-val">${item.quantity}</span>
              <button type="button" class="drawer-qty-btn" onclick="updateCartDrawerItemQty(${item.menu_id}, ${item.quantity + 1})" title="Tambah" ${item.quantity >= item.stock ? "disabled" : ""}>+</button>
            </div>
            <span class="drawer-item-subtotal">${item.subtotal_formatted}</span>
          </div>
        </div>
        <button type="button" class="drawer-item-remove-btn" onclick="removeCartDrawerItem(${item.menu_id})" title="Hapus dari keranjang" aria-label="Hapus item">
          <ion-icon name="trash-outline"></ion-icon>
        </button>
      </div>
    `).join("");

  } catch (err) {
    console.error("Cart drawer error:", err);
    listEl.innerHTML = `
      <div class="cart-drawer-empty">
        <ion-icon name="alert-circle-outline" style="color: var(--red);"></ion-icon>
        <h4>Terjadi Kendala</h4>
        <p style="color: var(--text-muted); font-size: 0.88rem;">${err.message}</p>
        <button class="btn btn-secondary" onclick="loadCartDrawerContents()">Coba Lagi</button>
      </div>
    `;
  }
}

async function updateCartDrawerItemQty(menuId, newQty) {
  if (newQty <= 0) {
    return removeCartDrawerItem(menuId);
  }

  try {
    const params = new URLSearchParams({
      action: "update_cart",
      menu_id: menuId,
      quantity: newQty,
    });
    const res = await fetch("/api.php", { method: "POST", body: params });
    const json = await res.json();
    if (!json.success) {
      throw new Error(json.message || "Gagal memperbarui kuantitas.");
    }
    loadCartDrawerContents();
  } catch (e) {
    Swal.fire({
      icon: "warning",
      title: "Pemberitahuan",
      text: e.message,
      background: "#1F1F1F",
      color: "#FAFAFA",
      confirmButtonColor: "#FF3D2E",
    });
  }
}

async function removeCartDrawerItem(menuId) {
  try {
    const params = new URLSearchParams({
      action: "remove_from_cart",
      menu_id: menuId,
    });
    const res = await fetch("/api.php", { method: "POST", body: params });
    const json = await res.json();
    if (!json.success) {
      throw new Error(json.message || "Gagal menghapus item.");
    }
    loadCartDrawerContents();
  } catch (e) {
    Swal.fire({
      icon: "error",
      title: "Gagal Menghapus",
      text: e.message,
      background: "#1F1F1F",
      color: "#FAFAFA",
      confirmButtonColor: "#FF3D2E",
    });
  }
}

// =====================================================================
// SPICE LEVEL SIMULATOR (PANDUAN RASA INTERAKTIF)
// =====================================================================

const SPICE_LEVEL_DATA = [
  {
    level: 0,
    emoji: "😊",
    title: "Level 0 - Manja & Gurih",
    tag: "Aman Buat Anak",
    quote: '"Gurih manis renyah tanpa pedas sama sekali, nagih abis!"',
    desc: "Cocok untuk yang tidak tahan pedas, anak-anak, atau yang ingin menikmati keaslian rasa daging ayam empuk dan keju gurih.",
    recommendedItemName: "Cireng Isi Ayam Original",
    filterCategory: "original",
    themeColor: "#10B981",
  },
  {
    level: 1,
    emoji: "😌",
    title: "Level 1 - Anget-anget Kuku",
    tag: "Pemula Pedas",
    quote: '"Ada hangat-hangatnya di lidah, bikin mulut gak bisa diem."',
    desc: "Sentuhan lada putih dan cabai merah tipis. Pas untuk rasa pedas santai tanpa keringat dingin.",
    recommendedItemName: "Cireng Isi Keju Mozzarella",
    filterCategory: "original",
    themeColor: "#F59E0B",
  },
  {
    level: 2,
    emoji: "😅",
    title: "Level 2 - Standard Kick",
    tag: "Paling Pas & Populer",
    quote: '"Pedasnya pas banget! Bikin mata melek tapi tetap nikmat."',
    desc: "Pedas bumbu balado dengan aroma daun jeruk segar. Level favorit para pencinta jajanan gurih.",
    recommendedItemName: "Cireng Isi Bakso Pedas Daun Jeruk",
    filterCategory: "pedas",
    themeColor: "#FB923C",
  },
  {
    level: 3,
    emoji: "🥵",
    title: "Level 3 - Pedas Nampol!",
    tag: "Spicy Lovers",
    quote: '"Nangis bahagia! Bibir merah merona, keringat bercucuran."',
    desc: "Cabai rawit merah melimpah yang dioseng matang. Sangat cocok dinikmati bareng es teh manis dingin.",
    recommendedItemName: "Cireng Isi Ayam Pedas Mercon",
    filterCategory: "pedas",
    themeColor: "#FF3D2E",
  },
  {
    level: 4,
    emoji: "😈",
    title: "Level 4 - Brave Souls",
    tag: "Tantangan Serius",
    quote: '"Beneran gila pedasnya, lidah bergetar tapi tangan tetep nyomot!"',
    desc: "Kombinasi cabai rawit setan giling kasar. Hanya untuk pendekar cabai sejati yang berani uji nyali!",
    recommendedItemName: "Cireng Mercon Sapi Gurih",
    filterCategory: "pedas",
    themeColor: "#DC2626",
  },
  {
    level: 5,
    emoji: "💀",
    title: "Level 5 - Uji Nyali Akhirat",
    tag: "EXTREME CHALLENGE",
    quote: '"BRUTAL! Air mata keluar, nafas ngos-ngosan, tapi PUAS BANGET!"',
    desc: "Cabai rawit murni level ekstrem! Siapkan minuman dingin segar sebelum mulai bertarung.",
    recommendedItemName: "Paket Combo Cimohot Kenyang",
    filterCategory: "paket hemat",
    themeColor: "#9333EA",
  },
];

function setSpiceSimulatorLevel(levelIndex) {
  const data = SPICE_LEVEL_DATA[levelIndex];
  if (!data) return;

  const buttons = document.querySelectorAll(".spice-step-btn");
  buttons.forEach((btn, idx) => {
    btn.classList.toggle("active", idx === levelIndex);
    if (idx === levelIndex) {
      btn.style.setProperty("--active-color", data.themeColor);
    }
  });

  const emoji = document.getElementById("simEmoji");
  const levelBadge = document.getElementById("simLevelBadge");
  const quote = document.getElementById("simQuote");
  const desc = document.getElementById("simDesc");
  const recTitle = document.getElementById("simRecTitle");
  const recPrice = document.getElementById("simRecPrice");
  const recImg = document.getElementById("simRecImg");
  const recBtn = document.getElementById("simRecOrderBtn");
  const filterBtn = document.getElementById("simRecFilterBtn");
  const recCard = document.getElementById("simRecCard");
  const box = document.querySelector(".simulator-box");

  if (emoji) {
    emoji.textContent = data.emoji;
    emoji.style.transform = "scale(1.3)";
    setTimeout(() => (emoji.style.transform = "scale(1)"), 300);
  }

  if (levelBadge) {
    levelBadge.textContent = `${data.title} • ${data.tag}`;
    levelBadge.style.background = `${data.themeColor}1f`;
    levelBadge.style.borderColor = `${data.themeColor}4d`;
    levelBadge.style.color = data.themeColor;
  }

  if (quote) quote.textContent = data.quote;
  if (desc) desc.textContent = data.desc;

  if (box) {
    box.style.borderColor = `${data.themeColor}44`;
    box.style.boxShadow = `0 14px 40px rgba(0,0,0,0.5), 0 0 28px ${data.themeColor}22`;
  }

  // Screen shake for level 5
  if (levelIndex === 5 && box) {
    box.style.animation = "shakeX 0.5s ease";
    setTimeout(() => (box.style.animation = ""), 500);
  }

  // Update recommendation
  const matchingItem =
    allMenuItems.find((m) =>
      m.name.toLowerCase().includes(data.recommendedItemName.toLowerCase())
    ) || allMenuItems[0];

  if (matchingItem) {
    if (recTitle) recTitle.textContent = matchingItem.name;
    if (recPrice) recPrice.textContent = matchingItem.price_formatted;
    if (recImg) recImg.src = matchingItem.image;
    if (recBtn) {
      recBtn.innerHTML = `<ion-icon name="cart-outline"></ion-icon> Pesan Menu Ini`;
      recBtn.onclick = () => shoppingCart.addItem(matchingItem.id, 1, recBtn);
    }

    if (filterBtn) {
      filterBtn.innerHTML = `<ion-icon name="restaurant-outline"></ion-icon> Lihat Menu ${data.tag}`;
      filterBtn.onclick = () => applyFilterFromSimulator(data.filterCategory, data.title);
    }

    // Animate recommendation card
    if (recCard) {
      recCard.style.animation = "none";
      recCard.offsetHeight; // trigger reflow
      recCard.style.animation = "slideInRight 0.4s ease-out";
    }
  }
}

function applyFilterFromSimulator(filterCat, levelTitle) {
  const targetSection = document.getElementById("food-menu");
  if (targetSection) {
    targetSection.scrollIntoView({ behavior: "smooth" });
  }

  const filterBtn = document.querySelector(`.filter-btn[data-filter="${filterCat}"]`);
  if (filterBtn) {
    document.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("active"));
    filterBtn.classList.add("active");
    currentCategoryFilter = filterCat.toLowerCase();
    filterAndRenderMenu();
  }

  const Toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    showCloseButton: true,
    timer: 2000,
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
      }, 2200);
    },
  });
  Toast.fire({
    icon: "info",
    title: `Katalog disaring: ${levelTitle}`,
  });
}

function initSpiceSimulatorMenu() {
  setSpiceSimulatorLevel(3);
}

// =====================================================================
// USER AUTH & UI
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
      const count = currentUser.role === "admin" ? 0 : (json.data.cart_count || 0);
      const subtotalFormatted = json.data.cart_subtotal_formatted || "Rp 0";
      updateCartBadge(count);
      updateFloatingCartBar(count, subtotalFormatted);
    } else {
      currentUser = null;
      window.currentUser = null;
      updateCartBadge(0);
      updateFloatingCartBar(0, "Rp 0");
    }
  } catch (e) {
    currentUser = null;
    updateFloatingCartBar(0, "Rp 0");
  }
  updateLoginStatusUI();
  if (window.currentUser && window.currentUser.role === "admin") {
    filterAndRenderMenu();
  }
  return currentUser;
}

function handleLogout() {
  Swal.fire({
    title: "Ingin keluar akun?",
    text: "Pesanan di keranjang Anda tetap tersimpan.",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#FF3D2E",
    cancelButtonColor: "#6B7280",
    confirmButtonText: "Ya, Keluar",
    cancelButtonText: "Batal",
    background: "#1F1F1F",
    color: "#FAFAFA",
  }).then((res) => {
    if (res.isConfirmed) {
      window.location.href = "/logout";
    }
  });
}

// =====================================================================
// EVENT LISTENERS
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
  // Mobile menu toggle
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

  // Cart button
  const cartBtn = document.getElementById("cartBtn");
  if (cartBtn) {
    cartBtn.onclick = (e) => {
      e.preventDefault();
      if (window.currentUser && window.currentUser.role === "admin") {
        Swal.fire({
          icon: "info",
          title: "Keranjang Khusus Pelanggan",
          text: "Akun Admin bertugas mengelola restoran dan tidak dapat memesan makanan. Buka Manajemen Pesanan Pelanggan di Dashboard Admin?",
          showCancelButton: true,
          confirmButtonText: "Buka Manajemen Pesanan",
          cancelButtonText: "Tutup",
          confirmButtonColor: "#FF3D2E",
          cancelButtonColor: "#6B7280",
          background: "#1F1F1F",
          color: "#FAFAFA"
        }).then((res) => {
          if (res.isConfirmed) {
            window.location.href = "/admin/orders";
          }
        });
        return;
      }
      openCartDrawer();
    };
  }

  // Dock cart
  const dockCart = document.getElementById("dockCartLink");
  if (dockCart) {
    dockCart.onclick = (e) => {
      e.preventDefault();
      openCartDrawer();
    };
  }

  // Quick view quantity
  document.getElementById("qvQtyMinus")?.addEventListener("click", () => changeQuickViewQuantity(-1));
  document.getElementById("qvQtyPlus")?.addEventListener("click", () => changeQuickViewQuantity(1));
  document.getElementById("qvAddToCartBtn")?.addEventListener("click", () => {
    if (quickViewItem) {
      shoppingCart.addItem(quickViewItem.id, quickViewQuantity);
      closeQuickViewModal();
    }
  });

  // Filter buttons
  document.querySelectorAll(".filter-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      document.querySelectorAll(".filter-btn").forEach((b) => b.classList.remove("active"));
      btn.classList.add("active");
      currentCategoryFilter = btn.dataset.filter.toLowerCase();
      filterAndRenderMenu();
    });
  });

  // Search
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
    clearSearchBtn.addEventListener("click", () => resetSearchAndFilter());
  }

  // Spice simulator tabs
  document.querySelectorAll(".spice-step-btn").forEach((btn) => {
    btn.addEventListener("click", () => {
      const lvl = parseInt(btn.dataset.level);
      setSpiceSimulatorLevel(lvl);
    });
  });

  // Back to top
  const backTopBtn = document.querySelector("[data-back-top-btn]");
  window.addEventListener("scroll", () => {
    if (backTopBtn) {
      backTopBtn.classList.toggle("active", window.scrollY > 400);
    }
  });
  backTopBtn?.addEventListener("click", (e) => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: "smooth" });
  });

  // Cart drawer close on overlay click or close button
  const cartOverlay = document.getElementById("cartDrawerOverlay");
  if (cartOverlay) {
    cartOverlay.addEventListener("click", (e) => {
      if (e.target === cartOverlay) closeCartDrawer();
    });
  }
  document.getElementById("closeCartDrawerBtn")?.addEventListener("click", () => {
    closeCartDrawer();
  });
  document.getElementById("dockCartLink")?.addEventListener("click", (e) => {
    e.preventDefault();
    openCartDrawer();
  });
  window.addEventListener("keydown", (e) => {
    if (e.key === "Escape") {
      closeCartDrawer();
      closeQuickViewModal();
    }
  });
}

// =====================================================================
// MODAL HELPERS
// =====================================================================

function openModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.add("show");
}

function closeModal(modalId) {
  const modal = document.getElementById(modalId);
  if (modal) modal.classList.remove("show");
}

window.addEventListener("click", (e) => {
  if (e.target.classList.contains("modal")) {
    e.target.classList.remove("show");
  }
});

// =====================================================================
// LIVE ORDER TICKER
// =====================================================================

const STATIC_TICKER_MESSAGES = [
  "<strong>Rina</strong> (Koja) baru memesan <span class='item-highlight'>Cireng Keju Mozza x3</span>",
  "<strong>Budi</strong> (Tanjung Priok) baru memesan <span class='item-highlight'>Paket Combo Cimohot</span>",
  "<strong>Siti</strong> (Kelapa Gading) baru memesan <span class='item-highlight'>Cireng Ayam Pedas Mercon x5</span>",
  "<strong>Hendra</strong> (Penjaringan) baru memesan <span class='item-highlight'>Cireng Bakso Pedas x4</span>",
  "<strong>Dewi</strong> (Pademangan) baru memesan <span class='item-highlight'>Es Teh Manis + Cireng Original x6</span>",
  "<strong>Andi</strong> (Ancol) baru memesan <span class='item-highlight'>Paket Keluarga Cimohot</span>",
  "<strong>Fitri</strong> (Pluit) baru memesan <span class='item-highlight'>Cireng Tuna Kemangi x3</span>",
  "<strong>Rudi</strong> (Semper) baru memesan <span class='item-highlight'>Cireng Mercon Sapi x5</span>",
];

async function initLiveTicker() {
  const tickerText = document.getElementById("liveTickerText");
  if (!tickerText) return;

  let messages = [...STATIC_TICKER_MESSAGES];

  // Try to get real recent orders from API
  try {
    const res = await fetch("/api.php?action=get_recent_orders&limit=5");
    const json = await res.json();
    if (json.success && json.data && json.data.orders && json.data.orders.length > 0) {
      const realMessages = json.data.orders.map(order => {
        const firstName = (order.customer_name || "Pelanggan").split(" ")[0];
        return `<strong>${firstName}</strong> baru memesan <span class='item-highlight'>${order.item_summary || "menu favorit Cimohot"}</span>`;
      });
      messages = [...realMessages, ...STATIC_TICKER_MESSAGES];
    }
  } catch (e) {
    // Use static messages silently
  }

  let idx = 0;
  function showNext() {
    tickerText.style.opacity = "0";
    tickerText.style.transform = "translateY(5px)";
    setTimeout(() => {
      tickerText.innerHTML = messages[idx % messages.length];
      tickerText.style.opacity = "1";
      tickerText.style.transform = "translateY(0)";
      idx++;
    }, 350);
  }

  showNext();
  setInterval(showNext, 5000);
}

// =====================================================================
// MOBILE DOCK ACTIVE SECTION TRACKING
// =====================================================================

function initDockActiveSectionTracking() {
  const sections = [
    { id: "hero", dockTarget: "dockHomeLink" },
    { id: "food-menu", dockTarget: "dockMenuLink" },
    { id: "about", dockTarget: "dockAboutLink" },
  ];

  const dockItems = document.querySelectorAll(".dock-item");

  const obs = new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const match = sections.find((s) => s.id === entry.target.id);
        if (match) {
          dockItems.forEach((d) => d.classList.remove("active"));
          const activeLink = document.getElementById(match.dockTarget);
          if (activeLink) activeLink.classList.add("active");
        }
      }
    });
  }, { threshold: 0.4 });

  sections.forEach((s) => {
    const el = document.getElementById(s.id);
    if (el) obs.observe(el);
  });
}

// =====================================================================
// INITIALIZATION
// =====================================================================

document.addEventListener("DOMContentLoaded", async () => {
  // Init UI interactions
  initNavbarScroll();
  initScrollProgress();
  initScrollReveal();
  animateCounters();
  initializeAllEventListeners();
  initDockActiveSectionTracking();

  // Load data
  await checkLoginStatus();
  await loadAndRenderMenu();

  // Init spice simulator after menu loads
  initSpiceSimulatorMenu();

  // Init live ticker
  initLiveTicker();

  console.log("Cimohot v2 : Street Kitchen Premium Engine Online");
});
