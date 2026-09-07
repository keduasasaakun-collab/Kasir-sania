<?php
require "config.php";
wajib_login();
$id=(int)($_GET["id"] ?? 0);

$stmt=$conn->prepare("SELECT * FROM transaksi WHERE id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$trx=$stmt->get_result()->fetch_assoc();
if(!$trx) die("Transaksi tidak ditemukan.");

$stmt=$conn->prepare("SELECT d.*, p.nama FROM detail_transaksi d JOIN produk p ON p.id=d.produk_id WHERE d.transaksi_id=?");
$stmt->bind_param("i",$id);
$stmt->execute();
$detail=$stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Struk #<?= $id ?></title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="receipt">
<h2>KasirKu</h2>
<p><?= date("d-m-Y H:i", strtotime($trx["tanggal"])) ?></p>
<hr>
<?php while($d=$detail->fetch_assoc()): ?>
<div class="row">
<span><?= htmlspecialchars($d["nama"]) ?> x<?= $d["qty"] ?></span>
<strong>Rp <?= number_format($d["subtotal"],0,',','.') ?></strong>
</div>
<?php endwhile; ?>
<hr>
<div class="row"><span>Total</span><strong>Rp <?= number_format($trx["total"],0,',','.') ?></strong></div>
<div class="row"><span>Bayar</span><strong>Rp <?= number_format($trx["bayar"],0,',','.') ?></strong></div>
<div class="row"><span>Kembalian</span><strong>Rp <?= number_format($trx["kembalian"],0,',','.') ?></strong></div>
<br>
<button class="btn primary" onclick="window.print()">Cetak Struk</button>
<a class="btn" href="transaksi.php">Transaksi Baru</a>
</main>
</body>
</html>