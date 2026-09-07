<?php
require "config.php";
wajib_login();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $items = json_decode($_POST["items"] ?? "[]", true);
    $bayar = (int)($_POST["bayar"] ?? 0);

    if (!$items || $bayar < 0) die("Data transaksi tidak valid.");

    $total = 0;
    foreach ($items as $item) {
        $pid = (int)$item["id"];
        $qty = (int)$item["qty"];
        $stmt = $conn->prepare("SELECT harga, stok FROM produk WHERE id=?");
        $stmt->bind_param("i", $pid);
        $stmt->execute();
        $p = $stmt->get_result()->fetch_assoc();
        if (!$p || $qty <= 0 || $qty > $p["stok"]) die("Stok tidak cukup.");
        $total += $p["harga"] * $qty;
    }

    if ($bayar < $total) die("Uang pembayaran kurang.");

    $kembalian = $bayar - $total;
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("INSERT INTO transaksi (total,bayar,kembalian) VALUES (?,?,?)");
        $stmt->bind_param("iii", $total, $bayar, $kembalian);
        $stmt->execute();
        $transaksi_id = $conn->insert_id;

        foreach ($items as $item) {
            $pid = (int)$item["id"];
            $qty = (int)$item["qty"];
            $stmt = $conn->prepare("SELECT harga FROM produk WHERE id=?");
            $stmt->bind_param("i", $pid);
            $stmt->execute();
            $harga = $stmt->get_result()->fetch_assoc()["harga"];
            $subtotal = $harga * $qty;

            $stmt = $conn->prepare("INSERT INTO detail_transaksi (transaksi_id,produk_id,qty,harga,subtotal) VALUES (?,?,?,?,?)");
            $stmt->bind_param("iiiii", $transaksi_id, $pid, $qty, $harga, $subtotal);
            $stmt->execute();

            $stmt = $conn->prepare("UPDATE produk SET stok=stok-? WHERE id=?");
            $stmt->bind_param("ii", $qty, $pid);
            $stmt->execute();
        }
        $conn->commit();
        header("Location: struk.php?id=".$transaksi_id);
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        die("Transaksi gagal.");
    }
}

$produk = $conn->query("SELECT * FROM produk WHERE stok > 0 ORDER BY nama");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Transaksi</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<?php include "navbar.php"; ?>
<main class="container">
<h2>Transaksi Penjualan</h2>
<div class="panel">
    <div class="form-grid">
        <div>
            <label>Produk</label>
            <select id="produk">
                <option value="">-- Pilih Produk --</option>
                <?php while($p=$produk->fetch_assoc()): ?>
                <option value="<?= $p["id"] ?>" data-harga="<?= $p["harga"] ?>" data-stok="<?= $p["stok"] ?>" data-nama="<?= htmlspecialchars($p["nama"], ENT_QUOTES) ?>">
                    <?= htmlspecialchars($p["kode"]) ?> - <?= htmlspecialchars($p["nama"]) ?> (Stok <?= $p["stok"] ?>)
                </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div><label>Jumlah</label><input type="number" id="qty" min="1" value="1"></div>
        <div><label>&nbsp;</label><button type="button" class="btn primary" onclick="tambah()">Tambah</button></div>
    </div>
</div>

<div class="panel">
<table id="cart">
<thead><tr><th>Produk</th><th>Harga</th><th>Qty</th><th>Subtotal</th><th>Aksi</th></tr></thead>
<tbody></tbody>
</table>
<h3>Total: <span id="total">Rp 0</span></h3>
<form method="post" onsubmit="return kirim()">
    <input type="hidden" name="items" id="items">
    <label>Bayar</label>
    <input type="number" id="bayar" name="bayar" min="0" required>
    <button class="btn primary">Bayar & Cetak Struk</button>
</form>
</div>
</main>
<script>
let cart = [];

function rupiah(n){ return "Rp " + n.toLocaleString("id-ID"); }

function tambah(){
    const s=document.getElementById("produk");
    const opt=s.options[s.selectedIndex];
    const id=parseInt(s.value);
    const qty=parseInt(document.getElementById("qty").value);
    if(!id || qty<1) return alert("Pilih produk dan jumlah.");

    const harga=parseInt(opt.dataset.harga);
    const stok=parseInt(opt.dataset.stok);
    const nama=opt.dataset.nama;
    const existing=cart.find(x=>x.id===id);
    const jumlahBaru=(existing ? existing.qty : 0)+qty;

    if(jumlahBaru>stok) return alert("Jumlah melebihi stok.");
    if(existing) existing.qty=jumlahBaru;
    else cart.push({id,nama,harga,qty});
    render();
}

function hapus(i){ cart.splice(i,1); render(); }

function render(){
    const tbody=document.querySelector("#cart tbody");
    tbody.innerHTML="";
    let total=0;
    cart.forEach((x,i)=>{
        const sub=x.harga*x.qty; total+=sub;
        tbody.innerHTML += `<tr>
            <td>${x.nama}</td><td>${rupiah(x.harga)}</td><td>${x.qty}</td>
            <td>${rupiah(sub)}</td><td><button type="button" class="btn small danger" onclick="hapus(${i})">Hapus</button></td>
        </tr>`;
    });
    document.getElementById("total").textContent=rupiah(total);
}

function kirim(){
    if(cart.length===0){ alert("Keranjang masih kosong."); return false; }
    const total=cart.reduce((a,x)=>a+x.harga*x.qty,0);
    const bayar=parseInt(document.getElementById("bayar").value);
    if(bayar<total){ alert("Uang pembayaran kurang."); return false; }
    document.getElementById("items").value=JSON.stringify(cart);
    return true;
}
</script>
</body>
</html>