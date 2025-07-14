<?php 
session_start(); 
// Jika sudah login, langsung arahkan ke halaman utama
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
    <title>Login - Cimohot</title>

    <!-- CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
    <!-- Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- CSS -->
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
            grid-template-columns: 1fr 1.2fr;
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
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
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
            margin-bottom: 8px;
            font-weight: 700;
        }

        .login-subtitle {
            color: var(--text-light);
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .form-group {
            margin-bottom: 22px;
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

        .input-group input,
        .input-group select {
            width: 100%;
            padding: 15px 48px;
            border: 2px solid var(--gainsboro);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .input-group input:focus,
        .input-group select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.15);
            outline: none;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            color: var(--text-light);
            z-index: 2;
        }

        .input-group input:focus + .input-icon,
        .input-group select:focus + .input-icon {
            color: var(--primary-color);
        }

        select {
            appearance: none;
            background-image: url('data:image/svg+xml;charset=UTF-8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="%237f8c8d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m6 9 6 6 6-6"/></svg>');
            background-repeat: no-repeat;
            background-position: right 15px center;
            background-size: 1em;
            padding-left: 48px;
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            cursor: pointer;
            color: var(--text-light);
            z-index: 2;
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
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <!-- Logo Section -->
        <div class="logo-section">
            <div class="logo">
                <a href="../index.php" style="text-decoration:none;color:inherit;">
                    <h1>Cimohot<span>.</span></h1>
                </a>
                <p>Pedasnya Nampol!</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="login-form-section">
            <h2>Selamat Datang Kembali</h2>
            <p class="login-subtitle">Silakan login untuk melanjutkan</p>

            <form id="loginForm">
                <div class="form-group">
                    <label for="email_or_phone">Email atau Nomor Telepon</label>
                    <div class="input-group">
                        <ion-icon name="person-circle-outline" class="input-icon"></ion-icon>
                        <input type="text" id="email_or_phone" name="email_or_phone" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <ion-icon name="lock-closed-outline" class="input-icon"></ion-icon>
                        <input type="password" id="password" name="password" required>
                        <ion-icon name="eye-outline" class="toggle-password" id="togglePasswordIcon"></ion-icon>
                    </div>
                </div>

                <div class="form-group">
                    <label for="role">Login sebagai:</label>
                    <div class="input-group">
                        <ion-icon name="people-outline" class="input-icon"></ion-icon>
                        <select id="role" name="role">
                            <option value="user">Pengguna</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="login-btn">
                    <span>Login</span>
                    <ion-icon name="log-in-outline"></ion-icon>
                </button>
            </form>

            <div class="register-section">
                <p>Belum punya akun? <a href="register.php">Daftar sekarang</a></p>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePasswordIcon = document.getElementById('togglePasswordIcon');
            const passwordInput = document.getElementById('password');

            if (togglePasswordIcon && passwordInput) {
                togglePasswordIcon.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    this.setAttribute('name', isPassword ? 'eye-off-outline' : 'eye-outline');
                });
            }

            document.getElementById('loginForm').addEventListener('submit', async (e) => {
                e.preventDefault();

                const loginBtn = e.target.querySelector('.login-btn');
                const originalContent = loginBtn.innerHTML;

                loginBtn.disabled = true;
                loginBtn.innerHTML = `<span>Memproses...</span>`;

                const formData = new FormData(e.target);
                formData.append('action', 'login');

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
                            text: 'Login berhasil, Anda akan diarahkan...',
                            timer: 1500,
                            showConfirmButton: false
                        });

                        window.location.href = result.data.user.role === 'admin'
                            ? '../admin/admin-dashboard.php'
                            : '../index.php';
                    } else {
                        Swal.fire('Gagal!', result.message, 'error');
                    }
                } catch (error) {
                    Swal.fire('Error!', 'Terjadi kesalahan sistem.', 'error');
                } finally {
                    loginBtn.disabled = false;
                    loginBtn.innerHTML = originalContent;
                }
            });
        });
    </script>
</body>
</html>
