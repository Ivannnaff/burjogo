<?php
session_start();

// PROTEKSI: Jika admin belum login melalui SQL, lempar kembali ke halaman login.php
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit();
}

include 'koneksi.php';

// Logika mendeteksi jika tombol Selesai diklik untuk mengupdate status di SQL
if (isset($_GET['selesai_id'])) {
    $id_target = mysqli_real_escape_string($conn, $_GET['selesai_id']);
    
    mysqli_query($conn, "UPDATE pesanan SET status='Selesai' WHERE id_pesanan='$id_target'");
    
    header("Location: pesanan.php");
    exit();
}

// Ambil semua data dari tabel pesanan SQL (Urutkan dari yang paling baru di atas)
$result = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY waktu_order DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BurjoGo Admin - Pesanan </title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="admin-container">
    
    <div class="sidebar">
        <h2>BurjoGo Admin</h2>
        <p style="text-align:center; color:#fff; font-size:0.85rem; margin-bottom:20px; background: #444; padding: 5px; border-radius: 5px;">
            User Active: <strong><?php echo htmlspecialchars($_SESSION['admin_logged']); ?></strong>
        </p>
        <ul>
            <li><a href="admin.php">Kelola Menu </a></li>
            <li><a href="pesanan.php" class="active">Pesanan Masuk </a></li>
            <li><a href="index.html" target="_blank">Lihat Website</a></li>
            <li><a href="logout.php" style="background: #dc3545; margin-top: 30px; text-align: center; font-weight: bold;">Logout</a></li>
        </ul>
    </div>

    <div class="admin-content">
        <h1 class="page-title" style="text-align: left; margin-bottom: 20px;">Daftar Pesanan Pelanggan </h1>

        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID / Waktu</th>
                    <th>Nama & Meja</th>
                    <th>Detail Menu</th>
                    <th>Total Bayar</th>
                    <th>Catatan</th>
                    <th>Status</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) === 0): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #888; padding: 20px;">Belum ada data pesanan masuk </td>
                    </tr>
                <?php else: ?>
                    
                    <?php while ($ord = mysqli_fetch_assoc($result)): ?>
                        <tr style="<?php echo $ord['status'] === 'Selesai' ? 'background: #f0fff0; opacity: 0.7;' : ''; ?>">
                            
                            <td>
                                <strong><?php echo $ord['id_pesanan']; ?></strong><br>
                                <span style="font-size: 0.8rem; color:#888;"><?php echo $ord['waktu_order']; ?></span>
                            </td>
                            
                            <td>
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($ord['nama_pelanggan']); ?></span><br>
                                <mark style="background: #ffe4c5; padding: 2px 5px; border-radius: 4px; font-size: 0.8rem;"><?php echo $ord['no_meja']; ?></mark>
                            </td>
                            
                            <td>
                                <ul style="padding-left: 15px; margin: 0; font-size: 0.9rem;">
                                    <?php 
                                    // Mengubah kembali teks string JSON belanjaan dari SQL menjadi array PHP
                                    $items = json_decode($ord['detail_items'], true);
                                    
                                    if (is_array($items)) {
                                        foreach ($items as $item) {
                                            echo "<li>" . htmlspecialchars($item['nama']) . " <strong>(x" . $item['qty'] . ")</strong></li>";
                                        }
                                    }
                                    ?>
                                </ul>
                            </td>
                            
                            <td style="font-weight: bold; color: #ff7b00;">
                                Rp <?php echo number_format($ord['total_bayar'], 0, ',', '.'); ?>
                            </td>
                            
                            <td style="font-size: 0.85rem; color: #555;">
                                <?php echo htmlspecialchars($ord['catatan'] ?: '-'); ?>
                            </td>
                            
                            <td>
                                <span style="padding: 4px 8px; border-radius: 6px; font-size: 0.8rem; font-weight: bold; background: <?php echo $ord['status'] === 'Selesai' ? '#d4edda; color: #155724;' : '#fff3cd; color: #856404;'; ?>">
                                    <?php echo $ord['status']; ?>
                                </span>
                            </td>
                            
                            <td style="text-align: center;">
                                <?php if ($ord['status'] === 'Pending'): ?>
                                    <a href="pesanan.php?selesai_id=<?php echo $ord['id_pesanan']; ?>" class="btn" style="padding: 6px 12px; font-size: 0.8rem; background: #28a745; text-decoration: none;">Selesai</a>
                                <?php else: ?>
                                    <span style="color: #28a745; font-weight: bold;">✓ Beres</span>
                                <?php endif; ?>
                            </td>

                        </tr>
                    <?php endwhile; ?>

                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>

</body>
</html>