<?php
session_start();

// PROTEKSI: Jika admin belum login melalui SQL, tendang kembali ke login.php
if (!isset($_SESSION['admin_logged'])) {
    header("Location: login.php");
    exit();
}

$file_json = 'data.json';

if (!file_exists($file_json)) {
    file_put_contents($file_json, json_encode(["menus" => []], JSON_PRETTY_PRINT));
}

$current_data = file_get_contents($file_json);
$array_data = json_decode($current_data, true);

// ==========================================
// LOGIKA 1: TAMBAH MENU BARU (FILE / URL)
// ==========================================
if (isset($_POST['add_menu'])) {
    $nama = $_POST['nama'];
    $deskripsi = $_POST['deskripsi'];
    $harga = (int)$_POST['harga'];
    $kategori = $_POST['kategori'];
    $metode_gambar = $_POST['metode_gambar'];
    
    // Set default gambar jika tidak diisi apa-apa
    $path_gambar = "https://placehold.co/600x400?text=No+Image";

    if ($metode_gambar === 'url' && !empty($_POST['gambar_url'])) {
        // JIKA ADMIN MEMILIH TEMPEL LINK INTERNET
        $path_gambar = $_POST['gambar_url'];
    } 
    elseif ($metode_gambar === 'file' && isset($_FILES['gambar_upload']) && $_FILES['gambar_upload']['error'] === 0) {
        // JIKA ADMIN MEMILIH UNGGAH FILE FISIK DARI LAPTOP
        if (!file_exists('img')) {
            mkdir('img', 0777, true);
        }

        $nama_asli = $_FILES['gambar_upload']['name'];
        $ekstensi = pathinfo($nama_asli, PATHINFO_EXTENSION);
        
        // Bikin nama unik pakai timestamp biar tidak bentrok
        $nama_file_baru = "img_" . time() . "." . $ekstensi;
        $target_simpan = "img/" . $nama_file_baru;

        if (move_uploaded_file($_FILES['gambar_upload']['tmp_name'], $target_simpan)) {
            $path_gambar = $target_simpan;
        }
    }

    // Tentukan ID Baru Otomatis
    $id_baru = 1;
    if (!empty($array_data['menus'])) {
        $id_tertinggi = max(array_column($array_data['menus'], 'id'));
        $id_baru = $id_tertinggi + 1;
    }
    
    // Susun data menu baru
    $new_item = [
        "id" => $id_baru,
        "nama" => $nama,
        "deskripsi" => $deskripsi,
        "harga" => $harga,
        "kategori" => $kategori,
        "gambar" => $path_gambar,
        "tersedia" => true
    ];

    $array_data['menus'][] = $new_item;
    file_put_contents($file_json, json_encode($array_data, JSON_PRETTY_PRINT));
    
    header("Location: admin.php");
    exit();
}

