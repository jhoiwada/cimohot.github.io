"use strict";
let cartState = {
    items: CART_ITEMS.map(i => ({...i})),
    deliveryType: "delivery",
    paymentMethod: "cod",
    notes: "",
    promoCode: "",
    promoData: null,
    discount: 0,
};
function fmt(n) { return "Rp " + parseInt(n).toLocaleString("id-ID"); }
function catBadge(cat) {
    const map = { original:"badge-original", pedas:"badge-pedas", "paket hemat":"badge-paket", minuman:"badge-minuman" };
    const cls = map[cat.toLowerCase()] || "badge-original";
    return `<span class="item-cat-badge ${cls}">${cat}</span>`;
}
function calcSubtotal() { return cartState.items.reduce((s,i)=>s+i.price_raw*i.quantity,0); }
function calcTotal() { return Math.max(0, calcSubtotal() - cartState.discount); }
function updateSummary() {
    const el = id => document.getElementById(id);
    if(el("sumSubtotal")) el("sumSubtotal").textContent = fmt(calcSubtotal());
    if(el("sumTotal")) el("sumTotal").textContent = fmt(calcTotal());
    if(el("sumDiscount")) el("sumDiscount").textContent = "-" + fmt(cartState.discount);
    const dr = el("discountRow");
    if(dr) dr.style.display = cartState.discount > 0 ? "flex" : "none";
}
function render() {
    const app = document.getElementById("cart-app");
    if(!cartState.items || cartState.items.length === 0) { app.innerHTML = renderEmpty(); return; }
    app.innerHTML = renderCart();
    attachListeners();
    updateSummary();
}
function renderEmpty() {
    return `<div class="card"><div class="empty-state"><span class="empty-icon-wrap" style="font-size:3.5rem;color:var(--dim);margin-bottom:16px;display:inline-block;"><ion-icon name="cart-outline"></ion-icon></span><h2 class="empty-title">Keranjangmu masih kosong</h2><p class="empty-text">Belum ada menu di keranjang Anda. Yuk pilih cireng montok favoritmu dulu!</p><button class="btn-go-menu" onclick="location.href='/#food-menu'"><ion-icon name="restaurant-outline"></ion-icon> Lihat Menu</button></div></div>`;
}
function renderCart() {
    const hasProfile = USER_PROFILE && USER_PROFILE.phone && USER_PROFILE.address;
    const warnHTML = !hasProfile ? `<div class="profile-warn"><ion-icon name="warning-outline"></ion-icon><span>Lengkapi <a href="/akun?redirect=cart">profil kamu</a> (no. HP & alamat) untuk pengiriman.</span></div>` : "";
    const itemCount = cartState.items.reduce((s,i)=>s+i.quantity,0);
    return `<div class="cart-layout">
      <div>
        <div class="card" style="margin-bottom:16px">
          <div class="card-header"><ion-icon name="bag-handle-outline"></ion-icon>Pesananmu<span class="header-badge">${itemCount} item</span></div>
          <div id="items-list">${cartState.items.map(renderItem).join("")}</div>
          <div class="notes-section">
            <div class="notes-label"><ion-icon name="create-outline"></ion-icon>Catatan Pesanan <span style="font-weight:400;color:var(--dim)">(opsional)</span></div>
            <textarea id="notesInput" class="notes-input" placeholder="Misal: less spicy, no MSG, tambah saus..."></textarea>
          </div>
        </div>
      </div>
      <aside class="card summary-card">
        <div class="card-header"><ion-icon name="receipt-outline"></ion-icon>Ringkasan</div>
        <div class="summary-content">
          ${warnHTML}
          <div class="summary-row"><span>Subtotal (${itemCount} item)</span><span id="sumSubtotal">${fmt(calcSubtotal())}</span></div>
          <div class="summary-row" id="discountRow" style="display:none"><span>Diskon Promo</span><span id="sumDiscount" class="discount">-${fmt(0)}</span></div>
          <div class="summary-row"><span>Biaya Layanan</span><span style="color:#22C55E;font-weight:700;">Gratis</span></div>
          <div class="delivery-section">
            <div class="section-title">Metode Pengiriman</div>
            <div class="delivery-options">
              <label class="delivery-opt selected" data-type="delivery"><input type="radio" name="delivery_type" value="delivery" checked><div class="opt-radio"></div><span class="opt-icon"><ion-icon name="bicycle-outline"></ion-icon></span><div class="opt-info"><div class="opt-label">Antar ke Alamat</div><div class="opt-desc">Dikirim ke alamat profil kamu</div></div><span class="opt-badge badge-fast">POPULER</span></label>
              <label class="delivery-opt" data-type="pickup"><input type="radio" name="delivery_type" value="pickup"><div class="opt-radio"></div><span class="opt-icon"><ion-icon name="storefront-outline"></ion-icon></span><div class="opt-info"><div class="opt-label">Ambil Sendiri</div><div class="opt-desc">Jl. Muncang Raya No. 55, Koja</div></div><span class="opt-badge badge-free">GRATIS</span></label>
            </div>
          </div>
          <div class="payment-section">
            <div class="section-title">Metode Bayar</div>
            <div class="payment-options">
              <label class="pay-opt selected" data-pay="cod"><input type="radio" name="payment_method" value="cod" checked><span class="pay-icon"><ion-icon name="cash-outline"></ion-icon></span><div class="opt-info"><div class="pay-label">Bayar di Tempat (COD)</div><div class="pay-desc">Bayar tunai saat pesanan tiba</div></div><div class="pay-check"></div></label>
              <label class="pay-opt" data-pay="transfer"><input type="radio" name="payment_method" value="transfer"><span class="pay-icon"><ion-icon name="card-outline"></ion-icon></span><div class="opt-info"><div class="pay-label">Transfer Bank</div><div class="pay-desc">BCA / Mandiri - konfirmasi via WA</div></div><div class="pay-check"></div></label>
              <label class="pay-opt" data-pay="pickup" id="payPickupOpt" style="display:none"><input type="radio" name="payment_method" value="pickup"><span class="pay-icon"><ion-icon name="storefront-outline"></ion-icon></span><div class="opt-info"><div class="pay-label">Bayar di Kasir</div><div class="pay-desc">Bayar langsung saat ambil di toko</div></div><div class="pay-check"></div></label>
            </div>
          </div>
          <div class="promo-section">
            <div class="section-title">Kode Promo</div>
            <div class="promo-input-row">
              <input type="text" id="promoInput" class="promo-input${cartState.promoCode ? ' valid' : ''}" placeholder="Contoh: CIMOHOT10" maxlength="20" value="${cartState.promoCode ? cartState.promoCode : ''}">
              <button class="promo-btn${cartState.promoCode ? ' btn-remove-promo' : ''}" id="promoBtn" data-action="${cartState.promoCode ? 'remove' : 'apply'}">${cartState.promoCode ? 'Hapus' : 'Pakai'}</button>
            </div>
            <div class="promo-status${cartState.promoCode ? ' ok' : ''}" id="promoStatus">${cartState.promoCode && cartState.promoData ? '✓ ' + (cartState.promoData.description || 'Promo diterapkan!') + ' - hemat ' + fmt(cartState.discount) : ''}</div>
          </div>
          <hr class="summary-divider" style="margin-top:18px">
          <div class="summary-total"><span>Total Bayar</span><span class="total-amount" id="sumTotal">${fmt(calcTotal())}</span></div>
        </div>
        <div class="checkout-section">
          <button class="btn-checkout" id="checkoutBtn"><ion-icon name="card-outline"></ion-icon><span>Pesan Sekarang</span></button>
          <button class="btn-back-shop" onclick="location.href='/#food-menu'"><ion-icon name="arrow-back-outline"></ion-icon> Lanjut Pilih Menu</button>
        </div>
      </aside>
    </div>`;
}
function renderItem(item) {
    const img = "../" + item.image;
    const sub = fmt(item.price_raw * item.quantity);
    return `<div class="cart-item" data-id="${item.id}">
      <div class="item-img-wrap"><img src="${img}" alt="${item.name}" loading="lazy" onerror="this.src='../assets/images/placeholder.jpg'">${catBadge(item.category)}</div>
      <div class="item-info"><div class="item-name">${item.name}</div><div class="item-price">${item.price}</div><div class="item-subtotal">Subtotal: ${sub}</div></div>
      <div class="item-controls">
        <div class="qty-box">
          <button class="qty-btn" data-action="dec" data-id="${item.id}">-</button>
          <input type="text" class="qty-val" value="${item.quantity}" readonly>
          <button class="qty-btn" data-action="inc" data-id="${item.id}">+</button>
        </div>
        <button class="del-btn" data-del="${item.id}" title="Hapus"><ion-icon name="trash-outline"></ion-icon></button>
      </div>
    </div>`;
}
function attachListeners() {
    document.querySelectorAll(".qty-btn").forEach(btn => btn.addEventListener("click", () => changeQty(btn.dataset.id, btn.dataset.action === "inc" ? 1 : -1)));
    document.querySelectorAll(".del-btn").forEach(btn => btn.addEventListener("click", () => deleteItem(btn.dataset.del)));
    const notes = document.getElementById("notesInput");
    if(notes) notes.addEventListener("input", () => cartState.notes = notes.value);
    document.querySelectorAll(".delivery-opt").forEach(opt => {
        opt.addEventListener("click", () => {
            document.querySelectorAll(".delivery-opt").forEach(o => o.classList.remove("selected"));
            opt.classList.add("selected");
            cartState.deliveryType = opt.dataset.type;
            const pp = document.getElementById("payPickupOpt");
            if(opt.dataset.type === "pickup") { if(pp) pp.style.display = "flex"; selectPayment("pickup"); }
            else { if(pp) pp.style.display = "none"; if(cartState.paymentMethod === "pickup") selectPayment("cod"); }
        });
    });
    document.querySelectorAll(".pay-opt").forEach(opt => opt.addEventListener("click", () => selectPayment(opt.dataset.pay)));
    const pb = document.getElementById("promoBtn"), pi = document.getElementById("promoInput");
    if(pb && pi) {
        pb.addEventListener("click", applyPromo);
        pi.addEventListener("keydown", e => { if(e.key === "Enter") applyPromo(); });
        pi.addEventListener("input", () => {
            const val = pi.value.trim().toUpperCase();
            if(cartState.promoCode && val !== cartState.promoCode) {
                cartState.promoCode = "";
                cartState.promoData = null;
                cartState.discount = 0;
                pi.classList.remove("valid", "invalid");
                const statusEl = document.getElementById("promoStatus");
                if(statusEl) {
                    statusEl.textContent = val === "" ? "Kode promo dihapus." : "";
                    statusEl.className = "promo-status";
                }
                const btn = document.getElementById("promoBtn");
                if(btn) {
                    btn.textContent = "Pakai";
                    btn.dataset.action = "apply";
                    btn.classList.remove("btn-remove-promo");
                }
                updateSummary();
            } else if(!val) {
                pi.classList.remove("valid", "invalid");
                const statusEl = document.getElementById("promoStatus");
                if(statusEl) { statusEl.textContent = ""; statusEl.className = "promo-status"; }
            }
        });
    }
    const cb = document.getElementById("checkoutBtn");
    if(cb) cb.addEventListener("click", proceedToCheckout);
}
function selectPayment(val) {
    cartState.paymentMethod = val;
    document.querySelectorAll(".pay-opt").forEach(o => { o.classList.toggle("selected", o.dataset.pay === val); const r = o.querySelector("input"); if(r) r.checked = o.dataset.pay === val; });
}
function removePromo() {
    cartState.promoCode = "";
    cartState.promoData = null;
    cartState.discount = 0;
    const inp = document.getElementById("promoInput");
    const statusEl = document.getElementById("promoStatus");
    const btn = document.getElementById("promoBtn");
    if(inp) {
        inp.value = "";
        inp.classList.remove("valid", "invalid");
    }
    if(btn) {
        btn.textContent = "Pakai";
        btn.dataset.action = "apply";
        btn.classList.remove("btn-remove-promo");
    }
    if(statusEl) {
        statusEl.textContent = "Kode promo dihapus.";
        statusEl.className = "promo-status";
        setTimeout(() => {
            if(statusEl && statusEl.textContent === "Kode promo dihapus.") {
                statusEl.textContent = "";
            }
        }, 2500);
    }
    updateSummary();
}
function recalculatePromo() {
    if(!cartState.promoData || !cartState.promoCode) {
        cartState.discount = 0;
        return;
    }
    const subtotal = calcSubtotal();
    const promo = cartState.promoData;
    if(promo.min_order && subtotal < promo.min_order) {
        cartState.promoCode = "";
        cartState.promoData = null;
        cartState.discount = 0;
        const inp = document.getElementById("promoInput");
        const statusEl = document.getElementById("promoStatus");
        const btn = document.getElementById("promoBtn");
        if(inp) { inp.classList.remove("valid"); inp.classList.add("invalid"); }
        if(btn) { btn.textContent = "Pakai"; btn.dataset.action = "apply"; btn.classList.remove("btn-remove-promo"); }
        if(statusEl) {
            statusEl.textContent = `✕ Minimum order ${fmt(promo.min_order)} tidak terpenuhi. Promo dibatalkan.`;
            statusEl.className = "promo-status err";
        }
        return;
    }
    let disc = promo.discount_type === "percentage" ? Math.round(subtotal * promo.discount_value / 100) : promo.discount_value;
    disc = Math.min(disc, subtotal);
    cartState.discount = disc;
    const statusEl = document.getElementById("promoStatus");
    if(statusEl && cartState.promoCode) {
        statusEl.textContent = "✓ " + (promo.description || "Promo diterapkan!") + " - hemat " + fmt(disc);
    }
}
async function changeQty(id, delta) {
    const item = cartState.items.find(i => i.id == id);
    if(!item) return;
    const nq = item.quantity + delta;
    if(nq < 1) { deleteItem(id); return; }
    const p = new URLSearchParams({ action:"update_cart", menu_id:id, quantity:nq });
    const r = await fetch("/api.php", { method:"POST", body:p });
    const d = await r.json();
    if(d.success) {
        item.quantity = nq;
        const row = document.querySelector(`.cart-item[data-id="${id}"]`);
        if(row) { row.querySelector(".qty-val").value = nq; row.querySelector(".item-subtotal").textContent = "Subtotal: " + fmt(item.price_raw * nq); }
        recalculatePromo();
        updateSummary();
    } else {
        if(d.message && (d.message.includes("login") || d.message.includes("Akses ditolak"))) {
            Swal.fire({
                icon: "warning",
                title: "Sesi Berakhir",
                text: "Sesi login Anda telah berakhir. Silakan masuk kembali.",
                confirmButtonText: "Masuk Sekarang",
                confirmButtonColor: "#FF3D2E",
                background: "#1A1A1F",
                color: "#FAFAFA"
            }).then(() => {
                location.href = "/login?redirect=cart";
            });
            return;
        }
        Swal.fire({ icon:"error", title:"Gagal", text:d.message, background:"#1A1A1F", color:"#FAFAFA", confirmButtonColor:"#FF3D2E" });
    }
}
async function deleteItem(id) {
    const c = await Swal.fire({ title:"Hapus dari keranjang?", icon:"question", showCancelButton:true, confirmButtonText:"Ya, hapus", cancelButtonText:"Batal", confirmButtonColor:"#FF3D2E", background:"#1A1A1F", color:"#FAFAFA" });
    if(!c.isConfirmed) return;
    const p = new URLSearchParams({ action:"remove_from_cart", menu_id:id });
    const r = await fetch("/api.php", { method:"POST", body:p });
    const d = await r.json();
    if(d.success) { cartState.items = cartState.items.filter(i => i.id != id); recalculatePromo(); render(); }
    else { Swal.fire({ icon:"error", title:"Error", text:d.message, background:"#1A1A1F", color:"#FAFAFA" }); }
}
async function applyPromo() {
    const btn = document.getElementById("promoBtn");
    const inp = document.getElementById("promoInput");
    const statusEl = document.getElementById("promoStatus");
    const code = inp ? inp.value.trim().toUpperCase() : "";

    if(cartState.promoCode && btn && btn.dataset.action === "remove") {
        removePromo();
        return;
    }

    if(!code) {
        removePromo();
        if(statusEl) {
            statusEl.textContent = "Masukkan kode promo dulu.";
            statusEl.className = "promo-status err";
        }
        return;
    }

    btn.disabled = true;
    btn.textContent = "...";

    try {
        const p = new URLSearchParams({ action:"validate_promo", code, total: calcSubtotal() });
        const r = await fetch("/api.php", { method:"POST", body:p });
        const d = await r.json();
        if(d.success && d.data) {
            const promo = d.data;
            let disc = promo.discount_type === "percentage" ? Math.round(calcSubtotal() * promo.discount_value / 100) : promo.discount_value;
            disc = Math.min(disc, calcSubtotal());
            cartState.promoCode = code; cartState.promoData = promo; cartState.discount = disc;
            if(inp) {
                inp.value = code;
                inp.classList.add("valid");
                inp.classList.remove("invalid");
            }
            if(btn) {
                btn.textContent = "Hapus";
                btn.dataset.action = "remove";
                btn.classList.add("btn-remove-promo");
            }
            if(statusEl) {
                statusEl.textContent = "✓ " + (promo.description || "Promo diterapkan!") + " - hemat " + fmt(disc);
                statusEl.className = "promo-status ok";
            }
            updateSummary();
        } else {
            removePromo();
            if(inp) {
                inp.classList.add("invalid");
                inp.classList.remove("valid");
            }
            if(statusEl) {
                statusEl.textContent = "✕ " + (d.message || "Kode promo tidak valid.");
                statusEl.className = "promo-status err";
            }
        }
    } catch(e) {
        if(statusEl) {
            statusEl.textContent = "✕ Gagal memvalidasi promo.";
            statusEl.className = "promo-status err";
        }
    } finally {
        btn.disabled = false;
        if(!cartState.promoCode && btn) {
            btn.textContent = "Pakai";
            btn.dataset.action = "apply";
            btn.classList.remove("btn-remove-promo");
        }
    }
}
async function proceedToCheckout() {
    if(cartState.deliveryType === "delivery" && (!USER_PROFILE || !USER_PROFILE.phone || !USER_PROFILE.address)) {
        const r = await Swal.fire({ icon:"warning", title:"Profil Belum Lengkap", html:"Lengkapi <strong>no. HP</strong> dan <strong>alamat</strong> di profil dulu ya!", confirmButtonText:"Lengkapi Sekarang", showCancelButton:true, cancelButtonText:"Nanti", confirmButtonColor:"#FF6B35", background:"#1A1A1F", color:"#FAFAFA" });
        if(r.isConfirmed) location.href = "/akun?redirect=cart";
        return;
    }
    const btn = document.getElementById("checkoutBtn");
    btn.disabled = true;
    btn.innerHTML = "<div class=\"spin\"></div><span>Memproses...</span>";
    document.getElementById("step1").classList.add("done");
    document.getElementById("step2").classList.add("active");
    try {
        const p = new URLSearchParams();
        p.append("action", "checkout");
        p.append("items", JSON.stringify(cartState.items));
        p.append("payment_method", cartState.paymentMethod);
        p.append("delivery_type", cartState.deliveryType);
        p.append("notes", cartState.notes);
        if(cartState.promoCode && cartState.discount > 0) p.append("promo_code", cartState.promoCode);
        const r = await fetch("/api.php", { method:"POST", body:p });
        const d = await r.json();
        if(d.success) {
            const orderIdParam = d.data?.order_id ? `?order_id=${d.data.order_id}` : "";
            setTimeout(() => { location.href = `/cart/confirmation${orderIdParam}`; }, 400);
        } else { throw new Error(d.message); }
    } catch(err) {
        if(err.message && (err.message.includes("login") || err.message.includes("Akses ditolak"))) {
            Swal.fire({
                icon: "warning",
                title: "Sesi Berakhir",
                text: "Sesi login Anda telah berakhir. Silakan masuk kembali.",
                confirmButtonText: "Masuk Sekarang",
                confirmButtonColor: "#FF3D2E",
                background: "#1A1A1F",
                color: "#FAFAFA"
            }).then(() => {
                location.href = "/login?redirect=cart";
            });
            return;
        }
        Swal.fire({ icon:"error", title:"Checkout Gagal", text:err.message, background:"#1A1A1F", color:"#FAFAFA", confirmButtonColor:"#FF3D2E" });
        btn.disabled = false;
        btn.innerHTML = "<ion-icon name=\"card-outline\"></ion-icon><span>Pesan Sekarang</span>";
        document.getElementById("step1").classList.remove("done");
        document.getElementById("step2").classList.remove("active");
    }
}
document.addEventListener("DOMContentLoaded", render);
