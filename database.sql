CREATE DATABASE IF NOT EXISTS kasir_db;
USE kasir_db;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE produk (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(30) NOT NULL UNIQUE,
    nama VARCHAR(100) NOT NULL,
    harga INT NOT NULL DEFAULT 0,
    stok INT NOT NULL DEFAULT 0
);

CREATE TABLE transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    total INT NOT NULL DEFAULT 0,
    bayar INT NOT NULL DEFAULT 0,
    kembalian INT NOT NULL DEFAULT 0
);

CREATE TABLE detail_transaksi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaksi_id INT NOT NULL,
    produk_id INT NOT NULL,
    qty INT NOT NULL,
    harga INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (transaksi_id) REFERENCES transaksi(id) ON DELETE CASCADE,
    FOREIGN KEY (produk_id) REFERENCES produk(id)
);

INSERT INTO users (username, password)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC1g6YfJ7LxM0Yj6ZpQe');

INSERT INTO produk (kode, nama, harga, stok) VALUES
('BRG001', 'Indomie Goreng', 3500, 50),
('BRG002', 'Teh Botol', 5000, 30),
('BRG003', 'Air Mineral', 3000, 40);