// ==========================================
// LOGIKA 2: HAPUS MENU & FILE JIKA ADA
// ==========================================
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    
    foreach ($array_data['menus'] as $menu) {
        if ($menu['id'] === $delete_id) {
            // Hapus file fisik di folder img jika jalurnya lokal
            if (file_exists($menu['gambar']) && strpos($menu['gambar'], 'img/') === 0) {
                unlink($menu['gambar']);
            }
        }
    }

    $array_data['menus'] = array_filter($array_data['menus'], function($item) use ($delete_id) {
        return $item['id'] !== $delete_id;
    });

    $array_data['menus'] = array_values($array_data['menus']);
    file_put_contents($file_json, json_encode($array_data, JSON_PRETTY_PRINT));
    
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BurjoGo - Admin Panel</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="admin-container">
    
    <div class="sidebar">
        <h2>BurjoGo Admin</h2>
        <p style="text-align: center; color: #fff; font-size: 0.85rem; margin-bottom: 20px; background: #444; padding: 5px; border-radius: 5px;">
            User Active: <strong><?php echo htmlspecialchars($_SESSION['admin_logged']); ?></strong>
        </p>
        <ul>
            <li><a href="admin.php" class="active">Kelola Menu</a></li>
            <li><a href="pesanan.php">Pesanan Masuk </a></li>
            <li><a href="index.html" target="_blank">Lihat Website</a></li>
            <li><a href="logout.php" style="background: #dc3545; margin-top: 30px; text-align: center; font-weight: bold; color: white; border-radius: 5px;">Logout</a></li>
        </ul>
    </div>

    <div class="admin-content">
        <h1 class="page-title" style="text-align: left; margin-bottom: 20px;">Dashboard Kelola Menu</h1>

        <div class="dashboard-cards">
            <div class="card-stat">
                <h3>Total Menu Aktif</h3>
                <p style="font-size: 1.8rem; font-weight: bold; color: #ff7b00; margin-top: 5px;">
                    <?php echo count($array_data['menus']); ?> Menu
                </p>
            </div>
        </div>

        <div class="form-container" style="margin: 0 0 40px 0; max-width: 100%;">
            <h3 style="margin-bottom: 20px;">Tambah Menu Baru</h3>
            
            <form action="admin.php" method="POST" enctype="multipart/form-data">
                
                <div class="form-group">
                    <label>Nama Makanan / Minuman</label>
                    <input type="text" name="nama" required placeholder="Contoh: Nasi Oreg Spesial Aa">
                </div>

                <div class="form-group">
                    <label>Harga (Rp)</label>
                    <input type="number" name="harga" required placeholder="Contoh: 13000">
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="kategori" required>
                        <option value="makanan">Makanan</option>
                        <option value="minuman">Minuman</option>
                        <option value="snack">Snack</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Metode Input Gambar</label>
                    <div style="display: flex; gap: 25px; margin: 8px 0;">
                        <label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="metode_gambar" value="file" checked onclick="toggleMetodeGambar('file')"> Upload File Laptop
                        </label>
                        <label style="font-weight: normal; cursor: pointer; display: flex; align-items: center; gap: 5px;">
                            <input type="radio" name="metode_gambar" value="url" onclick="toggleMetodeGambar('url')"> Pakai Link / URL Internet
                        </label>
                    </div>
                </div>

                <div class="form-group" id="box_gambar_file">
                    <label>Pilih File Gambar Hidangan (Format JPG/PNG)</label>
                    <input type="file" name="gambar_upload" accept="image/*" style="border: 1px dashed #ff7b00; padding: 10px; background: #fff8f2; cursor: pointer;">
                </div>

                <div class="form-group" id="box_gambar_url" style="display: none;">
                    <label>Masukkan Link / URL Gambar Internet</label>
                    <input type="text" name="gambar_url" placeholder="https://images.unsplash.com/photo-...">
                </div>

                <div class="form-group">
                    <label>Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3" required placeholder="Tulis rasa hidangan di sini..."></textarea>
                </div>

                <button type="submit" name="add_menu" class="btn" style="width: 100%; background: #ff7b00;">Simpan Menu Baru</button>
            </form>
        </div>

        <h3 style="margin-bottom: 15px;">Daftar Menu Saat Ini</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Gambar</th>
                    <th>Nama Menu</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Deskripsi</th>
                    <th style="text-align: center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($array_data['menus'])): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; color: #888; padding: 20px;">Belum ada menu yang terdaftar.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($array_data['menus'] as $menu): ?>
                        <tr>
                            <td>
                                <img src="<?php echo $menu['gambar']; ?>" style="width: 60px; height: 50px; object-fit: cover; border-radius: 6px;" onerror="this.src='https://placehold.co/60x50?text=Error'">
                            </td>
                            <td style="font-weight: 600;"><?php echo htmlspecialchars($menu['nama']); ?></td>
                            <td style="text-transform: capitalize;"><mark style="background:#ffe4c5; padding: 2px 6px; border-radius:4px; font-size:0.85rem; color:#232323; font-weight:600;"><?php echo $menu['kategori']; ?></mark></td>
                            <td>Rp <?php echo number_format($menu['harga'], 0, ',', '.'); ?></td>
                            <td style="color: #666; font-size: 0.9rem; max-width: 250px;"><?php echo htmlspecialchars($menu['deskripsi']); ?></td>
                            <td style="text-align: center;">
                                <a href="admin.php?delete_id=<?php echo $menu['id']; ?>" class="btn btn-danger" style="padding: 6px 12px; font-size: 0.85rem;" onclick="return confirm('Yakin ingin menghapus menu ini?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

    </div>
</div>

<script>
function toggleMetodeGambar(metode) {
    const boxFile = document.getElementById('box_gambar_file');
    const boxUrl = document.getElementById('box_gambar_url');
    
    if (metode === 'file') {
        boxFile.style.display = 'block';
        boxUrl.style.display = 'none';
    } else {
        boxFile.style.display = 'none';
        boxUrl.style.display = 'block';
    }
}
</script>

</body>
</html>