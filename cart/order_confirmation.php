<?php
session_start();
// Halaman ini sebaiknya hanya bisa diakses setelah checkout,
// tapi kita tambahkan pengecekan login untuk keamanan dasar.
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login/login.php');
    exit;
}
require_once '../db_connect.php'; 

$order_id = $_SESSION['last_order_id'] ?? null;
$order = null;

if ($order_id) {
    $stmt = $conn->prepare("SELECT order_id, total_amount, payment_method FROM orders WHERE order_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $order_result = $stmt->get_result();
    if ($order_result->num_rows > 0) {
        $order = $order_result->fetch_assoc();
    }
    $stmt->close();
    // Hapus variabel sesi agar tidak digunakan lagi pada kunjungan berikutnya
    unset($_SESSION['last_order_id']);
}

// Jika tidak ada pesanan yang valid, arahkan kembali ke halaman utama
if (!$order) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil! - Cimohot</title>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&family=Roboto:wght@400&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
    <style>
        :root {
            --primary-orange: #ff6b35;
            --dark-orange: #e55a2e;
            --success-green: #2ed573;
            --light-gray: #f8f9fa;
            --text-dark: #2c3e50;
            --text-light: #6c757d;
            --white: #ffffff;
            --whatsapp-green: #25D366;
            --bank-blue: #0066cc;
            --warning-yellow: #ffc107;
            --border-light: #e9ecef;
            --ff-heading: 'Rubik', sans-serif;
            --ff-body: 'Roboto', sans-serif;
        }
        body { font-family: var(--ff-body); background-color: var(--light-gray); display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .confirmation-card { background: var(--white); padding: 50px 40px; border-radius: 20px; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1); max-width: 600px; width: 90%; text-align: center; transform: scale(0.95); opacity: 0; animation: fadeInScale 0.5s 0.2s forwards; }
        @keyframes fadeInScale { to { transform: scale(1); opacity: 1; } }
        .checkmark-container { width: 100px; height: 100px; margin: 0 auto 20px; }
        .checkmark-circle { stroke-dasharray: 166; stroke-dashoffset: 166; stroke-width: 3; stroke: var(--success-green); fill: none; animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards; }
        .checkmark { width: 100px; height: 100px; border-radius: 50%; display: block; stroke-width: 3; stroke: var(--white); stroke-miterlimit: 10; box-shadow: inset 0px 0px 0px var(--success-green); animation: scale .3s ease-in-out .9s both, fill .3s ease-in-out .6s both; }
        .checkmark-check { transform-origin: 50% 50%; stroke-dasharray: 48; stroke-dashoffset: 48; animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards; }
        @keyframes stroke { 100% { stroke-dashoffset: 0; } }
        @keyframes scale { 0%, 100% { transform: none; } 50% { transform: scale3d(1.1, 1.1, 1); } }
        @keyframes fill { 100% { box-shadow: inset 0px 0px 0px 50px var(--success-green); } }
        h1 { font-family: var(--ff-heading); font-size: 2.2rem; margin-bottom: 10px; color: var(--text-dark); }
        .order-id-text { font-size: 1.1rem; color: var(--text-light); margin-bottom: 25px; }
        .order-id-text strong { color: var(--primary-orange); }
        
        /* Payment Details Styling */
        .payment-details { background: linear-gradient(135deg, #fff8f5 0%, #ffeee6 100%); border: 2px solid var(--primary-orange); padding: 30px; border-radius: 16px; margin-bottom: 30px; text-align: left; box-shadow: 0 5px 15px rgba(255, 107, 53, 0.1); }
        .payment-details h3 { font-family: var(--ff-heading); font-size: 1.4rem; margin-bottom: 20px; color: var(--dark-orange); text-align: center; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .payment-details h3::before { content: "💳"; font-size: 1.2em; }
        
        .total-amount { background: var(--primary-orange); color: var(--white); padding: 15px 20px; border-radius: 12px; text-align: center; margin-bottom: 25px; font-size: 1.3rem; font-weight: 700; box-shadow: 0 3px 10px rgba(255, 107, 53, 0.3); }
        
        .bank-accounts { display: grid; gap: 15px; margin-bottom: 25px; }
        .bank-item { background: var(--white); border: 2px solid var(--border-light); border-radius: 12px; padding: 20px; transition: all 0.3s ease; cursor: pointer; position: relative; }
        .bank-item:hover { border-color: var(--bank-blue); box-shadow: 0 5px 15px rgba(0, 102, 204, 0.1); transform: translateY(-2px); }
        .bank-name { font-weight: 700; font-size: 1.1rem; color: var(--bank-blue); margin-bottom: 8px; display: flex; align-items: center; gap: 10px; }
        .bank-name::before { content: "🏦"; }
        .bank-account { font-size: 1.2rem; font-weight: 600; color: var(--text-dark); margin-bottom: 5px; font-family: monospace; letter-spacing: 1px; }
        .bank-holder { color: var(--text-light); font-size: 0.95rem; }
        
        .important-notice { background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); border: 2px solid var(--warning-yellow); border-radius: 12px; padding: 20px; margin-bottom: 25px; }
        .important-notice h4 { font-family: var(--ff-heading); font-size: 1.2rem; color: #856404; margin-bottom: 15px; display: flex; align-items: center; gap: 10px; }
        .important-notice h4::before { content: "⚠️"; }
        .important-notice p { color: #856404; margin-bottom: 10px; line-height: 1.6; }
        
        .whatsapp-section { background: linear-gradient(135deg, #e8f8f5 0%, #d1f2eb 100%); border: 2px solid var(--whatsapp-green); border-radius: 12px; padding: 25px; margin-bottom: 25px; text-align: center; }
        .whatsapp-section h4 { font-family: var(--ff-heading); font-size: 1.3rem; color: var(--whatsapp-green); margin-bottom: 15px; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .whatsapp-section h4::before { content: "📱"; }
        .whatsapp-btn { display: inline-block; background: var(--whatsapp-green); color: var(--white); text-decoration: none; padding: 15px 30px; border-radius: 50px; font-weight: 600; font-size: 1.1rem; transition: all 0.3s ease; margin-top: 15px; box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3); }
        .whatsapp-btn:hover { background: #128c7e; transform: translateY(-2px); box-shadow: 0 8px 25px rgba(37, 211, 102, 0.4); }
        .whatsapp-btn::before { content: "📲"; margin-right: 8px; }
        
        .format-message { background: var(--white); border: 2px dashed var(--whatsapp-green); border-radius: 8px; padding: 15px; margin-top: 15px; font-family: monospace; font-size: 0.95rem; color: var(--text-dark); position: relative; }
        .format-message::before { content: "💬"; position: absolute; top: -10px; left: 15px; background: var(--white); padding: 0 5px; }
        
        .btn-group { display: flex; flex-direction: column; gap: 15px; align-items: center; }
        .btn { text-decoration: none; width: 100%; text-align: center; padding: 15px 25px; border-radius: 8px; font-weight: 500; font-size: 1rem; transition: all 0.3s ease; }
        .btn-primary { background: var(--primary-orange); color: var(--white); }
        .btn-primary:hover { background: var(--dark-orange); transform: translateY(-2px); box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); }
        .btn-secondary { color: var(--text-light); font-weight: 400; text-decoration: underline; }
        .btn-secondary:hover { color: var(--text-dark); }
        
        .steps { text-align: left; margin-bottom: 20px; }
        .step { display: flex; align-items: flex-start; margin-bottom: 15px; }
        .step-number { background: var(--primary-orange); color: var(--white); width: 25px; height: 25px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; margin-right: 15px; flex-shrink: 0; }
        .step-content { flex: 1; }
        .step-title { font-weight: 600; color: var(--text-dark); margin-bottom: 5px; }
        .step-desc { color: var(--text-light); font-size: 0.9rem; line-height: 1.4; }
    </style>
</head>
<body>
    <div class="confirmation-card">
        <div class="checkmark-container">
            <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                <circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
                <path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
            </svg>
        </div>

        <h1>Pesanan Diterima!</h1>
        <p class="order-id-text">Pesanan Anda dengan ID <strong>#<?php echo htmlspecialchars($order['order_id']); ?></strong> sedang kami proses.</p>
        
        <?php if ($order['payment_method'] == 'cod'): ?>
            <div class="payment-details">
                <h3>Metode Pembayaran: Bayar di Tempat (COD)</h3>
                <p>Silakan siapkan uang tunai sejumlah <strong>Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></strong> untuk dibayarkan saat pesanan tiba atau diambil.</p>
            </div>
        <?php elseif ($order['payment_method'] == 'transfer'): ?>
            <div class="payment-details">
                <h3>Transfer Bank</h3>
                
                <div class="total-amount">
                    Total Pembayaran: Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                </div>
                
                <div class="bank-item">
                <div class="bank-name">Bank BCA</div>
                <div class="bank-account">1234567890</div>
                <div class="bank-holder">a/n Cimohot Indonesia</div>
            </div>

            <div class="bank-item">
                <div class="bank-name">Bank Mandiri</div>
                <div class="bank-account">0987654321</div>
                <div class="bank-holder">a/n Cimohot Indonesia</div>
            </div>
                
                <div class="important-notice">
                    <h4>Langkah Pembayaran</h4>
                    <div class="steps">
                        <div class="step">
                            <div class="step-number">1</div>
                            <div class="step-content">
                                <div class="step-title">Transfer ke salah satu rekening</div>
                                <div class="step-desc">Pilih bank yang mudah untuk Anda dan transfer sesuai jumlah total</div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <div class="step-content">
                                <div class="step-title">Screenshot bukti transfer</div>
                                <div class="step-desc">Ambil foto atau screenshot bukti transfer dari aplikasi banking</div>
                            </div>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <div class="step-content">
                                <div class="step-title">Kirim ke WhatsApp</div>
                                <div class="step-desc">Kirim bukti transfer ke nomor WhatsApp kami dengan format yang telah ditentukan</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="whatsapp-section">
                    <h4>Kirim Bukti Transfer</h4>
                    <p style="color: var(--text-dark); margin-bottom: 15px;">
                        Setelah transfer, segera kirimkan bukti pembayaran ke WhatsApp kami:
                    </p>
                    
                    <div class="format-message">
                        BUKTI TRANSFER - #<?php echo htmlspecialchars($order['order_id']); ?>
                    </div>
                    
                    <a href="https://wa.me/6285930194086?text=BUKTI%20TRANSFER%20-%20%23<?php echo htmlspecialchars($order['order_id']); ?>" 
                       target="_blank" 
                       class="whatsapp-btn">
                        Kirim ke WhatsApp
                    </a>
                    
                    <p style="color: var(--text-light); font-size: 0.9rem; margin-top: 15px;">
                        Nomor WhatsApp: <strong>0859-3019-4086</strong>
                    </p>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="btn-group">
            <a href="../akun.php#riwayat" class="btn btn-primary">Lihat Status Pesanan</a>
            <a href="../index.php" class="btn btn-secondary">Kembali ke Beranda</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            confetti({
                particleCount: 150,
                spread: 180,
                origin: { y: 0.6 }
            });
        });
        
        function copyAccount(event, accountNumber) {
    navigator.clipboard.writeText(accountNumber).then(function() {
        const btn = event.currentTarget;
        const originalText = btn.textContent;

        // Mengambil warna dari variabel CSS
        const rootStyles = getComputedStyle(document.documentElement);
        const successColor = rootStyles.getPropertyValue('--success-green').trim();
        const primaryColor = rootStyles.getPropertyValue('--primary-orange').trim();

        // Ubah tampilan tombol untuk feedback
        btn.textContent = 'Tersalin!';
        btn.style.backgroundColor = successColor;

        // Kembalikan tampilan tombol setelah 2 detik
        setTimeout(() => {
            btn.textContent = originalText;
            btn.style.backgroundColor = primaryColor;
        }, 2000);
    }, function(err) {
        console.error('Gagal menyalin teks: ', err);
        alert('Gagal menyalin nomor rekening.');
    });
}
    </script>
</body>
</html>