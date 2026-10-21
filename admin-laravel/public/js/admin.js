/**
 * Cimohot Command Center — Core Admin Script
 * Handles Theme, Notifications, Charts, and AJAX CRUD Modules
 */

"use strict";

// CSRF Token Helper
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

// Reusable API Fetcher
async function apiFetch(url, options = {}) {
    if (url.startsWith('/api/')) {
        url = '/admin' + url;
    }

    const defaultHeaders = {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
    };

    if (!(options.body instanceof FormData)) {
        defaultHeaders['Content-Type'] = 'application/json';
    }

    options.headers = { ...defaultHeaders, ...options.headers };

    try {
        const response = await fetch(url, options);
        const data = await response.json();
        if (!response.ok) {
            throw new Error(data.message || `HTTP Error ${response.status}`);
        }
        return data;
    } catch (error) {
        console.error('API Error:', error);
        throw error;
    }
}

// Format Rupiah
function formatRupiah(number) {
    return 'Rp ' + Number(number).toLocaleString('id-ID');
}

// Document Ready
document.addEventListener('DOMContentLoaded', () => {
    initTheme();
    initSidebar();
    initNotifications();

    // Module Auto-detect based on current page
    const page = document.body.dataset.page;
    if (page === 'dashboard') initDashboard();
    if (page === 'orders') initOrders();
    if (page === 'menu') initMenu();
    if (page === 'customers') initCustomers();
    if (page === 'reports') initReports();
    if (page === 'promotions') initPromotions();
});

/* ==========================================================================
   THEME TOGGLE (Dark / Light)
   ========================================================================== */
function initTheme() {
    const themeBtn = document.getElementById('theme-toggle-btn');
    const savedTheme = localStorage.getItem('cimohot_admin_theme') || 'dark';

    document.documentElement.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    if (themeBtn) {
        themeBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'dark';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('cimohot_admin_theme', newTheme);
            updateThemeIcon(newTheme);

            // Re-render chart colors if on dashboard
            if (window.renderCharts) window.renderCharts();
        });
    }
}

function updateThemeIcon(theme) {
    const icon = document.querySelector('#theme-toggle-btn ion-icon');
    if (icon) {
        icon.setAttribute('name', theme === 'dark' ? 'moon-outline' : 'sunny-outline');
    }
}

/* ==========================================================================
   SIDEBAR & MOBILE
   ========================================================================== */
function initSidebar() {
    const toggleBtn = document.getElementById('mobile-menu-toggle');
    const closeBtn = document.getElementById('sidebar-close-btn');
    const sidebar = document.getElementById('admin-sidebar');
    const overlay = document.getElementById('sidebar-backdrop');

    if (!sidebar) return;

    const closeSidebar = () => {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.style.display = 'none';
    };

    if (toggleBtn) {
        toggleBtn.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('mobile-open');
            if (overlay) overlay.style.display = isOpen ? 'block' : 'none';
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeSidebar);
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }
}

/* ==========================================================================
   REAL-TIME NOTIFICATIONS
   ========================================================================== */
