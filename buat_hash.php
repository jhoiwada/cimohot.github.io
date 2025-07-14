<?php
// File: buat_hash.php
// Gunakan file ini untuk membuat hash password yang aman.

// ------------------------------------------------------------------
// UBAH TEKS DI DALAM TANDA KUTIP INI SESUAI PASSWORD YANG DIINGINKAN
$passwordSaya = 'user123';
// ------------------------------------------------------------------

// Proses hashing
$hashedPassword = password_hash($passwordSaya, PASSWORD_DEFAULT);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Generator Hash Password</title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 40px; background-color: #f4f4f9; color: #333; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 700px; margin: auto; }
        h1 { color: #e55a2e; }
        p { font-size: 1.1rem; }
        strong { color: #d63031; }
        textarea { width: 100%; padding: 10px; margin-top: 10px; font-family: monospace; font-size: 1rem; border: 1px solid #ccc; border-radius: 4px; }
        .note { margin-top: 20px; padding: 15px; background-color: #fffbe6; border-left: 4px solid #f59e0b; font-size: 0.9rem;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Generator Hash Password</h1>
        <p>Password Teks Biasa: <strong><?php echo htmlspecialchars($passwordSaya); ?></strong></p>
        
        <p><strong>Hash yang dihasilkan (salin semua teks di bawah ini):</strong></p>
        <textarea rows="4" readonly onclick="this.select();"><?php echo htmlspecialchars($hashedPassword); ?></textarea>

        <div class="note">
            <strong>Cara Menggunakan:</strong><br>
            1. Salin hash di atas.<br>
            2. Buka phpMyAdmin, edit baris pengguna yang diinginkan.<br>
            3. Tempel (paste) hash ini ke dalam kolom 'password'.<br>
            4. Klik 'Go' atau 'Save'.<br>
            5. Untuk membuat hash password lain (misal: 'user123'), ubah nilai variabel `$passwordSaya` di file ini, lalu refresh halaman browser.
        </div>
    </div>
</body>
</html>