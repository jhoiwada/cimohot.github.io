"use strict";

document.addEventListener("DOMContentLoaded", () => {
  // Validasi paling awal
  if (typeof API_URL === "undefined") {
    console.error(
      "FATAL ERROR: Variabel API_URL tidak ditemukan. Pastikan sudah ada di file PHP."
    );
    Swal.fire("Error Kritis", "Konfigurasi URL API tidak ditemukan.", "error");
    return;
  }

  // === 1. KUMPULKAN SEMUA ELEMEN ===

  const orderDetailsModal = document.getElementById("order-details-modal");
  const closeOrderModalBtn = document.getElementById("close-order-modal-btn");
  const cancelOrderModalBtn = document.getElementById("cancel-order-modal-btn");
  const allOrdersContainer = document.getElementById("all-orders-table");
  const navLinks = document.querySelectorAll(".sidebar-nav .nav-link");
  const pages = document.querySelectorAll(".main-content .page-content");
  const pageTitle = document.getElementById("page-title");
  const allCustomersContainer = document.getElementById("all-customers-table");
  const customerSearchInput = document.getElementById("customer-search-input");

  // Elemen untuk Modal Menu
  const menuModal = document.getElementById("menu-modal");
  const addMenuBtn = document.getElementById("add-menu-btn");
  const closeModalBtn = document.getElementById("close-modal-btn");
  const cancelBtn = document.getElementById("cancel-btn");
  const menuForm = document.getElementById("menu-form");
  const menuGridContainer = document.getElementById("menu-grid-container");

  let salesChartInstance = null;

  // === 2. SEMUA FUNGSI DIDEFINISIKAN DI SINI ===

  // Fungsi untuk mengambil data pelanggan
  async function fetchAllCustomers(searchTerm = "") {
    const container = document.getElementById("all-customers-table");
    if (container) container.innerHTML = "<p>Mencari pelanggan...</p>";

    let action = "get_all_customers";
    if (searchTerm) {
      action += `&search=${encodeURIComponent(searchTerm)}`;
    }

    const result = await fetchData(action);
    if (result && result.data && result.data.customers) {
      renderCustomersTable(result.data.customers);
    } else {
      container.innerHTML = "<p>Tidak ada pelanggan ditemukan.</p>";
    }
  }

  // Fungsi untuk menampilkan tabel pelanggan
  function renderCustomersTable(customers) {
    const tableContainer = document.getElementById("all-customers-table");
    let tableHTML = `
        <table class="data-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Telepon</th>
                    <th>Bergabung Sejak</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
           `;
    customers.forEach((customer) => {
      tableHTML += `
            <tr>
                <td>${customer.name}</td>
                <td>${customer.email}</td>
                <td>${customer.phone}</td>
                <td>${new Date(customer.created_at).toLocaleDateString(
                  "id-ID"
                )}</td>
                <td>
                    <button 
                        class="btn btn-secondary btn-sm view-history-btn" 
                        data-customer-id="${customer.user_id}" 
                        data-customer-name="${customer.name}">
                        Lihat Riwayat
                    </button>
                </td>
            </tr>
        `;
    });
    tableHTML += `</tbody></table>`;
    tableContainer.innerHTML = tableHTML;
  }

  const hideOrderDetailsModal = () => {
    orderDetailsModal.style.display = "none";
  };

  async function showOrderDetails(orderId) {
    document.getElementById(
      "order-details-title"
    ).textContent = `Detail Pesanan #${orderId}`;
    const contentDiv = document.getElementById("order-details-content");
    contentDiv.innerHTML = "<p>Memuat detail pesanan...</p>";
    orderDetailsModal.style.display = "flex";

    const result = await fetchData(`get_order_details&order_id=${orderId}`);

    if (result && result.data) {
      const { order_info, items } = result.data;

      let itemsHTML = items
        .map(
          (item) => `
            <tr>
                <td>${item.menu_name}</td>
                <td>${item.quantity}</td>
                <td>Rp ${parseFloat(item.price).toLocaleString("id-ID")}</td>
                <td>Rp ${parseFloat(item.subtotal).toLocaleString("id-ID")}</td>
            </tr>
        `
        )
        .join("");

      contentDiv.innerHTML = `
            <div class="order-customer-info">
                <p><strong>Pelanggan:</strong> ${order_info.customer_name}</p>
                <p><strong>Telepon:</strong> ${
                  order_info.customer_phone || "-"
                }</p>
                <p><strong>Alamat:</strong> ${
                  order_info.delivery_address || "-"
                }</p>
                <p><strong>Tanggal:</strong> ${new Date(
                  order_info.order_date
                ).toLocaleString("id-ID")}</p>
            </div>
            <h4>Item Pesanan:</h4>
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Menu</th>
                            <th>Jumlah</th>
                            <th>Harga</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHTML}
                    </tbody>
                </table>
            </div>
        `;
    } else {
      contentDiv.innerHTML = "<p>Gagal memuat detail. Coba lagi nanti.</p>";
    }
  }

  // Fungsi untuk navigasi halaman
  function showPage(pageId, context = {}) {
    // <--- Tambahkan context
    pages.forEach((page) => page.classList.remove("active"));
    const targetPage = document.getElementById(`${pageId}-page`);

    if (targetPage) {
      targetPage.classList.add("active");

      let title = document.querySelector(
        `.nav-link[data-page="${pageId}"] span`
      ).textContent;
      if (pageId === "orders" && context.userName) {
        title = `Pesanan oleh ${context.userName}`;
      }
      pageTitle.textContent = title;
    }

    navLinks.forEach((link) => link.classList.remove("active"));
    document
      .querySelector(`.nav-link[data-page="${pageId}"]`)
      ?.classList.add("active");

    loadPageData(pageId, context);
  }

  // Ubah fungsi loadPageData untuk menerima 'context'
  function loadPageData(pageId, context = {}) {
    switch (pageId) {
      case "dashboard":
        fetchDashboardData();
        break;
      case "orders":
        fetchAllOrders(context);
        break;
      case "menu":
        fetchAllMenus();
        break;
      case "customers":
        fetchAllCustomers();
        break;
    }
  }

  // Fungsi untuk memuat data sesuai halaman yang aktif
  function loadPageData(pageId, context = {}) {
    switch (pageId) {
      case "dashboard":
        fetchDashboardData();
        break;
      case "orders":
        fetchAllOrders(context);
        break;
      case "menu":
        fetchAllMenus();
        break;
      case "customers":
        fetchAllCustomers();
        break;
    }
  }

  // Fungsi dasar untuk fetch data ke API
  async function fetchData(action, options = {}) {
    const endpoint = `${API_URL}?action=${action}`;
    try {
      const response = await fetch(endpoint, options);
      if (!response.ok)
        throw new Error(`Network response error. Status: ${response.status}`);
      const result = await response.json();
      if (!result.success) throw new Error(result.message);
      return result;
    } catch (error) {
      console.error(`Error fetching ${action}:`, error);
      Swal.fire("Error", `Gagal memuat data: ${error.message}`, "error");
      return null;
    }
  }

  // --- Fungsi-fungsi untuk mengambil data ---
  async function fetchDashboardData() {
    const result = await fetchData("get_dashboard_stats");
    if (result && result.data) renderDashboard(result.data);
  }

  async function fetchAllOrders(filter = {}) {
    const container = document.getElementById("all-orders-table");
    if (container) container.innerHTML = "<p>Memuat pesanan...</p>";

    let action = "get_all_orders";
    if (filter.userId) {
      action += `&customer_id=${filter.userId}`;
    }
    const result = await fetchData(action);
    if (result && result.data && result.data.orders)
      renderOrdersTable(result.data.orders);
  }

  async function fetchAllMenus() {
    const container = document.getElementById("menu-grid-container");
    if (container) container.innerHTML = "<p>Memuat menu...</p>";
    const result = await fetchData("get_all_menus_admin");
    if (result && result.data && result.data.menu)
      renderMenuGrid(result.data.menu);
  }

  // --- Fungsi-fungsi untuk Aksi (Update/Tambah/Hapus) ---

  async function updateOrderStatus(orderId, newStatus) {
    const formData = new FormData();
    formData.append("action", "update_order_status");
    formData.append("order_id", orderId);
    formData.append("new_status", newStatus);

    try {
      const response = await fetch(API_URL, { method: "POST", body: formData });
      const result = await response.json();
      if (result.success) {
        Swal.fire(
          "Berhasil!",
          `Status pesanan #${orderId} berhasil diubah.`,
          "success"
        );
        fetchAllOrders();
        fetchDashboardData();
      } else {
        throw new Error(result.message);
      }
    } catch (error) {
      Swal.fire("Error", `Gagal mengupdate status: ${error.message}`, "error");
    }
  }

  async function handleMenuFormSubmit(event) {
    event.preventDefault();
    const saveBtn = document.getElementById("save-menu-btn");
    saveBtn.disabled = true;
    saveBtn.textContent = "Menyimpan...";

    const formData = new FormData(menuForm);
    const menuId = document.getElementById("menu-id").value;

    const action = menuId ? "update_menu" : "add_menu";
    formData.append("action", action);

    try {
      const response = await fetch(API_URL, { method: "POST", body: formData });
      const result = await response.json();
      if (result.success) {
        Swal.fire("Berhasil!", result.message, "success");
        hideModal();
        fetchAllMenus();
      } else {
        throw new Error(result.message);
      }
    } catch (error) {
      Swal.fire("Gagal!", error.message, "error");
    } finally {
      saveBtn.disabled = false;
      saveBtn.textContent = "Simpan";
    }
  }

  function handleDeleteMenu(menuId) {
    Swal.fire({
      title: "Anda yakin?",
      text: "Menu yang dihapus tidak dapat dikembalikan!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#d33",
      confirmButtonText: "Ya, hapus!",
      cancelButtonText: "Batal",
    }).then(async (result) => {
      if (result.isConfirmed) {
        const formData = new FormData();
        formData.append("action", "delete_menu");
        formData.append("menu_id", menuId);
        try {
          const response = await fetch(API_URL, {
            method: "POST",
            body: formData,
          });
          const res = await response.json();
          if (res.success) {
            Swal.fire("Dihapus!", res.message, "success");
            fetchAllMenus();
          } else {
            throw new Error(res.message);
          }
        } catch (error) {
          Swal.fire("Gagal!", error.message, "error");
        }
      }
    });
  }

  // --- Fungsi-fungsi untuk merender HTML (Tampilan) ---

  function renderDashboard(data) {
    document.getElementById("stat-revenue").textContent = `Rp ${parseFloat(
      data.revenue || 0
    ).toLocaleString("id-ID")}`;
    document.getElementById("stat-orders").textContent = data.orders_today || 0;
    document.getElementById("stat-customers").textContent =
      data.new_customers || 0;
    document.getElementById("stat-top-product").textContent =
      data.top_product || "-";
    const ordersNotification = document.getElementById("orders-notification");
    if (data.pending_orders > 0) {
      ordersNotification.textContent = data.pending_orders;
      ordersNotification.style.display = "flex";
    } else {
      ordersNotification.style.display = "none";
    }
    renderSalesChart(data.sales_chart);
    renderRecentOrders(data.recent_orders);
  }

  function renderSalesChart(chartData) {
    const canvas = document.getElementById("salesChart");
    if (!chartData || !canvas) return;
    const ctx = canvas.getContext("2d");
    if (salesChartInstance) {
      salesChartInstance.destroy();
    }
    const gradient = ctx.createLinearGradient(0, 0, 0, 320);
    gradient.addColorStop(0, "rgba(255, 61, 46, 0.4)");
    gradient.addColorStop(1, "rgba(255, 61, 46, 0.0)");

    salesChartInstance = new Chart(ctx, {
      type: "line",
      data: {
        labels: chartData.labels,
        datasets: [
          {
            label: "Pendapatan (Rp)",
            data: chartData.values,
            backgroundColor: gradient,
            borderColor: "#FF3D2E",
            borderWidth: 3,
            pointBackgroundColor: "#FF6B35",
            pointBorderColor: "#FFFFFF",
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 7,
            tension: 0.4,
            fill: true,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            labels: { color: "#94A3B8", font: { family: "Plus Jakarta Sans", weight: "600" } },
          },
          tooltip: {
            backgroundColor: "#151822",
            titleColor: "#FFFFFF",
            bodyColor: "#F5A623",
            borderColor: "rgba(255, 255, 255, 0.1)",
            borderWidth: 1,
            padding: 12,
            displayColors: false,
            callbacks: {
              label: (context) => "Rp " + parseFloat(context.parsed.y).toLocaleString("id-ID")
            }
          }
        },
        scales: {
          x: {
            grid: { color: "rgba(255, 255, 255, 0.05)" },
            ticks: { color: "#94A3B8", font: { family: "Plus Jakarta Sans" } },
          },
          y: {
            beginAtZero: true,
            grid: { color: "rgba(255, 255, 255, 0.05)" },
            ticks: {
              color: "#94A3B8",
              font: { family: "Plus Jakarta Sans" },
              callback: (val) => "Rp " + (val >= 1000 ? (val / 1000).toLocaleString("id-ID") + "k" : val),
            },
          },
        },
      },
    });
  }

  function renderRecentOrders(orders) {
    const container = document.getElementById("recent-orders-list");
    if (!orders || orders.length === 0) {
      container.innerHTML = "<p>Belum ada pesanan terbaru.</p>";
      return;
    }
    container.innerHTML = `<table class="data-table"><thead><tr><th>ID</th><th>Pelanggan</th><th>Total</th><th>Status</th></tr></thead><tbody>
        ${orders
          .map(
            (order) =>
              `<tr><td>#${order.order_id}</td><td>${
                order.customer_name
              }</td><td>Rp ${parseFloat(order.total_amount).toLocaleString(
                "id-ID"
              )}</td><td><span class="status-badge status-${order.status.toLowerCase()}">${
                order.status
              }</span></td></tr>`
          )
          .join("")}</tbody></table>`;
  }

  function renderOrdersTable(orders) {
    const container = document.getElementById("all-orders-table");
    if (!orders || orders.length === 0) {
      container.innerHTML = "<p>Tidak ada pesanan ditemukan.</p>";
      return;
    }

    const createActionButtons = (order) => {
      const id = order.order_id;
      switch (order.status) {
        case "pending":
          return `
            <button class="btn btn-proses btn-sm status-action-btn" data-id="${id}" data-status="diproses">Proses</button>
            <button class="btn btn-danger btn-sm status-action-btn" data-id="${id}" data-status="dibatalkan">Batal</button>
          `;
        case "diproses":
          return `<button class="btn btn-selesai btn-sm status-action-btn" data-id="${id}" data-status="selesai">Selesai</button>`;
        case "selesai":
          return `<span class="status-badge status-selesai">Selesai</span>`;
        case "dibatalkan":
          return `<span class="status-badge status-dibatalkan">Dibatalkan</span>`;
        default:
          return `<span class="status-badge">${order.status}</span>`;
      }
    };

    container.innerHTML = `<table class="data-table"><thead><tr><th>ID</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
        ${orders
          .map(
            (order) =>
              `<tr>
                <td>#${order.order_id}</td>
                <td>${order.customer_name}</td>
                <td>${new Date(order.order_date).toLocaleDateString("id-ID", {
                  day: "2-digit",
                  month: "short",
                  year: "numeric",
                })}</td>
                <td>Rp ${parseFloat(order.total_amount).toLocaleString(
                  "id-ID"
                )}</td>
                <td>${createActionButtons(order)}</td>
                <td>
                  <button class="btn btn-secondary btn-sm view-details-btn" data-id="${
                    order.order_id
                  }">
                    Lihat Detail
                  </button>
                </td>
            </tr>`
          )
          .join("")}</tbody></table>`;
  }
  function renderMenuGrid(menus) {
    const container = document.getElementById("menu-grid-container");
    if (!menus || menus.length === 0) {
      container.innerHTML = "<p>Belum ada menu.</p>";
      return;
    }
    container.innerHTML = menus
      .map((menu) => {
        const stockStyle =
          menu.stock <= 5
            ? 'style="color: var(--danger-color); font-weight: 600;"'
            : "";

        // Menambahkan timestamp ke URL gambar untuk cache busting
        const imageSrc = `../${menu.image}?t=${new Date().getTime()}`;

        return `
        <div class="menu-card" data-id="${menu.id}">
            <img src="${imageSrc}" alt="${
          menu.name
        }" onerror="this.onerror=null; this.src='../assets/images/default-food.png';">
            <h4>${menu.name}</h4>
            <p class="price">Rp ${parseFloat(menu.price).toLocaleString(
              "id-ID"
            )}</p>
            <p class="category">${menu.category}</p>
            <p class="stock" ${stockStyle}>Stok: ${menu.stock}</p>
            <div class="menu-actions">
                <button class="btn btn-secondary btn-sm edit-btn" data-id="${
                  menu.id
                }" data-menu='${JSON.stringify(menu)}'>Edit</button>
                <button class="btn btn-danger btn-sm delete-btn" data-id="${
                  menu.id
                }">Hapus</button>
            </div>
        </div>`;
      })
      .join("");
  }

  const hideModal = () => {
    menuModal.style.display = "none";
  };

  // === 3. SEMUA EVENT LISTENER DITAMBAHKAN DI SINI ===

  // Event listener untuk pencarian pelanggan
  customerSearchInput.addEventListener("input", (e) => {
    fetchAllCustomers(e.target.value);
  });

  // Event listener untuk tombol "Lihat Riwayat" (menggunakan event delegation)
  document.body.addEventListener("click", (e) => {
    if (e.target.matches(".view-history-btn")) {
      const userId = e.target.dataset.customerId;
      const userName = e.target.dataset.customerName;
      showPage("orders", { userId: userId, userName: userName });
    }
  });

  const generateReportBtn = document.getElementById("generate-report-btn");

  if (generateReportBtn) {
    generateReportBtn.addEventListener("click", async () => {
      const startDate = document.getElementById("start-date").value;
      const endDate = document.getElementById("end-date").value;
      const resultsContainer = document.getElementById("report-results");

      if (!startDate || !endDate) {
        Swal.fire(
          "Input Kurang",
          "Harap pilih tanggal mulai dan tanggal akhir.",
          "warning"
        );
        return;
      }

      resultsContainer.innerHTML = `<p>Menghasilkan laporan...</p>`;

      const formData = new FormData();
      formData.append("action", "generate_sales_report");
      formData.append("start_date", startDate);
      formData.append("end_date", endDate);

      try {
        const response = await fetch(API_URL, {
          method: "POST",
          body: formData,
        });
        const result = await response.json();

        if (result.success) {
          const data = result.data;
          const revenue = parseFloat(data.total_revenue || 0);
          const avg = parseFloat(data.average_transaction || 0);

          resultsContainer.innerHTML = `
                    <h4>Hasil Laporan untuk ${startDate} s/d ${endDate}</h4>
                    <div class="report-summary">
                        <div class="summary-item">
                            <p>Total Pendapatan</p>
                            <h3>Rp ${revenue.toLocaleString("id-ID")}</h3>
                        </div>
                        <div class="summary-item">
                            <p>Total Pesanan Selesai</p>
                            <h3>${data.total_orders || 0}</h3>
                        </div>
                        <div class="summary-item">
                            <p>Rata-rata Transaksi</p>
                            <h3>Rp ${avg.toLocaleString("id-ID")}</h3>
                        </div>
                    </div>
                `;
        } else {
          throw new Error(result.message);
        }
      } catch (error) {
        resultsContainer.innerHTML = `<p style="color: var(--danger-color);">Gagal membuat laporan: ${error.message}</p>`;
      }
    });
  }

  // Event listener untuk modal detail pesanan
  closeOrderModalBtn.addEventListener("click", hideOrderDetailsModal);
  cancelOrderModalBtn.addEventListener("click", hideOrderDetailsModal);

  // Event delegation untuk tombol "Lihat Detail"
  allOrdersContainer.addEventListener("click", (e) => {
    const viewBtn = e.target.closest(".view-details-btn");
    if (viewBtn) {
      const orderId = viewBtn.dataset.id;
      showOrderDetails(orderId);
    }
  });

  navLinks.forEach((link) => {
    link.addEventListener("click", (e) => {
      if (link.classList.contains("logout-btn")) return;
      e.preventDefault();
      const page = link.dataset.page;
      if (page) showPage(page);
    });
  });

  document.body.addEventListener("click", function (e) {
    if (e.target.matches(".status-action-btn")) {
      const orderId = e.target.dataset.id;
      const newStatus = e.target.dataset.status;

      // Konfirmasi untuk aksi pembatalan
      if (newStatus === "dibatalkan") {
        Swal.fire({
          title: "Anda yakin?",
          text: `Akan membatalkan pesanan #${orderId}. Aksi ini tidak bisa diurungkan.`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonColor: "#d33",
          cancelButtonText: "Tidak",
          confirmButtonText: "Ya, batalkan!",
        }).then((result) => {
          if (result.isConfirmed) {
            updateOrderStatus(orderId, newStatus);
          }
        });
      } else {
        updateOrderStatus(orderId, newStatus);
      }
    }
  });

  addMenuBtn.addEventListener("click", () => {
    menuForm.reset();
    document.getElementById("modal-title").textContent = "Tambah Menu Baru";
    document.getElementById("menu-id").value = "";
    document.getElementById("image-hint").style.display = "none";
    menuModal.style.display = "flex";
  });

  closeModalBtn.addEventListener("click", hideModal);
  cancelBtn.addEventListener("click", hideModal);
  menuForm.addEventListener("submit", handleMenuFormSubmit);

  menuGridContainer.addEventListener("click", (e) => {
    const editBtn = e.target.closest(".edit-btn");
    if (editBtn) {
      const menuData = JSON.parse(editBtn.dataset.menu);
      document.getElementById("modal-title").textContent = "Edit Menu";
      document.getElementById("menu-id").value = menuData.id;
      document.getElementById("menu-name").value = menuData.name;
      document.getElementById("menu-description").value = menuData.description;
      document.getElementById("menu-price").value = menuData.price;
      document.getElementById("menu-category").value = menuData.category;
      document.getElementById("menu-stock").value = menuData.stock;
      document.getElementById("image-hint").style.display = "block";
      menuModal.style.display = "flex";
    }
    const deleteBtn = e.target.closest(".delete-btn");
    if (deleteBtn) {
      handleDeleteMenu(deleteBtn.dataset.id);
    }
  });

  // === 4. INISIALISASI HALAMAN ===
  // === FITUR BARU: LOGIKA SIDEBAR MOBILE ===
  const sidebar = document.querySelector(".sidebar");
  const sidebarToggleBtn = document.getElementById("sidebar-toggle");

  if (sidebarToggleBtn && sidebar) {
    sidebarToggleBtn.addEventListener("click", () => {
      sidebar.classList.toggle("active");
    });

    // Menutup sidebar saat link navigasi di dalam sidebar di-klik (pada mode mobile)
    document.querySelectorAll(".sidebar-nav .nav-link").forEach((link) => {
      link.addEventListener("click", () => {
        // Cek apakah layar dalam mode mobile (lebar di bawah 992px sesuai CSS)
        if (window.innerWidth <= 992) {
          sidebar.classList.remove("active");
        }
      });
    });
  }
  showPage("dashboard");
});