function initNotifications() {
    const bellBtn = document.getElementById('notification-bell-btn');
    const dropdown = document.getElementById('notification-dropdown');
    const badge = document.getElementById('notification-badge');
    const list = document.getElementById('notification-list');
    const countText = document.getElementById('notification-count-text');

    if (bellBtn && dropdown) {
        bellBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('active');
        });

        document.addEventListener('click', (e) => {
            if (!dropdown.contains(e.target) && !bellBtn.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    }

    async function pollNotifications() {
        try {
            const res = await apiFetch('/api/notifications');
            if (res.success && res.data) {
                const { pending_count, orders } = res.data;

                // Update badge in topbar & sidebar
                if (badge) {
                    if (pending_count > 0) {
                        badge.style.display = 'inline-flex';
                        badge.textContent = pending_count;
                    } else {
                        badge.style.display = 'none';
                    }
                }

                const sidebarOrdersBadge = document.getElementById('sidebar-orders-badge');
                if (sidebarOrdersBadge) {
                    sidebarOrdersBadge.textContent = pending_count > 0 ? pending_count : '';
                    sidebarOrdersBadge.style.display = pending_count > 0 ? 'inline-block' : 'none';
                }

                if (countText) {
                    countText.textContent = `${pending_count} Pesanan Baru`;
                }

                if (list) {
                    if (orders.length === 0) {
                        list.innerHTML = `<p style="color:var(--text-muted);font-size:0.85rem;text-align:center;padding:12px;">Tidak ada pesanan pending saat ini.</p>`;
                    } else {
                        list.innerHTML = orders.map(o => `
                            <div class="notification-item" onclick="window.location.href='/admin/orders'">
                                <div class="notification-item-icon">
                                    <ion-icon name="cart-outline"></ion-icon>
                                </div>
                                <div class="notification-item-content">
                                    <div class="notification-item-title">Pesanan #${o.order_id} — ${o.customer}</div>
                                    <div class="notification-item-sub">${o.total}</div>
                                    <div class="notification-item-time">${o.time_ago}</div>
                                </div>
                            </div>
                        `).join('');
                    }
                }
            }
        } catch (e) {
            console.warn('Poll error:', e);
        }
    }

    pollNotifications();
    // Poll every 30 seconds
    setInterval(pollNotifications, 30000);
}

/* ==========================================================================
   DASHBOARD MODULE (Elevated Real-Time Kitchen & Cashier Command Center)
   ========================================================================== */
function initDashboard() {
    let salesChartInstance = null;
    let categoryChartInstance = null;
    let prevPendingIds = null;

    // Web Audio Chime for New Orders
    function playKitchenChime() {
        if (localStorage.getItem('cimohot_sound_enabled') === 'false') return;
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const now = ctx.currentTime;

            // Two-tone chime (E5 -> G#5)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(659.25, now);
            gain1.gain.setValueAtTime(0.22, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.55);

            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(830.61, now + 0.14);
            gain2.gain.setValueAtTime(0.26, now + 0.14);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.85);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.14);
            osc2.stop(now + 0.85);
        } catch (e) {
            console.log('Audio chime error:', e);
        }
    }

    // Audio notification toggle
    const soundBtn = document.getElementById('btn-toggle-sound');
    const soundIcon = document.getElementById('sound-icon');
    const soundText = document.getElementById('sound-text');
    let soundEnabled = localStorage.getItem('cimohot_sound_enabled') !== 'false';

    function updateSoundUI() {
        if (soundIcon) soundIcon.setAttribute('name', soundEnabled ? 'volume-high-outline' : 'volume-mute-outline');
        if (soundText) soundText.textContent = soundEnabled ? 'Suara: Aktif' : 'Suara: Senyap';
    }

    if (soundBtn) {
        updateSoundUI();
        soundBtn.addEventListener('click', () => {
            soundEnabled = !soundEnabled;
            localStorage.setItem('cimohot_sound_enabled', soundEnabled ? 'true' : 'false');
            updateSoundUI();
            if (soundEnabled) playKitchenChime();
        });
    }

    // Manual refresh button
    const refreshBtn = document.getElementById('btn-manual-refresh');
    const refreshIcon = document.getElementById('refresh-icon');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', async () => {
            if (refreshIcon) refreshIcon.style.animation = 'spin 0.8s linear infinite';
            await loadStats();
            if (refreshIcon) refreshIcon.style.animation = 'none';
        });
    }

    // Modal elements
    const orderModal = document.getElementById('quick-order-modal');
    const orderModalContent = document.getElementById('modal-order-content');
    const orderModalTitle = document.getElementById('modal-order-title');
    const orderModalTime = document.getElementById('modal-order-time');
    const closeOrderModalBtn = document.getElementById('btn-close-order-modal');

    const restockModal = document.getElementById('quick-restock-modal');
    const restockForm = document.getElementById('quick-restock-form');
    const restockMenuId = document.getElementById('restock-menu-id');
    const restockMenuName = document.getElementById('restock-menu-name');
    const restockStockVal = document.getElementById('restock-stock-val');
    const closeRestockModalBtn = document.getElementById('btn-close-restock-modal');
    const cancelRestockBtn = document.getElementById('btn-cancel-restock');

    if (closeOrderModalBtn && orderModal) {
        closeOrderModalBtn.addEventListener('click', () => { orderModal.style.display = 'none'; });
        orderModal.addEventListener('click', (e) => { if (e.target === orderModal) orderModal.style.display = 'none'; });
    }

    if (closeRestockModalBtn && restockModal) {
        closeRestockModalBtn.addEventListener('click', () => { restockModal.style.display = 'none'; });
        cancelRestockBtn?.addEventListener('click', () => { restockModal.style.display = 'none'; });
        restockModal.addEventListener('click', (e) => { if (e.target === restockModal) restockModal.style.display = 'none'; });
    }

    // Handle Quick Restock Submit
    if (restockForm) {
        restockForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = restockMenuId.value;
            const newStock = parseInt(restockStockVal.value, 10);
            const submitBtn = document.getElementById('btn-submit-restock');

            try {
                if (submitBtn) submitBtn.disabled = true;
                const res = await apiFetch(`/api/menu/${id}/stock`, {
                    method: 'PATCH',
                    body: JSON.stringify({ stock: newStock })
                });

                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Stok Berhasil Diperbarui',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    restockModal.style.display = 'none';
                    await loadStats();
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal memperbarui stok', 'error');
                }
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            } finally {
                if (submitBtn) submitBtn.disabled = false;
            }
        });
    }

    async function loadStats() {
        try {
            const res = await apiFetch('/api/stats');
            if (!res.success) return;
            const data = res.data;

            // 1. Update KPI stats with count-up animation
            animateValue('stat-revenue', 0, data.revenue_today, 800, true);
            animateValue('stat-orders', 0, data.orders_today, 600, false);
            animateValue('stat-customers', 0, data.total_customers, 600, false);

            document.getElementById('stat-top-product').textContent = data.top_product;
            document.getElementById('stat-top-product-sold').textContent = `${data.top_product_sold} porsi terjual`;

            // Percentage tag
            const revDiff = document.getElementById('stat-revenue-diff');
            if (revDiff) {
                revDiff.textContent = `${data.revenue_diff >= 0 ? '+' : ''}${data.revenue_diff}% vs kemarin`;
            }

            // 2. Operational Kitchen Pipeline Counters
            const pendingEl = document.getElementById('pipeline-pending-count');
            const processingEl = document.getElementById('pipeline-processing-count');
            const completedEl = document.getElementById('pipeline-completed-count');
            const pulseDot = document.getElementById('pending-pulse-dot');
            const pendingHint = document.getElementById('pending-status-hint');
            const pendingCard = document.getElementById('pipeline-pending-card');

            if (pendingEl) pendingEl.textContent = data.pending_count || 0;
            if (processingEl) processingEl.textContent = data.processing_count || 0;
            if (completedEl) completedEl.textContent = data.completed_count || 0;

            if (data.pending_count > 0) {
                if (pulseDot) pulseDot.style.display = 'inline-block';
                if (pendingHint) pendingHint.textContent = `${data.pending_count} butuh respon segera!`;
                if (pendingCard) pendingCard.style.boxShadow = '0 0 16px rgba(255, 107, 53, 0.25)';
            } else {
                if (pulseDot) pulseDot.style.display = 'none';
                if (pendingHint) pendingHint.textContent = 'Semua aman terkendali';
                if (pendingCard) pendingCard.style.boxShadow = 'none';
            }

            // Detect new pending orders for chime
            if (prevPendingIds !== null && Array.isArray(data.active_pending_ids)) {
                const newOrders = data.active_pending_ids.filter(id => !prevPendingIds.includes(id));
                if (newOrders.length > 0) {
                    playKitchenChime();
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: `🔔 ${newOrders.length} Pesanan Baru Masuk!`,
                        text: 'Segera periksa antrean di dashboard.',
                        showConfirmButton: false,
                        timer: 5000,
                        timerProgressBar: true,
                        background: '#1A1A1F',
                        color: '#FAFAFA'
                    });
                }
            }
            prevPendingIds = Array.isArray(data.active_pending_ids) ? data.active_pending_ids : [];

            // 3. Low Stock Menus Container
            const lowStockContainer = document.getElementById('low-stock-container');
            if (lowStockContainer) {
                if (!data.low_stock_menus || data.low_stock_menus.length === 0) {
                    lowStockContainer.innerHTML = `
                        <div style="display:flex;align-items:center;gap:12px;padding:18px;background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:var(--radius-md);color:#4ADE80;font-weight:600;font-size:0.85rem;">
                            <ion-icon name="checkmark-circle-outline" style="font-size:1.6rem;flex-shrink:0;"></ion-icon>
                            <span>Semua stok menu dalam kondisi aman! Tidak ada stok yang menipis saat ini.</span>
                        </div>
                    `;
                } else {
                    lowStockContainer.innerHTML = data.low_stock_menus.map(m => `
                        <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--bg-input);border-radius:var(--radius-md);border:1px solid var(--border-subtle);">
                            <div style="display:flex;align-items:center;gap:12px;">
                                <img src="/${m.image_url}" alt="${m.name}" style="width:38px;height:38px;border-radius:8px;object-fit:cover;" onerror="this.src='/assets/images/default-food.png'">
                                <div>
                                    <div style="font-weight:700;font-size:0.88rem;color:var(--text-primary);">${m.name}</div>
                                    <div style="font-size:0.75rem;color:var(--text-muted);">${m.category} • ${m.price_formatted}</div>
                                </div>
                            </div>
                            <div style="display:flex;align-items:center;gap:10px;">
                                <span class="badge ${m.stock <= 2 ? 'badge-cancelled' : 'badge-pending'}" style="font-size:0.75rem;padding:4px 8px;">
                                    Sisa ${m.stock}
                                </span>
                                <button type="button" class="btn btn-secondary btn-sm btn-quick-stock" data-id="${m.menu_id}" data-name="${m.name}" data-stock="${m.stock}" style="padding:4px 10px;font-size:0.75rem;">
                                    + Tambah
                                </button>
                            </div>
                        </div>
                    `).join('');

                    // Bind restock click events
                    lowStockContainer.querySelectorAll('.btn-quick-stock').forEach(btn => {
                        btn.addEventListener('click', () => {
                            if (!restockModal) return;
                            restockMenuId.value = btn.dataset.id;
                            restockMenuName.value = btn.dataset.name;
                            restockStockVal.value = btn.dataset.stock;
                            restockModal.style.display = 'flex';
                            restockStockVal.focus();
                        });
                    });
                }
            }

            // 4. Payment Breakdown Bars & Text
            if (data.payment_breakdown) {
                const pb = data.payment_breakdown;
                const barTransfer = document.getElementById('bar-pay-transfer');
                const barCash = document.getElementById('bar-pay-cash');
                const txtTransferRev = document.getElementById('pay-transfer-rev');
                const txtTransferCount = document.getElementById('pay-transfer-count');
                const txtCashRev = document.getElementById('pay-cash-rev');
                const txtCashCount = document.getElementById('pay-cash-count');

                if (barTransfer) barTransfer.style.width = `${pb.transfer_pct}%`;
                if (barCash) barCash.style.width = `${pb.cash_pct}%`;
                if (txtTransferRev) txtTransferRev.textContent = pb.transfer_rev_formatted;
                if (txtTransferCount) txtTransferCount.textContent = `${pb.transfer_count} transaksi (${pb.transfer_pct}%)`;
                if (txtCashRev) txtCashRev.textContent = pb.cash_rev_formatted;
                if (txtCashCount) txtCashCount.textContent = `${pb.cash_count} transaksi (${pb.cash_pct}%)`;
            }

            // 5. Recent Orders Table with Action Buttons
            const recentContainer = document.getElementById('recent-orders-body');
            if (recentContainer && data.recent_orders) {
                if (data.recent_orders.length === 0) {
                    recentContainer.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">Belum ada pesanan terbaru hari ini.</td></tr>`;
                } else {
                    recentContainer.innerHTML = data.recent_orders.map(o => `
                        <tr>
                            <td class="mono" style="font-weight:700;">#${o.order_id}</td>
                            <td>
                                <div style="font-weight:700;">${o.customer_name}</div>
                                <div style="font-size:0.75rem;color:var(--text-muted);">${o.customer_phone || '-'}</div>
                            </td>
                            <td>
                                <div style="font-size:0.82rem; max-width:200px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;" title="${o.items_summary}">
                                    ${o.items_summary}
                                </div>
                                <div style="font-size:0.72rem;color:var(--brand-gold);">${o.payment_method}</div>
                            </td>
                            <td class="mono" style="font-weight:700;color:var(--brand-gold);">${o.total_formatted}</td>
                            <td><span class="status-badge status-${o.status}">${o.status}</span></td>
                            <td>
                                <div style="display:flex;gap:6px;align-items:center;">
                                    <button type="button" class="btn btn-secondary btn-sm btn-quick-detail" data-id="${o.order_id}" style="padding:4px 8px;font-size:0.75rem;">
                                        <ion-icon name="eye-outline"></ion-icon>
                                        <span>Detail</span>
                                    </button>
                                    ${o.status === 'pending' ? `
                                        <button type="button" class="btn btn-primary btn-sm btn-quick-accept" data-id="${o.order_id}" style="padding:4px 10px;font-size:0.75rem;background:var(--brand-orange);border-color:var(--brand-orange);">
                                            <ion-icon name="flame-outline"></ion-icon>
                                            <span>Terima</span>
                                        </button>
                                    ` : ''}
                                </div>
                            </td>
                        </tr>
                    `).join('');

                    // Bind Quick Detail clicks
                    recentContainer.querySelectorAll('.btn-quick-detail').forEach(btn => {
                        btn.addEventListener('click', () => showQuickOrderDetail(btn.dataset.id));
                    });

                    // Bind Quick Accept clicks
                    recentContainer.querySelectorAll('.btn-quick-accept').forEach(btn => {
                        btn.addEventListener('click', () => quickAcceptOrder(btn.dataset.id));
                    });
                }
            }

            // Update timestamp
            const updatedEl = document.getElementById('last-updated-text');
            if (updatedEl) {
                const now = new Date();
                updatedEl.textContent = `Diperbarui: ${now.toLocaleTimeString('id-ID')}`;
            }

        } catch (err) {
            console.error('Error loading stats:', err);
        }
    }

    // Quick Accept Order (Pending -> Diproses)
    async function quickAcceptOrder(orderId) {
        try {
            const res = await apiFetch(`/api/orders/${orderId}/status`, {
                method: 'PATCH',
                body: JSON.stringify({ status: 'diproses' })
            });

            if (res.success) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: `Pesanan #${orderId} Diterima!`,
                    text: 'Status diubah ke Diproses (Dapur).',
                    timer: 2000,
                    showConfirmButton: false,
                    background: '#1A1A1F',
                    color: '#FAFAFA'
                });
                await loadStats();
            } else {
                Swal.fire('Gagal', res.message || 'Gagal mengubah status', 'error');
            }
        } catch (err) {
            Swal.fire('Error', err.message, 'error');
        }
    }

    // Show Quick Order Detail Modal
    async function showQuickOrderDetail(orderId) {
        if (!orderModal || !orderModalContent) return;
        orderModal.style.display = 'flex';
        orderModalTitle.textContent = `Detail Pesanan #${orderId}`;
        orderModalContent.innerHTML = `<div style="text-align:center;padding:32px;"><div class="spinner"></div> Memuat rincian pesanan...</div>`;

        try {
            const res = await apiFetch(`/api/orders/${orderId}`);
            if (!res.success) throw new Error(res.message);

            const o = res.data;
            if (orderModalTime) orderModalTime.textContent = o.order_date;

            orderModalContent.innerHTML = `
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px; background:var(--bg-input); padding:14px; border-radius:var(--radius-md); font-size:0.85rem;">
                    <div>
                        <div style="color:var(--text-muted);font-size:0.75rem;">Pelanggan:</div>
                        <div style="font-weight:700;">${o.customer_name}</div>
                        <div style="color:var(--text-secondary);font-size:0.78rem;">${o.customer_phone || '-'}</div>
                    </div>
                    <div>
                        <div style="color:var(--text-muted);font-size:0.75rem;">Metode & Status:</div>
                        <div><strong style="color:var(--brand-gold);">${o.payment_method}</strong> • <span class="status-badge status-${o.status}">${o.status}</span></div>
                        <div style="color:var(--text-muted);font-size:0.75rem;">Alamat: ${o.delivery_address}</div>
                    </div>
                </div>

                ${o.notes && o.notes !== '-' ? `
                    <div style="padding:10px 14px; background:rgba(245,166,35,0.08); border:1px solid rgba(245,166,35,0.2); border-radius:var(--radius-sm); margin-bottom:16px; font-size:0.82rem; color:var(--brand-gold);">
                        <strong>Catatan:</strong> ${o.notes}
                    </div>
                ` : ''}

                <div class="table-responsive" style="margin-bottom:18px;">
                    <table class="data-table" style="font-size:0.85rem;">
                        <thead>
                            <tr>
                                <th>Item Menu</th>
                                <th style="text-align:center;">Qty</th>
                                <th style="text-align:right;">Harga</th>
                                <th style="text-align:right;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${o.items.map(it => `
                                <tr>
                                    <td>
                                        <div style="font-weight:700;">${it.menu_name}</div>
                                        <div style="font-size:0.72rem;color:var(--text-muted);">${it.category}</div>
                                    </td>
                                    <td style="text-align:center;font-weight:700;">${it.quantity}</td>
                                    <td style="text-align:right;">${it.price_formatted}</td>
                                    <td style="text-align:right;font-weight:700;" class="mono">${it.subtotal_formatted}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right;font-weight:700;padding-top:12px;">Total Transaksi:</td>
                                <td style="text-align:right;font-weight:800;font-size:1.05rem;color:var(--brand-gold);padding-top:12px;" class="mono">${o.total_formatted}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid var(--border-subtle); padding-top:14px;">
                    <div style="display:flex; align-items:center; gap:8px;">
                        <span style="font-size:0.8rem; font-weight:600; color:var(--text-secondary);">Ubah Status:</span>
                        <select id="modal-quick-status-select" class="form-control" style="width:auto; padding:6px 12px; font-size:0.8rem;">
                            <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                            <option value="diproses" ${o.status === 'diproses' ? 'selected' : ''}>Diproses</option>
                            <option value="selesai" ${o.status === 'selesai' ? 'selected' : ''}>Selesai</option>
                            <option value="dibatalkan" ${o.status === 'dibatalkan' ? 'selected' : ''}>Dibatalkan</option>
                        </select>
                    </div>
                    <button type="button" class="btn btn-primary btn-sm" id="btn-save-modal-status">
                        Simpan Status
                    </button>
                </div>
            `;

            document.getElementById('btn-save-modal-status')?.addEventListener('click', async () => {
                const newStatus = document.getElementById('modal-quick-status-select').value;
                try {
                    const statusRes = await apiFetch(`/api/orders/${orderId}/status`, {
                        method: 'PATCH',
                        body: JSON.stringify({ status: newStatus })
                    });
                    if (statusRes.success) {
                        Swal.fire({ icon: 'success', title: 'Berhasil', text: statusRes.message, timer: 1400, showConfirmButton: false });
                        orderModal.style.display = 'none';
                        await loadStats();
                    }
                } catch (e) {
                    Swal.fire('Error', e.message, 'error');
                }
            });

        } catch (err) {
            orderModalContent.innerHTML = `<div style="text-align:center;padding:24px;color:var(--color-danger);">${err.message}</div>`;
        }
    }

    async function loadCharts(days = 7) {
        try {
            const res = await apiFetch(`/api/sales-chart?days=${days}`);
            if (!res.success) return;
            const { line_chart, category_chart } = res.data;

            const isDark = (document.documentElement.getAttribute('data-theme') || 'dark') === 'dark';
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)';
            const textColor = isDark ? '#94A3B8' : '#64748B';

            // 1. Line Chart: Revenue
            const ctxLine = document.getElementById('salesTrendChart')?.getContext('2d');
            if (ctxLine) {
                if (salesChartInstance) salesChartInstance.destroy();

                const gradient = ctxLine.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(255, 61, 46, 0.35)');
                gradient.addColorStop(1, 'rgba(255, 61, 46, 0.0)');

                salesChartInstance = new Chart(ctxLine, {
                    type: 'line',
                    data: {
                        labels: line_chart.labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: line_chart.revenue,
                            borderColor: '#FF3D2E',
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.35,
                            pointRadius: 4,
                            pointBackgroundColor: '#FF3D2E',
                            pointHoverRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: (ctx) => ` Pendapatan: ${formatRupiah(ctx.raw)}`
                                }
                            }
                        },
                        scales: {
                            y: {
                                grid: { color: gridColor },
                                ticks: {
                                    color: textColor,
                                    callback: (val) => val >= 1000 ? (val/1000) + 'k' : val
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: textColor }
                            }
                        }
                    }
                });
            }

            // 2. Donut Chart: Categories
            const ctxCat = document.getElementById('categoryDonutChart')?.getContext('2d');
            if (ctxCat) {
                if (categoryChartInstance) categoryChartInstance.destroy();

                categoryChartInstance = new Chart(ctxCat, {
                    type: 'doughnut',
                    data: {
                        labels: category_chart.labels,
                        datasets: [{
                            data: category_chart.data,
                            backgroundColor: ['#FF3D2E', '#FF6B35', '#F5A623', '#06B6D4', '#8B5CF6'],
                            borderWidth: 2,
                            borderColor: isDark ? '#11151F' : '#FFFFFF',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '72%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { color: textColor, boxWidth: 12, padding: 14 }
                            }
                        }
                    }
                });
            }
        } catch (err) {
            console.error('Error loading charts:', err);
        }
    }

    // Chart filter buttons
    document.querySelectorAll('.chart-filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            document.querySelectorAll('.chart-filter-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            const days = btn.dataset.days;
            loadCharts(days);
        });
    });

    window.renderCharts = () => loadCharts();

    loadStats();
    loadCharts(7);

    // Auto-polling every 20 seconds for real-time kitchen monitoring
    const pollInterval = setInterval(() => {
        if (document.body.dataset.page === 'dashboard') {
            loadStats();
        } else {
            clearInterval(pollInterval);
        }
    }, 20000);
}

// Simple Count-up animation
function animateValue(id, start, end, duration, isCurrency = false) {
    const obj = document.getElementById(id);
    if (!obj) return;
    let startTimestamp = null;
    const step = (timestamp) => {
        if (!startTimestamp) startTimestamp = timestamp;
        const progress = Math.min((timestamp - startTimestamp) / duration, 1);
        const current = Math.floor(progress * (end - start) + start);
        obj.textContent = isCurrency ? formatRupiah(current) : current.toLocaleString('id-ID');
        if (progress < 1) {
            window.requestAnimationFrame(step);
        } else {
            obj.textContent = isCurrency ? formatRupiah(end) : end.toLocaleString('id-ID');
        }
    };
    window.requestAnimationFrame(step);
}

/* ==========================================================================
   ORDERS MODULE
   ========================================================================== */
function initOrders() {
    let currentStatus = 'all';
    let searchQuery = '';
    let currentPage = 1;

    const tableBody = document.getElementById('orders-table-body');
    const paginationContainer = document.getElementById('orders-pagination');
    const searchInput = document.getElementById('orders-search-input');
    const detailModal = document.getElementById('order-detail-modal');

    async function fetchOrders(page = 1) {
        currentPage = page;
        if (tableBody) {
            tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:32px;color:var(--text-muted);"><div class="spinner"></div> Memuat pesanan...</td></tr>`;
        }

        try {
            let url = `/api/orders?page=${page}&status=${currentStatus}`;
            if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

            const res = await apiFetch(url);
            if (!res.success) return;

            const { orders, pagination } = res.data;

            if (orders.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:36px;color:var(--text-muted);">Tidak ada pesanan ditemukan.</td></tr>`;
                if (paginationContainer) paginationContainer.innerHTML = '';
                return;
            }

            tableBody.innerHTML = orders.map(o => `
                <tr>
                    <td class="mono">#${o.order_id}</td>
                    <td>
                        <div style="font-weight:700;">${o.customer_name}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">${o.customer_phone}</div>
                    </td>
                    <td class="mono" style="font-weight:700;color:var(--brand-gold);">${o.total_formatted}</td>
                    <td><span class="status-badge status-${o.status}">${o.status}</span></td>
                    <td><span style="font-size:0.75rem;font-weight:700;background:var(--bg-input);padding:4px 8px;border-radius:6px;border:1px solid var(--border-subtle);">${o.payment_method}</span></td>
                    <td style="color:var(--text-secondary);font-size:0.82rem;">${o.order_date}</td>
                    <td>
                        <div style="display:flex;gap:6px;">
                            <button class="btn btn-secondary btn-sm btn-view-order" data-id="${o.order_id}">
                                <ion-icon name="eye-outline"></ion-icon> Detail
                            </button>
                            <select class="form-control select-quick-status" data-id="${o.order_id}" style="width:auto;padding:5px 10px;font-size:0.78rem;">
                                <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="diproses" ${o.status === 'diproses' ? 'selected' : ''}>Diproses</option>
                                <option value="selesai" ${o.status === 'selesai' ? 'selected' : ''}>Selesai</option>
                                <option value="dibatalkan" ${o.status === 'dibatalkan' ? 'selected' : ''}>Batalkan</option>
                            </select>
                        </div>
                    </td>
                </tr>
            `).join('');

            // Pagination Controls
            renderPagination(pagination, paginationContainer, (p) => fetchOrders(p));

            // Attach event listeners for detail and status
            attachOrderEvents();

        } catch (err) {
            console.error('Error fetching orders:', err);
            if (tableBody) tableBody.innerHTML = `<tr><td colspan="7" style="text-align:center;padding:24px;color:var(--color-danger);">Gagal memuat pesanan.</td></tr>`;
        }
    }

    function attachOrderEvents() {
        document.querySelectorAll('.btn-view-order').forEach(btn => {
            btn.addEventListener('click', () => showOrderDetail(btn.dataset.id));
        });

        document.querySelectorAll('.select-quick-status').forEach(select => {
            select.addEventListener('change', async () => {
                const orderId = select.dataset.id;
                const newStatus = select.value;
                try {
                    const res = await apiFetch(`/api/orders/${orderId}/status`, {
                        method: 'PATCH',
                        body: JSON.stringify({ status: newStatus })
                    });
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Status Diperbarui',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#11151F',
                            color: '#F8FAFC'
                        });
                        fetchOrders(currentPage);
                    }
                } catch (e) {
                    Swal.fire('Gagal', e.message, 'error');
                }
            });
        });
    }

    async function showOrderDetail(orderId) {
        if (!detailModal) return;
        detailModal.classList.add('active');
        const content = document.getElementById('order-detail-content');
        content.innerHTML = `<div style="text-align:center;padding:30px;"><p>Memuat detail pesanan #${orderId}...</p></div>`;

        try {
            const res = await apiFetch(`/api/orders/${orderId}`);
            if (!res.success) return;
            const o = res.data;

            content.innerHTML = `
                <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:20px;padding-bottom:16px;border-bottom:1px solid var(--border-subtle);">
                    <div>
                        <div style="font-size:1.25rem;font-weight:800;" class="mono">Pesanan #${o.order_id}</div>
                        <div style="color:var(--text-muted);font-size:0.8rem;margin-top:2px;">${o.order_date}</div>
                    </div>
                    <span class="status-badge status-${o.status}">${o.status}</span>
                </div>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px;background:var(--bg-input);padding:14px;border-radius:var(--radius-md);">
                    <div>
                        <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;">Pelanggan</div>
                        <div style="font-weight:700;">${o.customer_name}</div>
                        <div style="font-size:0.8rem;color:var(--text-secondary);">${o.customer_phone}</div>
                    </div>
                    <div>
                        <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;">Pembayaran</div>
                        <div style="font-weight:700;">${o.payment_method}</div>
                        <div style="font-size:0.8rem;color:var(--brand-gold);font-weight:800;">Total: ${o.total_formatted}</div>
                    </div>
                </div>

                <div style="margin-bottom:16px;">
                    <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Alamat Pengiriman</div>
                    <div style="font-size:0.85rem;background:var(--bg-input);padding:10px;border-radius:var(--radius-sm);">${o.delivery_address}</div>
                </div>

                ${o.notes && o.notes !== '-' ? `
                <div style="margin-bottom:16px;">
                    <div style="font-size:0.75rem;color:var(--text-muted);text-transform:uppercase;margin-bottom:4px;">Catatan Khusus</div>
                    <div style="font-size:0.85rem;background:var(--bg-input);padding:10px;border-radius:var(--radius-sm);color:var(--brand-gold);">${o.notes}</div>
                </div>` : ''}

                <div style="font-size:0.8rem;font-weight:700;text-transform:uppercase;margin-bottom:10px;color:var(--text-secondary);">Item Pesanan</div>
                <div class="table-responsive" style="margin-bottom:20px;">
                    <table class="data-table" style="font-size:0.82rem;">
                        <thead>
                            <tr>
                                <th>Menu</th>
                                <th>Harga</th>
                                <th>Qty</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${o.items.map(item => `
                                <tr>
                                    <td><strong>${item.menu_name}</strong></td>
                                    <td class="mono">${item.price_formatted}</td>
                                    <td class="mono">x${item.quantity}</td>
                                    <td class="mono" style="font-weight:700;">${item.subtotal_formatted}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>

                <div style="display:flex;justify-content:flex-end;align-items:center;gap:12px;margin-top:20px;">
                    <label style="font-size:0.85rem;font-weight:700;">Ubah Status:</label>
                    <select id="modal-order-status-select" class="form-control" style="width:160px;">
                        <option value="pending" ${o.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="diproses" ${o.status === 'diproses' ? 'selected' : ''}>Diproses</option>
                        <option value="selesai" ${o.status === 'selesai' ? 'selected' : ''}>Selesai</option>
                        <option value="dibatalkan" ${o.status === 'dibatalkan' ? 'selected' : ''}>Dibatalkan</option>
                    </select>
                    <button class="btn btn-primary btn-sm" id="btn-save-modal-status" data-id="${o.order_id}">Simpan</button>
                </div>
            `;

            document.getElementById('btn-save-modal-status')?.addEventListener('click', async () => {
                const newStatus = document.getElementById('modal-order-status-select').value;
                try {
                    const statusRes = await apiFetch(`/api/orders/${o.order_id}/status`, {
                        method: 'PATCH',
                        body: JSON.stringify({ status: newStatus })
                    });
                    if (statusRes.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: statusRes.message,
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#11151F',
                            color: '#F8FAFC'
                        });
                        detailModal.classList.remove('active');
                        fetchOrders(currentPage);
                    }
                } catch (e) {
                    Swal.fire('Gagal', e.message, 'error');
                }
            });

        } catch (e) {
            content.innerHTML = `<div style="color:var(--color-danger);padding:20px;text-align:center;">Gagal mengambil detail pesanan.</div>`;
        }
    }

    // Modal Close
    document.querySelectorAll('.modal-close-trigger').forEach(btn => {
        btn.addEventListener('click', () => {
            if (detailModal) detailModal.classList.remove('active');
        });
    });

    // Tab Filters
    document.querySelectorAll('.orders-filter-tab').forEach(tab => {
        tab.addEventListener('click', () => {
            document.querySelectorAll('.orders-filter-tab').forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            currentStatus = tab.dataset.status;
            fetchOrders(1);
        });
    });

    // Search input with debounce
    let searchTimer;
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                searchQuery = e.target.value.trim();
                fetchOrders(1);
            }, 350);
        });
    }

    fetchOrders(1);
}

/* ==========================================================================
   MENU MODULE
   ========================================================================== */
function initMenu() {
    let currentCategory = 'all';
    let searchQuery = '';

    const grid = document.getElementById('menu-grid');
    const modal = document.getElementById('menu-form-modal');
    const form = document.getElementById('menu-form');
    const addBtn = document.getElementById('add-menu-btn');
    const modalTitle = document.getElementById('menu-modal-title');
    const searchInput = document.getElementById('menu-search-input');
    const filterTabsContainer = document.getElementById('menu-category-tabs');

    async function fetchMenu() {
        if (!grid) return;
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);"><div class="spinner"></div> Memuat menu...</div>`;

        try {
            let url = `/api/menu?category=${currentCategory}`;
            if (searchQuery) url += `&search=${encodeURIComponent(searchQuery)}`;

            const res = await apiFetch(url);
            if (!res.success) return;

            const { menus, categories } = res.data;

            // Render category buttons if not rendered
            if (filterTabsContainer && filterTabsContainer.children.length <= 1) {
                categories.forEach(cat => {
                    const btn = document.createElement('button');
                    btn.className = 'filter-tab-btn menu-cat-tab';
                    btn.dataset.category = cat;
                    btn.textContent = cat;
                    btn.addEventListener('click', () => {
                        document.querySelectorAll('.menu-cat-tab').forEach(t => t.classList.remove('active'));
                        btn.classList.add('active');
                        currentCategory = cat;
                        fetchMenu();
                    });
                    filterTabsContainer.appendChild(btn);
                });
            }

            if (menus.length === 0) {
                grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--text-muted);">Tidak ada menu ditemukan.</div>`;
                return;
            }

            grid.innerHTML = menus.map(m => {
                const stockClass = m.stock > 10 ? 'in-stock' : (m.stock > 0 ? 'low-stock' : 'out-stock');
                const stockText = m.stock > 0 ? `Stok: ${m.stock}` : 'Habis';

                return `
                    <div class="menu-card" data-id="${m.menu_id}">
                        <div class="menu-card-img-wrap">
                            <img src="${m.image_url}" alt="${m.name}" loading="lazy" onerror="this.src='/assets/images/default-food.png'">
                            <span class="menu-category-tag">${m.category}</span>
                        </div>
                        <div class="menu-card-body">
                            <h4 class="menu-card-title">${m.name}</h4>
                            <p class="menu-card-desc">${m.description || 'Tidak ada deskripsi.'}</p>
                            <div class="menu-card-footer">
                                <div>
                                    <div class="menu-card-price">${m.price_formatted}</div>
                                    <div class="menu-card-stock ${stockClass}">${stockText}</div>
                                </div>
                                <div class="menu-card-actions">
                                    <button class="btn btn-secondary btn-icon btn-edit-menu" data-menu='${JSON.stringify(m).replace(/'/g, "&apos;")}'>
                                        <ion-icon name="create-outline"></ion-icon>
                                    </button>
                                    <button class="btn btn-danger btn-icon btn-delete-menu" data-id="${m.menu_id}" data-name="${m.name}">
                                        <ion-icon name="trash-outline"></ion-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');

            // Event Listeners for Edit and Delete
            document.querySelectorAll('.btn-edit-menu').forEach(btn => {
                btn.addEventListener('click', () => {
                    const menu = JSON.parse(btn.dataset.menu);
                    openEditModal(menu);
                });
            });

            document.querySelectorAll('.btn-delete-menu').forEach(btn => {
                btn.addEventListener('click', () => {
                    const id = btn.dataset.id;
                    const name = btn.dataset.name;
                    confirmDeleteMenu(id, name);
                });
            });

        } catch (err) {
            console.error('Error fetching menu:', err);
            grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:30px;color:var(--color-danger);">Gagal memuat menu.</div>`;
        }
    }

    function openEditModal(menu) {
        if (!modal || !form) return;
        modalTitle.textContent = `Edit Menu — ${menu.name}`;
        form.reset();
        document.getElementById('menu-id').value = menu.menu_id;
        document.getElementById('menu-name').value = menu.name;
        document.getElementById('menu-price').value = menu.price;
        document.getElementById('menu-stock').value = menu.stock;
        document.getElementById('menu-category').value = menu.category;
        document.getElementById('menu-description').value = menu.description || '';

        const hint = document.getElementById('menu-image-hint');
        if (hint) hint.textContent = '* Kosongkan jika tidak ingin mengubah foto menu yang sudah ada.';

        modal.classList.add('active');
    }

    function confirmDeleteMenu(id, name) {
        Swal.fire({
            title: 'Hapus Menu?',
            text: `Apakah Anda yakin ingin menghapus "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            cancelButtonColor: '#1E2433',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            background: '#11151F',
            color: '#F8FAFC'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await apiFetch(`/api/menu/${id}`, { method: 'DELETE' });
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#11151F',
                            color: '#F8FAFC'
                        });
                        fetchMenu();
                    }
                } catch (e) {
                    Swal.fire('Gagal', e.message, 'error');
                }
            }
        });
    }

    // Add Menu Button Click
    if (addBtn) {
        addBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Tambah Menu Baru';
            form.reset();
            document.getElementById('menu-id').value = '';
            const hint = document.getElementById('menu-image-hint');
            if (hint) hint.textContent = '* Upload foto menu beresolusi jelas (maksimal 3MB).';
            modal.classList.add('active');
        });
    }

    // Modal Close
    document.querySelectorAll('.menu-modal-close').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    });

    // Form Submit
    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('menu-id').value;
            const formData = new FormData(form);

            const url = id ? `/api/menu/${id}` : '/api/menu';
            const saveBtn = document.getElementById('btn-save-menu');
            if (saveBtn) {
                saveBtn.disabled = true;
                saveBtn.innerHTML = '<div class="spinner"></div> Menyimpan...';
            }

            try {
                const res = await apiFetch(url, {
                    method: 'POST',
                    body: formData
                });

                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#11151F',
                        color: '#F8FAFC'
                    });
                    modal.classList.remove('active');
                    fetchMenu();
                }
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            } finally {
                if (saveBtn) {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = 'Simpan Menu';
                }
            }
        });
    }

    // Category All Tab
    document.querySelector('.menu-cat-all')?.addEventListener('click', (e) => {
        document.querySelectorAll('.menu-cat-tab').forEach(t => t.classList.remove('active'));
        e.target.classList.add('active');
        currentCategory = 'all';
        fetchMenu();
    });

    // Search with debounce
    let timer;
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                searchQuery = e.target.value.trim();
                fetchMenu();
            }, 350);
        });
    }

    fetchMenu();
}

/* ==========================================================================
   CUSTOMERS MODULE
   ========================================================================== */
function initCustomers() {
    const tableBody = document.getElementById('customers-table-body');
    const searchInput = document.getElementById('customer-search-input');
    const modal = document.getElementById('customer-history-modal');

    async function fetchCustomers(search = '') {
        if (!tableBody) return;
        tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:30px;color:var(--text-muted);"><div class="spinner"></div> Memuat pelanggan...</td></tr>`;

        try {
            let url = '/api/customers';
            if (search) url += `?search=${encodeURIComponent(search)}`;

            const res = await apiFetch(url);
            if (!res.success) return;

            const { customers } = res.data;
            if (customers.length === 0) {
                tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:36px;color:var(--text-muted);">Tidak ada data pelanggan.</td></tr>`;
                return;
            }

            tableBody.innerHTML = customers.map(c => `
                <tr>
                    <td>
                        <div style="font-weight:700;">${c.name}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">${c.email}</div>
                    </td>
                    <td>${c.phone}</td>
                    <td style="max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${c.address}</td>
                    <td class="mono" style="text-align:center;font-weight:700;">${c.total_orders}</td>
                    <td class="mono" style="font-weight:700;color:var(--brand-gold);">${c.total_spent_formatted}</td>
                    <td>
                        <button class="btn btn-secondary btn-sm btn-customer-history" data-id="${c.user_id}" data-name="${c.name}">
                            <ion-icon name="time-outline"></ion-icon> Riwayat
                        </button>
                    </td>
                </tr>
            `).join('');

            document.querySelectorAll('.btn-customer-history').forEach(btn => {
                btn.addEventListener('click', () => showCustomerHistory(btn.dataset.id, btn.dataset.name));
            });

        } catch (e) {
            tableBody.innerHTML = `<tr><td colspan="6" style="text-align:center;color:var(--color-danger);">Gagal memuat data pelanggan.</td></tr>`;
        }
    }

    async function showCustomerHistory(customerId, customerName) {
        if (!modal) return;
        modal.classList.add('active');
        const title = document.getElementById('customer-history-modal-title');
        const body = document.getElementById('customer-history-body');

        if (title) title.textContent = `Riwayat Pesanan: ${customerName}`;
        if (body) body.innerHTML = `<div style="text-align:center;padding:24px;color:var(--text-muted);"><div class="spinner"></div> Mengambil riwayat...</div>`;

        try {
            const res = await apiFetch(`/api/customers/${customerId}/history`);
            if (!res.success) return;

            const { orders } = res.data;
            if (orders.length === 0) {
                body.innerHTML = `<p style="text-align:center;padding:24px;color:var(--text-muted);">Pelanggan ini belum pernah melakukan pemesanan.</p>`;
                return;
            }

            body.innerHTML = `
                <div class="table-responsive">
                    <table class="data-table" style="font-size:0.82rem;">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Metode</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${orders.map(o => `
                                <tr>
                                    <td class="mono">#${o.order_id}</td>
                                    <td class="mono" style="font-weight:700;color:var(--brand-gold);">${o.total_formatted}</td>
                                    <td><span class="status-badge status-${o.status}">${o.status}</span></td>
                                    <td>${o.payment_method}</td>
                                    <td style="color:var(--text-muted);">${o.order_date}</td>
                                </tr>
                            `).join('')}
                        </tbody>
                    </table>
                </div>
            `;
        } catch (e) {
            body.innerHTML = `<p style="color:var(--color-danger);text-align:center;">Gagal memuat riwayat pesanan.</p>`;
        }
    }

    document.querySelectorAll('.customer-modal-close').forEach(btn => {
        btn.addEventListener('click', () => modal.classList.remove('active'));
    });

    let timer;
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            clearTimeout(timer);
            timer = setTimeout(() => {
                fetchCustomers(e.target.value.trim());
            }, 350);
        });
    }

    fetchCustomers();
}

/* ==========================================================================
   REPORTS MODULE
   ========================================================================== */
function initReports() {
    const startDateInput = document.getElementById('report-start-date');
    const endDateInput = document.getElementById('report-end-date');
    const generateBtn = document.getElementById('btn-generate-report');
    const pdfBtn = document.getElementById('btn-export-pdf');
    const excelBtn = document.getElementById('btn-export-excel');
    const resultsContainer = document.getElementById('report-results-wrapper');

    // Default dates: First day of month to today
    const now = new Date();
    const firstDay = new Date(now.getFullYear(), now.getMonth(), 1).toISOString().split('T')[0];
    const today = now.toISOString().split('T')[0];

    if (startDateInput) startDateInput.value = firstDay;
    if (endDateInput) endDateInput.value = today;

    async function generateReport() {
        const start = startDateInput.value;
        const end = endDateInput.value;

        if (!start || !end) {
            Swal.fire('Peringatan', 'Silakan pilih rentang tanggal laporan.', 'warning');
            return;
        }

        if (generateBtn) {
            generateBtn.disabled = true;
            generateBtn.innerHTML = '<div class="spinner"></div> Memproses...';
        }

        try {
            const res = await apiFetch(`/api/reports/generate?start_date=${start}&end_date=${end}`);
            if (!res.success) return;

            const data = res.data;

            // Update KPI summary cards
            document.getElementById('rep-revenue').textContent = data.total_revenue_formatted;
            document.getElementById('rep-orders').textContent = data.total_orders;
            document.getElementById('rep-completed').textContent = data.completed_orders;
            document.getElementById('rep-cancelled').textContent = data.cancelled_orders;
            document.getElementById('rep-avg').textContent = data.avg_order_value_formatted;

            // Top Menus Table
            const topMenuBody = document.getElementById('rep-top-menu-body');
            if (topMenuBody) {
                if (data.top_menus.length === 0) {
                    topMenuBody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:16px;color:var(--text-muted);">Tidak ada data penjualan di periode ini</td></tr>`;
                } else {
                    topMenuBody.innerHTML = data.top_menus.map((m, idx) => `
                        <tr>
                            <td><span style="font-weight:800;color:var(--brand-red);">#${idx + 1}</span> <strong>${m.name}</strong></td>
                            <td><span class="menu-category-tag" style="position:static;">${m.category}</span></td>
                            <td class="mono" style="font-weight:700;">${m.total_qty} porsi</td>
                            <td class="mono" style="color:var(--brand-gold);">${formatRupiah(m.total_rev)}</td>
                        </tr>
                    `).join('');
                }
            }

            // Orders Table
            const ordersBody = document.getElementById('rep-orders-body');
            if (ordersBody) {
                if (data.orders.length === 0) {
                    ordersBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:24px;color:var(--text-muted);">Tidak ada transaksi pada periode ini</td></tr>`;
                } else {
                    ordersBody.innerHTML = data.orders.map(o => `
                        <tr>
                            <td class="mono">#${o.order_id}</td>
                            <td>${o.customer_name}</td>
                            <td>${o.payment_method}</td>
                            <td class="mono" style="font-weight:700;color:var(--brand-gold);">${o.total_formatted}</td>
                            <td><span class="status-badge status-${o.status}">${o.status}</span></td>
                            <td style="color:var(--text-muted);font-size:0.8rem;">${o.order_date}</td>
                        </tr>
                    `).join('');
                }
            }

            if (resultsContainer) resultsContainer.style.display = 'block';

        } catch (err) {
            Swal.fire('Gagal', err.message, 'error');
        } finally {
            if (generateBtn) {
                generateBtn.disabled = false;
                generateBtn.innerHTML = '<ion-icon name="analytics-outline"></ion-icon> Tampilkan Laporan';
            }
        }
    }

    if (generateBtn) {
        generateBtn.addEventListener('click', generateReport);
    }

    // Export PDF
    if (pdfBtn) {
        pdfBtn.addEventListener('click', () => {
            const start = startDateInput.value;
            const end = endDateInput.value;
            window.location.href = `/admin/reports/export/pdf?start_date=${start}&end_date=${end}`;
        });
    }

    // Export Excel/CSV
    if (excelBtn) {
        excelBtn.addEventListener('click', () => {
            const start = startDateInput.value;
            const end = endDateInput.value;
            window.location.href = `/admin/reports/export/excel?start_date=${start}&end_date=${end}`;
        });
    }

    // Quick range presets
    document.querySelectorAll('.report-preset-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const preset = btn.dataset.preset;
            const todayStr = new Date().toISOString().split('T')[0];
            if (preset === 'today') {
                startDateInput.value = todayStr;
                endDateInput.value = todayStr;
            } else if (preset === '7days') {
                const d7 = new Date();
                d7.setDate(d7.getDate() - 7);
                startDateInput.value = d7.toISOString().split('T')[0];
                endDateInput.value = todayStr;
            } else if (preset === 'month') {
                startDateInput.value = firstDay;
                endDateInput.value = todayStr;
            }
            generateReport();
        });
    });

    generateReport();
}

