<?php
require "config.php";
wajib_login();

if (isset($_GET["hapus"])) {
    $id = (int)$_GET["hapus"];
    $stmt = $conn->prepare("DELETE FROM produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: produk.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $kode = trim($_POST["kode"]);
    $nama = trim($_POST["nama"]);
    $harga = (int)$_POST["harga"];
    $stok = (int)$_POST["stok"];

    if ($id > 0) {
        $stmt = $conn->prepare("UPDATE produk SET kode=?, nama=?, harga=?, stok=? WHERE id=?");
        $stmt->bind_param("ssiii", $kode, $nama, $harga, $stok, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO produk (kode,nama,harga,stok) VALUES (?,?,?,?)");
        $stmt->bind_param("ssii", $kode, $nama, $harga, $stok);
    }
    $stmt->execute();
    header("Location: produk.php");
    exit;
}

$edit = null;
if (isset($_GET["edit"])) {
    $id = (int)$_GET["edit"];
    $stmt = $conn->prepare("SELECT * FROM produk WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $edit = $stmt->get_result()->fetch_assoc();
}
$data = $conn->query("SELECT * FROM produk ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Produk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<main class="container">
<h2>Data Produk</h2>

<div class="panel">
<h3><?= $edit ? "Edit Produk" : "Tambah Produk" ?></h3>
<form method="post" class="form-grid">
    <input type="hidden" name="id" value="<?= $edit["id"] ?? 0 ?>">
    <div><label>Kode</label><input name="kode" required value="<?= htmlspecialchars($edit["kode"] ?? "") ?>"></div>
    <div><label>Nama Produk</label><input name="nama" required value="<?= htmlspecialchars($edit["nama"] ?? "") ?>"></div>
    <div><label>Harga</label><input type="number" name="harga" min="0" required value="<?= $edit["harga"] ?? 0 ?>"></div>
    <div><label>Stok</label><input type="number" name="stok" min="0" required value="<?= $edit["stok"] ?? 0 ?>"></div>
    <div><button class="btn primary"><?= $edit ? "Update" : "Simpan" ?></button>
    <?php if ($edit): ?><a class="btn" href="produk.php">Batal</a><?php endif; ?></div>
</form>
</div>

<div class="panel">
<table>
<thead><tr><th>No</th><th>Kode</th><th>Nama</th><th>Harga</th><th>Stok</th><th>Aksi</th></tr></thead>
<tbody>
<?php $no=1; while($p=$data->fetch_assoc()): ?>
<tr>
<td><?= $no++ ?></td>
<td><?= htmlspecialchars($p["kode"]) ?></td>
<td><?= htmlspecialchars($p["nama"]) ?></td>
<td>Rp <?= number_format($p["harga"],0,',','.') ?></td>
<td><?= $p["stok"] ?></td>
<td>
<a class="btn small" href="?edit=<?= $p["id"] ?>">Edit</a>
<a class="btn small danger" href="?hapus=<?= $p["id"] ?>" onclick="return confirm('Hapus produk ini?')">Hapus</a>
</td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>
</main>
</body>
</html>