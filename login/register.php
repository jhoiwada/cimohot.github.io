<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun - Cimohot</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #ff6b35;
            --primary-dark: #e55a2b;
            --text-dark: #2c3e50;
            --text-light: #7f8c8d;
            --white: #ffffff;
            --gainsboro: #e0e6ed;
            --shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Rubik', sans-serif;
            background: linear-gradient(135deg, rgb(148, 17, 17) 0%, rgb(161, 26, 26) 50%, rgb(163, 38, 38) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-card {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
            background: var(--white);
            border-radius: 24px;
            box-shadow: var(--shadow);
            overflow: hidden;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .logo-section {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            color: var(--white);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px;
            text-align: center;
        }

        .logo h1 {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 0 3px 15px rgba(0, 0, 0, 0.2);
        }

        .logo h1 span {
            color: #fffde4;
        }

        .logo p {
            font-size: 1.1rem;
            opacity: 0.9;
            margin-top: 5px;
        }

        .login-form-section {
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .login-form-section h2 {
            font-size: 2rem;
            color: var(--text-dark);
            margin-bottom: 25px;
            font-weight: 700;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-dark);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group input {
            width: 100%;
            padding: 15px 18px 15px 48px;
            border: 2px solid var(--gainsboro);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(255,107,53,0.15);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--text-light);
        }

        .input-group input:focus + .input-icon {
            color: var(--primary-color);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .login-btn:hover:not(:disabled) {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
        }

        .login-btn:disabled {
            background-color: #ccc;
            cursor: wait;
        }

        .register-section {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid var(--gainsboro);
            font-size: 0.95rem;
            color: var(--text-light);
        }

        .register-section a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .login-card {
                grid-template-columns: 1fr;
            }

            .logo-section {
                order: 2;
                padding: 30px;
            }

            .login-form-section {
                order: 1;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-form-section">
            <h2>Buat Akun Baru</h2>
            <form id="registerForm" novalidate>
                <div class="form-group">
                    <label for="name">Nama Lengkap</label>
                    <div class="input-group">
                        <ion-icon name="person-outline" class="input-icon"></ion-icon>
                        <input type="text" id="name" name="name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <div class="input-group">
                        <ion-icon name="mail-outline" class="input-icon"></ion-icon>
                        <input type="email" id="email" name="email" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <input type="password" id="password" name="password" placeholder="Minimal 6 karakter" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Konfirmasi Password</label>
                    <div class="input-group">
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Ketik ulang password" required>
                    </div>
                </div>
                <button type="submit" class="login-btn" id="registerBtn">
                    <span>Daftar</span>
                    <ion-icon name="person-add-outline"></ion-icon>
                </button>
            </form>
            <div class="register-section">
                <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </div>
        <div class="logo-section">
            <div class="logo">
                <a href="../index.php" style="text-decoration:none;color:inherit;">
                    <h1>Cimohot<span>.</span></h1>
                </a>
                <p>Daftar untuk mulai memesan!</p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password.length < 6) {
                Swal.fire('Gagal!', 'Password minimal harus 6 karakter.', 'error');
                return;
            }

            if (password !== confirmPassword) {
                Swal.fire('Gagal!', 'Konfirmasi password tidak cocok.', 'error');
                return;
            }

            const registerBtn = document.getElementById('registerBtn');
            registerBtn.disabled = true;
            registerBtn.innerHTML = `<span>Mendaftar...</span>`;

            const formData = new FormData(e.target);
            formData.append('action', 'register');

            try {
                const response = await fetch('../api.php', {
                    method: 'POST',
                    body: new URLSearchParams(formData)
                });

                const result = await response.json();

                if (result.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Pendaftaran berhasil, silakan login.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    window.location.href = 'login.php';
                } else {
                    Swal.fire('Gagal!', result.message, 'error');
                }
            } catch (error) {
                Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
            } finally {
                registerBtn.disabled = false;
                registerBtn.innerHTML = `<span>Daftar</span><ion-icon name="person-add-outline"></ion-icon>`;
            }
        });
    </script>
</body>
</html>