/* ==========================================================================
   PROMOTIONS MODULE
   ========================================================================== */
function initPromotions() {
    const grid = document.getElementById('promotions-grid');
    const modal = document.getElementById('promo-modal');
    const form = document.getElementById('promo-form');
    const addBtn = document.getElementById('add-promo-btn');
    const modalTitle = document.getElementById('promo-modal-title');

    async function fetchPromotions() {
        if (!grid) return;
        grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-muted);"><div class="spinner"></div> Memuat voucher promo...</div>`;

        try {
            const res = await apiFetch('/api/promotions');
            if (!res.success) return;

            const { promotions } = res.data;
            if (promotions.length === 0) {
                grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:48px;color:var(--text-muted);">Belum ada voucher promo aktif. Buat promo pertama Anda!</div>`;
                return;
            }

            grid.innerHTML = promotions.map(p => `
                <div class="promo-card" data-id="${p.promo_id}">
                    <div class="promo-badge-wrap">
                        <div class="promo-code-pill">
                            <ion-icon name="ticket-outline"></ion-icon>
                            <span>${p.code}</span>
                        </div>
                        <label class="switch" title="Aktif / Nonaktifkan">
                            <input type="checkbox" class="promo-toggle-switch" data-id="${p.promo_id}" ${p.is_active ? 'checked' : ''}>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div>
                        <div style="font-size:1.35rem;font-weight:800;color:var(--brand-gold);margin-bottom:4px;">
                            Diskon ${p.discount_formatted}
                        </div>
                        <div style="font-size:0.85rem;color:var(--text-secondary);">${p.description}</div>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;background:var(--bg-input);padding:10px 14px;border-radius:var(--radius-md);font-size:0.78rem;">
                        <span style="color:var(--text-muted);">Min. Belanja:</span>
                        <strong class="mono">${p.min_order_formatted}</strong>
                    </div>

                    <div style="display:flex;justify-content:space-between;align-items:center;font-size:0.75rem;color:var(--text-muted);padding-top:10px;border-top:1px solid var(--border-subtle);">
                        <span>Berlaku s/d: <strong style="color:var(--text-primary);">${p.expires_at_formatted}</strong></span>
                        <div style="display:flex;gap:6px;">
                            <button class="btn btn-secondary btn-icon btn-edit-promo" data-promo='${JSON.stringify(p).replace(/'/g, "&apos;")}'>
                                <ion-icon name="create-outline"></ion-icon>
                            </button>
                            <button class="btn btn-danger btn-icon btn-delete-promo" data-id="${p.promo_id}" data-code="${p.code}">
                                <ion-icon name="trash-outline"></ion-icon>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');

            // Toggle active status
            document.querySelectorAll('.promo-toggle-switch').forEach(sw => {
                sw.addEventListener('change', async () => {
                    const id = sw.dataset.id;
                    try {
                        const toggleRes = await apiFetch(`/api/promotions/${id}/toggle`, { method: 'PATCH' });
                        if (toggleRes.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Status Diubah',
                                text: toggleRes.message,
                                timer: 1200,
                                showConfirmButton: false,
                                background: '#11151F',
                                color: '#F8FAFC'
                            });
                        }
                    } catch (e) {
                        sw.checked = !sw.checked;
                        Swal.fire('Gagal', e.message, 'error');
                    }
                });
            });

            // Edit
            document.querySelectorAll('.btn-edit-promo').forEach(btn => {
                btn.addEventListener('click', () => {
                    const p = JSON.parse(btn.dataset.promo);
                    openEditPromoModal(p);
                });
            });

            // Delete
            document.querySelectorAll('.btn-delete-promo').forEach(btn => {
                btn.addEventListener('click', () => {
                    confirmDeletePromo(btn.dataset.id, btn.dataset.code);
                });
            });

        } catch (e) {
            grid.innerHTML = `<div style="grid-column:1/-1;text-align:center;color:var(--color-danger);">Gagal memuat voucher promo.</div>`;
        }
    }

    function openEditPromoModal(p) {
        modalTitle.textContent = `Edit Promo — ${p.code}`;
        form.reset();
        document.getElementById('promo-id').value = p.promo_id;
        document.getElementById('promo-code').value = p.code;
        document.getElementById('promo-discount-type').value = p.discount_type;
        document.getElementById('promo-discount-value').value = p.discount_value;
        document.getElementById('promo-min-order').value = p.min_order;
        document.getElementById('promo-description').value = p.description === '-' ? '' : p.description;
        document.getElementById('promo-expires-at').value = p.expires_at || '';
        modal.classList.add('active');
    }

    function confirmDeletePromo(id, code) {
        Swal.fire({
            title: 'Hapus Promo?',
            text: `Apakah Anda yakin ingin menghapus voucher "${code}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#EF4444',
            confirmButtonText: 'Hapus!',
            cancelButtonText: 'Batal',
            background: '#11151F',
            color: '#F8FAFC'
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    const res = await apiFetch(`/api/promotions/${id}`, { method: 'DELETE' });
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Terhapus',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false,
                            background: '#11151F',
                            color: '#F8FAFC'
                        });
                        fetchPromotions();
                    }
                } catch (e) {
                    Swal.fire('Gagal', e.message, 'error');
                }
            }
        });
    }

    if (addBtn) {
        addBtn.addEventListener('click', () => {
            modalTitle.textContent = 'Buat Voucher Promo Baru';
            form.reset();
            document.getElementById('promo-id').value = '';
            modal.classList.add('active');
        });
    }

    document.querySelectorAll('.promo-modal-close').forEach(btn => {
        btn.addEventListener('click', () => modal.classList.remove('active'));
    });

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const id = document.getElementById('promo-id').value;
            const payload = {
                code: document.getElementById('promo-code').value,
                discount_type: document.getElementById('promo-discount-type').value,
                discount_value: document.getElementById('promo-discount-value').value,
                min_order: document.getElementById('promo-min-order').value || 0,
                description: document.getElementById('promo-description').value,
                expires_at: document.getElementById('promo-expires-at').value || null,
            };

            const url = id ? `/api/promotions/${id}` : '/api/promotions';
            const method = id ? 'PUT' : 'POST';

            try {
                const res = await apiFetch(url, {
                    method: method,
                    body: JSON.stringify(payload)
                });
                if (res.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false,
                        background: '#11151F',
                        color: '#F8FAFC'
                    });
                    modal.classList.remove('active');
                    fetchPromotions();
                }
            } catch (err) {
                Swal.fire('Error', err.message, 'error');
            }
        });
    }

    fetchPromotions();
}

