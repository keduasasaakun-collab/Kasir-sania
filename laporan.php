<?php
require "config.php";
wajib_login();
$data=$conn->query("SELECT * FROM transaksi ORDER BY tanggal DESC");
$total=$conn->query("SELECT COALESCE(SUM(total),0) AS total FROM transaksi")->fetch_assoc()["total"];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Laporan</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<main class="container">
<h2>Laporan Penjualan</h2>
<div class="panel"><h3>Total Penjualan: Rp <?= number_format($total,0,',','.') ?></h3></div>
<div class="panel">
<table>
<thead><tr><th>ID</th><th>Tanggal</th><th>Total</th><th>Bayar</th><th>Kembalian</th><th>Struk</th></tr></thead>
<tbody>
<?php while($t=$data->fetch_assoc()): ?>
<tr>
<td>#<?= $t["id"] ?></td>
<td><?= htmlspecialchars($t["tanggal"]) ?></td>
<td>Rp <?= number_format($t["total"],0,',','.') ?></td>
<td>Rp <?= number_format($t["bayar"],0,',','.') ?></td>
<td>Rp <?= number_format($t["kembalian"],0,',','.') ?></td>
<td><a class="btn small" href="struk.php?id=<?= $t["id"] ?>">Lihat</a></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</main>
</body>
</html>