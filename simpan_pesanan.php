<?php
header("Content-Type: application/json");
include 'koneksi.php';

// Mengaktifkan laporan error PHP agar jika ada yang salah langsung ketahuan
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input_json = file_get_contents('php://input');
$data_baru = json_decode($input_json, true);

if ($data_baru) {
    $id_pesanan = 'ORD-' . time();
    $nama = mysqli_real_escape_string($conn, $data_baru['nama']);
    $meja = mysqli_real_escape_string($conn, $data_baru['meja']);
    $catatan = mysqli_real_escape_string($conn, $data_baru['catatan']);
    $total = (int)$data_baru['total'];
    
    $detail_items = mysqli_real_escape_string($conn, json_encode($data_baru['items']));

    $query = "INSERT INTO pesanan (id_pesanan, nama_pelanggan, no_meja, total_bayar, catatan, detail_items, status) 
              VALUES ('$id_pesanan', '$nama', '$meja', $total, '$catatan', '$detail_items', 'Pending')";

    if (mysqli_query($conn, $query)) {
        echo json_encode(["status" => "success"]);
    } else {
        // JIKA GAGAL: PHP akan mengirimkan alasan detail error MySQL-nya ke frontend
        echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data JSON mentah tidak terbaca oleh PHP"]);
}
exit();
?>