/* ==========================================================================
   PAGINATION HELPER
   ========================================================================== */
function renderPagination(pagination, container, onPageClick) {
    if (!container || !pagination || pagination.last_page <= 1) {
        if (container) container.innerHTML = '';
        return;
    }

    const { current_page, last_page } = pagination;
    let html = `<div style="display:flex;gap:6px;align-items:center;justify-content:flex-end;margin-top:20px;">`;

    if (current_page > 1) {
        html += `<button class="btn btn-secondary btn-sm page-btn" data-page="${current_page - 1}">&laquo; Prev</button>`;
    }

    for (let i = 1; i <= last_page; i++) {
        if (i === 1 || i === last_page || (i >= current_page - 1 && i <= current_page + 1)) {
            const activeClass = i === current_page ? 'btn-primary' : 'btn-secondary';
            html += `<button class="btn ${activeClass} btn-sm page-btn" data-page="${i}">${i}</button>`;
        } else if (i === current_page - 2 || i === current_page + 2) {
            html += `<span style="padding:0 4px;color:var(--text-muted);">...</span>`;
        }
    }

    if (current_page < last_page) {
        html += `<button class="btn btn-secondary btn-sm page-btn" data-page="${current_page + 1}">Next &raquo;</button>`;
    }

    html += `</div>`;
    container.innerHTML = html;

    container.querySelectorAll('.page-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const page = parseInt(btn.dataset.page);
            onPageClick(page);
        });
    });
}
