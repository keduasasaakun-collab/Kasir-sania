<?php
require "config.php";
wajib_login();

$produk = $conn->query("SELECT COUNT(*) AS jumlah FROM produk")->fetch_assoc()["jumlah"];
$transaksi = $conn->query("SELECT COUNT(*) AS jumlah FROM transaksi")->fetch_assoc()["jumlah"];
$penjualan = $conn->query("SELECT COALESCE(SUM(total),0) AS total FROM transaksi WHERE DATE(tanggal)=CURDATE()")->fetch_assoc()["total"];
$stok = $conn->query("SELECT COALESCE(SUM(stok),0) AS total FROM produk")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<main class="container">
<h2>Dashboard</h2>
<div class="cards">
    <div class="card"><h3><?= $produk ?></h3><p>Total Produk</p></div>
    <div class="card"><h3><?= $transaksi ?></h3><p>Total Transaksi</p></div>
    <div class="card"><h3>Rp <?= number_format($penjualan,0,',','.') ?></h3><p>Penjualan Hari Ini</p></div>
    <div class="card"><h3><?= $stok ?></h3><p>Total Stok</p></div>
</div>
<div class="panel">
    <h3>Selamat datang, <?= htmlspecialchars($_SESSION['user']['username']) ?> 👋</h3>
    <p>Gunakan menu di atas untuk mengelola produk dan transaksi.</p>
</div>
</main>
</body>
</html>