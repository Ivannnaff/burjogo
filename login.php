<?php
session_start();
include 'koneksi.php';

// Jika admin sudah login, langsung lempar ke halaman admin
if (isset($_SESSION['admin_logged'])) {
    header("Location: admin.php");
    exit();
}

// PROSES LOGIN SQL
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek ke tabel admin SQL
    $query = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) === 1) {
        $_SESSION['admin_logged'] = $username;
        header("Location: admin.php");
        exit();
    } else {
        $error = "Username atau Password salah, Aa!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BurjoGo - Login Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ===== RESET BASE ===== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        /* Background Full Oranye se-layar penuh */
        body {
            background: #ff7b00; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            min-height: 100vh;
            padding: 20px;
        }

        /* ===== CONTAINER UTAMA SPLIT SCREEN ===== */
        .login-container {
            display: flex;
            width: 950px;
            max-width: 100%;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
        }

        /* ===== KOLOM KIRI: HERO (LANGSUNG NYATU BACKGROUND) ===== */
        .login-hero {
            flex: 1;
            color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* Teks rata kiri biar estetik */
            padding: 20px;
            
        }

        .login-hero h1 {
            font-size: 2.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 15px;
            text-shadow: 0 4px 10px rgba(0,0,0,0.1);
            
        }

        .login-hero p {
            font-size: 1.1rem;
            opacity: 0.95;
            max-width: 380px;
            margin-bottom: 35px;
        }

        /* Gambar hidangan melayang */
        .login-hero img {
            width: 100%;
            max-width: 400px;
            filter: drop-shadow(0 20px 30px rgba(0,0,0,0.2));
            animation: floatingHero 3.5s ease-in-out infinite;
        }

        @keyframes floatingHero {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-15px); }
        }

        /* ===== KOLOM KANAN: KOTAK FORM PUTIH MELAYANG ===== */
        .login-box {
            background: #ffffff;
            width: 420px; 
            padding: 45px 35px;
            border-radius: 24px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .login-box h2 {
            margin-bottom: 5px; 
            color: #232323;
            font-weight: 700;
            font-size: 1.8rem;
        }

        .login-box .sub-title {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 25px;
        }

        /* ===== FORM INPUTS ===== */
        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            font-size: 0.9rem;
            color: #232323;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border-radius: 10px;
            border: 1px solid #ddd;
            font-size: 0.95rem;
            outline: none;
            transition: 0.2s ease;
        }

        .form-group input:focus {
            border-color: #ff7b00;
            box-shadow: 0 0 0 3px rgba(255, 123, 0, 0.15);
        }

        /* ===== TOMBOL SUBMIT ===== */
        .btn-submit {
            width: 100%; 
            padding: 12px;
            background: #ff7b00; 
            color: white;
            border: none;
            font-weight: bold;
            font-size: 1rem;
            border-radius: 10px;
            cursor: pointer;
            margin-top: 10px;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background: #e86f00;
        }

        .btn-submit:active {
            transform: scale(0.98);
        }

        /* ===== ERROR BOX ===== */
        .error-msg {
            background: #fdf2f2;
            color: #dc3545; 
            text-align: center; 
            padding: 10px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            font-weight: 500;
            border-left: 4px solid #dc3545;
        }

        .back-home {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 0.85rem;
            color: #666;
            text-decoration: none;
        }

        .back-home:hover {
            color: #ff7b00;
            text-decoration: underline;
        }

        /* ===== RESPONSIVE UNTUK HP ===== */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                gap: 30px;
                text-align: center;
            }
            .login-hero {
                align-items: center;
                padding: 10px;
            }
            .login-hero h1 {
                font-size: 2.2rem;
            }
            .login-hero img {
                max-width: 200px;
            }
            .login-box {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    
    <div class="login-hero">
        <h1>BurjoGo<br>Admin Portal</h1>
        <img src="img/burjogo_logo2.png" onerror="this.src='https://placehold.co/350x350?text=BurjoGo'">
    </div>

    <div class="login-box">
        <h2>Selamat Datang</h2>
        <p class="sub-title">Silakan login untuk masuk ke sistem manajemen.</p>
        
        <?php if (isset($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Masukkan username">
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Masukkan password">
            </div>

            <button type="submit" name="login" class="btn-submit">Masuk Sistem</button>
        </form>

        <a href="index.html" class="back-home">← Kembali ke Halaman Utama</a>
    </div>

</div>

</body>
</html>