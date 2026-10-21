<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pesanan Berhasil! - Cimohot</title>
<meta name="robots" content="noindex">
<link rel="icon" type="image/png" sizes="32x32" href="/assets/images/favicon-32x32.png?v=2">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
<style>
:root {
    --bg:#09090B;--surface:#131316;--card:#1A1A1F;--elevated:#212128;
    --border:rgba(255,255,255,0.07);--red:#FF3D2E;--red-glow:rgba(255,61,46,0.22);
    --gold:#F5A623;--green:#22C55E;--blue:#38BDF8;--wa:#25D366;
    --text:#FAFAFA;--muted:#9CA3AF;--dim:#6B7280;
    --r-sm:8px;--r-md:14px;--r-lg:20px;--r-xl:24px;--r-full:9999px;
    --t:all 0.22s cubic-bezier(0.4,0,0.2,1);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:"Plus Jakarta Sans",sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:24px 16px 60px;position:relative;overflow-x:hidden}
body::before{content:"";position:fixed;top:-10%;left:50%;transform:translateX(-50%);width:700px;height:400px;background:radial-gradient(ellipse,rgba(34,197,94,0.1) 0%,transparent 65%);z-index:0;pointer-events:none;filter:blur(60px)}

/* Header */
.conf-header{width:100%;max-width:660px;display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;z-index:1;position:relative}
.conf-logo{display:flex;align-items:center;gap:10px;text-decoration:none}
.conf-logo .lm{width:34px;height:34px;background:var(--red);border-radius:var(--r-sm);display:flex;align-items:center;justify-content:center;font-size:0.9rem;font-weight:800;color:#fff}
.conf-logo .lt{font-size:1.15rem;font-weight:800;color:#fff}
.conf-logo .lt span{color:var(--red)}
.home-btn{display:inline-flex;align-items:center;gap:7px;padding:8px 16px;border-radius:var(--r-full);font-size:0.83rem;font-weight:600;color:var(--muted);background:rgba(255,255,255,0.04);border:1px solid var(--border);text-decoration:none;transition:var(--t)}
.home-btn:hover{color:var(--text);background:rgba(255,255,255,0.08)}

/* Card */
.conf-card{width:100%;max-width:660px;background:var(--card);border:1px solid var(--border);border-radius:var(--r-xl);overflow:hidden;box-shadow:0 30px 70px rgba(0,0,0,0.5);position:relative;z-index:1;animation:fadeUp 0.5s ease forwards;opacity:0;transform:translateY(20px)}
@keyframes fadeUp{to{opacity:1;transform:translateY(0)}}

/* Success Header */
.success-head{text-align:center;padding:40px 32px 28px;background:linear-gradient(180deg,rgba(34,197,94,0.08) 0%,transparent 100%);border-bottom:1px solid var(--border);position:relative}
.check-wrap{width:80px;height:80px;margin:0 auto 20px;position:relative}
.check-bg{width:80px;height:80px;border-radius:50%;background:rgba(34,197,94,0.12);border:2px solid rgba(34,197,94,0.3);display:flex;align-items:center;justify-content:center;animation:popIn 0.5s 0.3s ease both}
@keyframes popIn{0%{transform:scale(0)}80%{transform:scale(1.12)}100%{transform:scale(1)}}
.check-icon{font-size:2.5rem;animation:fadeIn 0.3s 0.6s ease both;opacity:0}
@keyframes fadeIn{to{opacity:1}}
.success-title{font-size:1.8rem;font-weight:800;color:var(--text);margin-bottom:8px}
.order-num{display:inline-flex;align-items:center;gap:8px;padding:6px 16px;background:rgba(255,61,46,0.1);border:1px solid rgba(255,61,46,0.25);border-radius:var(--r-full);font-size:0.85rem;font-weight:700;color:var(--red);margin-bottom:14px}
.eta-badge{display:inline-flex;align-items:center;gap:7px;padding:7px 16px;background:rgba(245,166,35,0.1);border:1px solid rgba(245,166,35,0.25);border-radius:var(--r-full);font-size:0.82rem;font-weight:600;color:var(--gold)}

/* Section wrapper */
.section{padding:24px 28px;border-bottom:1px solid var(--border)}
.section:last-child{border-bottom:none}
.sec-title{display:flex;align-items:center;gap:9px;font-size:0.82rem;font-weight:800;color:var(--muted);text-transform:uppercase;letter-spacing:0.8px;margin-bottom:16px}
.sec-icon{font-size:1rem;color:var(--red)}

/* Order Items */
.order-item-row{display:flex;align-items:center;gap:14px;padding:10px 0;border-bottom:1px solid rgba(255,255,255,0.04)}
.order-item-row:last-of-type{border-bottom:none}
.oi-img{width:52px;height:52px;border-radius:var(--r-sm);object-fit:cover;border:1px solid var(--border);flex-shrink:0}
.oi-info{flex:1;min-width:0}
.oi-name{font-size:0.9rem;font-weight:700;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.oi-qty{font-size:0.78rem;color:var(--dim);margin-top:2px}
.oi-sub{font-size:0.88rem;font-weight:700;color:var(--gold);flex-shrink:0}

/* Price summary */
.price-row{display:flex;justify-content:space-between;align-items:center;font-size:0.88rem;color:var(--muted);margin-bottom:10px}
.price-row span:last-child{color:var(--text);font-weight:600}
.price-row.disc span:last-child{color:var(--green)}
.price-divider{border:none;border-top:1px solid var(--border);margin:12px 0}
.price-total{display:flex;justify-content:space-between;align-items:center;font-weight:800;font-size:1.1rem}
.total-val{color:var(--gold);font-size:1.2rem}

/* Delivery info */
.info-row{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:0.87rem}
.info-row:last-child{margin-bottom:0}
.info-icon{font-size:1rem;flex-shrink:0;margin-top:1px;color:var(--muted)}
.info-label{color:var(--dim);width:80px;flex-shrink:0}
.info-val{color:var(--text);font-weight:600;flex:1;word-break:break-word}

/* Bank info */
.bank-card{background:var(--elevated);border:1px solid var(--border);border-radius:var(--r-md);padding:18px 20px;margin-bottom:12px;cursor:pointer;transition:var(--t)}
.bank-card:hover{border-color:rgba(56,189,248,0.4);box-shadow:0 4px 20px rgba(56,189,248,0.1);transform:translateY(-1px)}
.bank-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:10px}
.bank-name{font-size:0.82rem;font-weight:800;color:var(--blue);text-transform:uppercase;letter-spacing:0.5px}
.copy-btn{display:inline-flex;align-items:center;gap:5px;padding:4px 12px;background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.25);color:var(--blue);border-radius:var(--r-full);font-size:0.72rem;font-weight:700;cursor:pointer;transition:var(--t)}
.copy-btn:hover{background:var(--blue);color:#fff}
.copy-btn.copied{background:rgba(34,197,94,0.15);border-color:var(--green);color:var(--green)}
.bank-account{font-size:1.35rem;font-weight:800;font-family:monospace;letter-spacing:2px;color:var(--text);margin-bottom:4px}
.bank-holder{font-size:0.8rem;color:var(--dim)}
.total-box{background:linear-gradient(135deg,rgba(255,61,46,0.1),rgba(245,166,35,0.08));border:1px solid rgba(255,61,46,0.25);border-radius:var(--r-md);padding:14px 20px;text-align:center;margin-bottom:20px;font-size:1.3rem;font-weight:800;color:var(--gold)}

/* Payment steps */
.pay-steps{display:flex;flex-direction:column;gap:12px;margin-bottom:0}
.pay-step{display:flex;align-items:flex-start;gap:12px}
.step-num{width:24px;height:24px;border-radius:50%;background:linear-gradient(135deg,var(--red),#FF6B35);color:#fff;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:800;flex-shrink:0}
.step-txt strong{display:block;font-size:0.88rem;font-weight:700;color:var(--text);margin-bottom:2px}
.step-txt span{font-size:0.8rem;color:var(--dim)}

/* WA Button */
.wa-section{background:rgba(37,211,102,0.06);border:1px solid rgba(37,211,102,0.2);border-radius:var(--r-md);padding:20px;text-align:center}
.wa-section h4{font-size:1rem;font-weight:800;color:var(--wa);margin-bottom:6px}
.wa-section p{font-size:0.83rem;color:var(--muted);margin-bottom:14px;line-height:1.5}
.wa-btn{display:inline-flex;align-items:center;gap:9px;padding:13px 28px;background:var(--wa);color:#fff;font-weight:800;font-size:0.95rem;border-radius:var(--r-full);text-decoration:none;transition:var(--t);box-shadow:0 6px 20px rgba(37,211,102,0.3)}
.wa-btn:hover{background:#1db954;transform:translateY(-2px);box-shadow:0 10px 28px rgba(37,211,102,0.45)}

/* COD info */
.cod-box{background:rgba(245,166,35,0.07);border:1px solid rgba(245,166,35,0.2);border-radius:var(--r-md);padding:18px 20px}
.cod-box h4{font-size:0.95rem;font-weight:800;color:var(--gold);margin-bottom:8px;display:flex;align-items:center;gap:8px}
.cod-box p{font-size:0.85rem;color:var(--muted);line-height:1.6}
.cod-amount{font-size:1.5rem;font-weight:800;color:var(--gold);display:block;margin:10px 0 4px}

/* CTA Buttons */
.cta-section{padding:24px 28px;display:flex;flex-direction:column;gap:10px}
.btn-track{display:flex;align-items:center;justify-content:center;gap:10px;padding:15px;background:linear-gradient(135deg,var(--red),#D62018);color:#fff;font-weight:800;font-size:0.98rem;border-radius:var(--r-lg);text-decoration:none;transition:var(--t);box-shadow:0 8px 25px rgba(255,61,46,0.3)}
.btn-track:hover{transform:translateY(-2px);box-shadow:0 12px 30px rgba(255,61,46,0.45)}
.btn-reorder{display:flex;align-items:center;justify-content:center;gap:10px;padding:13px;background:rgba(255,255,255,0.04);border:1px solid var(--border);color:var(--muted);font-weight:600;font-size:0.92rem;border-radius:var(--r-lg);text-decoration:none;transition:var(--t)}
.btn-reorder:hover{color:var(--text);background:rgba(255,255,255,0.07)}

@media(max-width:600px){.section{padding:20px 18px}.success-head{padding:32px 20px 22px}.success-title{font-size:1.5rem}.bank-account{font-size:1.15rem;letter-spacing:1px}.cta-section{padding:20px 18px}}
</style>
</head>
<body>

<div class="conf-header">
    <a href="/" class="conf-logo">
        <div class="lm">Ci</div>
        <div class="lt">Cimohot<span>.</span></div>
    </a>
    <a href="/" class="home-btn">&#x2190; Beranda</a>
</div>

<div class="conf-card">

    <!-- Success Header -->
    <div class="success-head">
        <div class="check-wrap">
            <div class="check-bg">
                <span class="check-icon">&#x2705;</span>
            </div>
        </div>
        <h1 class="success-title">Pesanan Masuk!</h1>
        <div class="order-num">&#x1F4CB; No. Pesanan #{{ $order["order_id"] }}</div>
        <br>
        <div class="eta-badge">
            &#x23F1;&#xFE0F; Estimasi diproses: {{ $isPickup ? "Siap dalam ~20 menit" : "~15-30 menit" }}
        </div>
    </div>

    <!-- Order Items -->
    <div class="section">
        <div class="sec-title">
            <span class="sec-icon">&#x1F6D2;</span> Item yang Dipesan
        </div>
        @foreach ($orderItems as $item)
        <div class="order-item-row">
            <img src="/{{ $item["image"] }}" alt="{{ $item["menu_name"] }}" class="oi-img" onerror="this.style.display='none'">
            <div class="oi-info">
                <div class="oi-name">{{ $item["menu_name"] }}</div>
                <div class="oi-qty">{{ $item["quantity"] }} x Rp {{ number_format($item["price"], 0, ",", ".") }}</div>
            </div>
            <div class="oi-sub">Rp {{ number_format($item["subtotal"], 0, ",", ".") }}</div>
        </div>
        @endforeach
        <div style="margin-top:16px">
            <div class="price-row"><span>Subtotal</span><span>Rp {{ number_format($subtotalRaw, 0, ",", ".") }}</span></div>
            @if (($order["discount_amount"] ?? 0) > 0)
            <div class="price-row disc"><span>Diskon ({{ $order["promo_code"] ?? "-" }})</span><span>-Rp {{ number_format($order["discount_amount"] ?? 0, 0, ",", ".") }}</span></div>
            @endif
            <div class="price-row"><span>Biaya Layanan</span><span>Gratis</span></div>
            <hr class="price-divider">
            <div class="price-total"><span>Total Bayar</span><span class="total-val">Rp {{ number_format($order["total_amount"], 0, ",", ".") }}</span></div>
        </div>
    </div>

    <!-- Delivery Info -->
    <div class="section">
        <div class="sec-title"><span class="sec-icon">&#x1F4CD;</span> Info Pengiriman</div>
        <div class="info-row"><span class="info-icon">&#x1F464;</span><span class="info-label">Pemesan</span><span class="info-val">{{ $order["customer_name"] }}</span></div>
        <div class="info-row"><span class="info-icon">&#x1F4DE;</span><span class="info-label">No. HP</span><span class="info-val">{{ $order["customer_phone"] ?? "-" }}</span></div>
        <div class="info-row"><span class="info-icon">{!! $isPickup ? "&#x1F3EA;" : "&#x1F3E0;" !!}</span><span class="info-label">{{ $isPickup ? "Metode" : "Alamat" }}</span><span class="info-val">{{ $isPickup ? "Ambil Sendiri di Toko" : ($order["delivery_address"] ?? "-") }}</span></div>
        @if (!empty($order["notes"]))
        <div class="info-row"><span class="info-icon">&#x1F4DD;</span><span class="info-label">Catatan</span><span class="info-val" style="color:var(--gold);font-style:italic">{{ $order["notes"] }}</span></div>
        @endif
    </div>

    <!-- Payment Section -->
    @if ($isTransfer)
    <div class="section">
        <div class="sec-title"><span class="sec-icon">&#x1F3E6;</span> Instruksi Transfer</div>
        <div class="total-box">Transfer Sebesar: Rp {{ number_format($order["total_amount"], 0, ",", ".") }}</div>
        <div class="bank-card" onclick="copyAcct(this,'1234567890')">
            <div class="bank-header">
                <div class="bank-name">&#x1F3E6; Bank BCA</div>
                <button class="copy-btn" onclick="event.stopPropagation();copyAcct(this,'1234567890')">&#x1F4CB; Salin</button>
            </div>
            <div class="bank-account">1234-5678-90</div>
            <div class="bank-holder">a/n Cimohot Indonesia</div>
        </div>
        <div class="bank-card" onclick="copyAcct(this,'0987654321')">
            <div class="bank-header">
                <div class="bank-name">&#x1F3E6; Bank Mandiri</div>
                <button class="copy-btn" onclick="event.stopPropagation();copyAcct(this,'0987654321')">&#x1F4CB; Salin</button>
            </div>
            <div class="bank-account">0987-6543-21</div>
            <div class="bank-holder">a/n Cimohot Indonesia</div>
        </div>
        <div class="pay-steps">
            <div class="pay-step"><div class="step-num">1</div><div class="step-txt"><strong>Transfer ke salah satu rekening di atas</strong><span>Sesuai total Rp {{ number_format($order["total_amount"], 0, ",", ".") }}</span></div></div>
            <div class="pay-step"><div class="step-num">2</div><div class="step-txt"><strong>Screenshot bukti transfer</strong><span>Dari aplikasi mobile banking atau ATM kamu</span></div></div>
            <div class="pay-step"><div class="step-num">3</div><div class="step-txt"><strong>Kirim ke WhatsApp kami</strong><span>Format sudah disiapkan, tinggal kirim!</span></div></div>
        </div>
    </div>
    <div class="section">
        <div class="wa-section">
            <h4>&#x1F4F2; Kirim Bukti Transfer</h4>
            <p>Pesan WA sudah diformat otomatis dengan detail pesananmu. Tinggal klik dan kirim!</p>
            <a href="{!! $waURL !!}" target="_blank" rel="noopener" class="wa-btn">
                &#x1F4AC; Kirim via WhatsApp
            </a>
        </div>
    </div>
    @else
    <div class="section">
        <div class="cod-box">
            <h4>&#x1F4B5; {{ $isPickup ? "Bayar di Kasir" : "Bayar di Tempat (COD)" }}</h4>
            <span class="cod-amount">Rp {{ number_format($order["total_amount"], 0, ",", ".") }}</span>
            <p>{{ $isPickup ? "Siapkan uang atau metode bayar saat datang ke toko ya! Tunjukkan nomor pesanan #" . $order["order_id"] . " ke kasir." : "Siapkan uang tunai sejumlah Rp " . number_format($order["total_amount"], 0, ",", ".") . " untuk dibayarkan saat pesanan tiba." }}</p>
        </div>
    </div>
    <div class="section">
        <div class="wa-section">
            <h4>&#x1F4F2; Konfirmasi via WhatsApp</h4>
            <p>Mau tanya status pesanan atau ada pertanyaan? Langsung chat kami aja!</p>
            <a href="{!! $waURL !!}" target="_blank" rel="noopener" class="wa-btn">
                &#x1F4AC; Chat WhatsApp
            </a>
        </div>
    </div>
    @endif

    <!-- CTA -->
    <div class="cta-section">
        <a href="/akun#riwayat" class="btn-track">&#x1F4E6; Lacak Status Pesanan</a>
        <a href="/#food-menu" class="btn-reorder">&#x1F37D;&#xFE0F; Pesan Lagi</a>
    </div>

</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Confetti
    setTimeout(() => {
        confetti({ particleCount: 120, spread: 160, origin: { y: 0.5 }, colors: ["#FF3D2E","#F5A623","#22C55E","#FAFAFA"] });
    }, 600);
});

function copyAcct(el, account) {
    const btn = el.classList.contains("copy-btn") ? el : el.querySelector(".copy-btn");
    navigator.clipboard.writeText(account).then(() => {
        btn.textContent = "✓ Tersalin!";
        btn.classList.add("copied");
        setTimeout(() => { btn.textContent = "Salin"; btn.classList.remove("copied"); }, 2500);
    }).catch(() => { alert("Nomor rekening: " + account); });
}
</script>
</body>
</html>
