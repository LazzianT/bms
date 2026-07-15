-- ============================================================
-- Migration: tambah tabel baru + kolom tambahan
-- Jalankan di phpMyAdmin / MySQL CLI setelah import DB utama
-- ============================================================

-- 1. master_jasa (tabel layanan jasa)
CREATE TABLE IF NOT EXISTS `master_jasa` (
    `jasa_id`    INT NOT NULL AUTO_INCREMENT,
    `nama_jasa`  VARCHAR(100) NOT NULL,
    `harga`      DOUBLE DEFAULT 0,
    `deskripsi`  TEXT,
    `kategori`   VARCHAR(50) DEFAULT NULL,
    `is_aktif`   TINYINT(1) DEFAULT 1,
    PRIMARY KEY (`jasa_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- sample data jasa
INSERT IGNORE INTO `master_jasa` (`nama_jasa`, `harga`, `deskripsi`, `kategori`, `is_aktif`) VALUES
('Jasa Servis Ringan',        80000,  'Servis ringan + ganti oli',         'Servis',    1),
('Jasa Ganti Oli',            20000,  'Ganti oli mesin',                   'Servis',    1),
('Jasa Ganti Ban',            20000,  'Pasang ban baru',                   'Ban',       1),
('Jasa Ganti Kampas Rem',     15000,  'Pasang kampas rem depan/belakang',  'Rem',       1),
('Jasa Penggantian Aki',      20000,  'Pasang aki baru',                   'Kelistrikan', 1),
('Jasa Tune Up',              50000,  'Tune up mesin injektor',            'Mesin',     1),
('Jasa Overhaul',            300000,  'Turun mesin / overhaul',            'Mesin',     1),
('Jasa Penggantian CVT',      60000,  'Servis CVT / ganti roller & v-belt','CVT',       1),
('Jasa Cek Kelistrikan',      30000,  'Diagnosa kelistrikan motor',        'Kelistrikan', 1),
('Belah Mesin',               20000,  'Bongkar mesin untuk perbaikan',     'Mesin',     1);

-- 2. Tambah kolom stok_minimum ke sparepart (jika belum ada)
SET @dbname = DATABASE();
SET @tablename = 'sparepart';
SET @columnname = 'stok_minimum';
SET @preparedStatement = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
     WHERE TABLE_SCHEMA = @dbname
       AND TABLE_NAME = @tablename
       AND COLUMN_NAME = @columnname) > 0,
    'SELECT 1',
    CONCAT('ALTER TABLE `', @tablename, '` ADD COLUMN `', @columnname, '` INT DEFAULT 5')
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 3. transaksi_po (Purchase Order header)
CREATE TABLE IF NOT EXISTS `transaksi_po` (
    `po_id`       INT NOT NULL AUTO_INCREMENT,
    `nomor_po`    VARCHAR(30) NOT NULL,
    `tanggal`     DATE NOT NULL,
    `supplier_id` INT NOT NULL,
    `status`      ENUM('Draft','Sent','Partial Received','Fully Received','Cancelled') DEFAULT 'Draft',
    `total_nilai` DECIMAL(15,2) DEFAULT 0,
    `keterangan`  TEXT,
    `user_id`     INT NOT NULL,
    `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`po_id`),
    KEY `idx_supplier` (`supplier_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_po_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
    CONSTRAINT `fk_po_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. transaksi_po_detail (item dalam PO)
CREATE TABLE IF NOT EXISTS `transaksi_po_detail` (
    `detail_id`    INT NOT NULL AUTO_INCREMENT,
    `po_id`        INT NOT NULL,
    `sparepart_id` INT NOT NULL,
    `qty_dipesan`  INT NOT NULL DEFAULT 0,
    `qty_diterima` INT NOT NULL DEFAULT 0,
    `harga_beli`   DECIMAL(15,2) DEFAULT 0,
    `subtotal`     DECIMAL(15,2) DEFAULT 0,
    PRIMARY KEY (`detail_id`),
    KEY `idx_po` (`po_id`),
    KEY `idx_sparepart` (`sparepart_id`),
    CONSTRAINT `fk_podetail_po` FOREIGN KEY (`po_id`) REFERENCES `transaksi_po` (`po_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_podetail_sp` FOREIGN KEY (`sparepart_id`) REFERENCES `sparepart` (`sparepart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. transaksi_mrs (Material Receipt Sheet / penerimaan barang)
CREATE TABLE IF NOT EXISTS `transaksi_mrs` (
    `mrs_id`       INT NOT NULL AUTO_INCREMENT,
    `nomor_mrs`    VARCHAR(30) NOT NULL,
    `po_id`        INT NOT NULL,
    `tanggal`      DATE NOT NULL,
    `supplier_id`  INT NOT NULL,
    `status`       ENUM('Received','Verified') DEFAULT 'Received',
    `keterangan`   TEXT,
    `user_id`      INT NOT NULL,
    `created_at`   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mrs_id`),
    KEY `idx_po` (`po_id`),
    KEY `idx_supplier` (`supplier_id`),
    KEY `idx_user` (`user_id`),
    CONSTRAINT `fk_mrs_po` FOREIGN KEY (`po_id`) REFERENCES `transaksi_po` (`po_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mrs_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `supplier` (`supplier_id`),
    CONSTRAINT `fk_mrs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. transaksi_mrs_detail (item dalam MRS)
CREATE TABLE IF NOT EXISTS `transaksi_mrs_detail` (
    `detail_id`    INT NOT NULL AUTO_INCREMENT,
    `mrs_id`       INT NOT NULL,
    `sparepart_id` INT NOT NULL,
    `qty_diterima` INT NOT NULL DEFAULT 0,
    `harga_beli`   DECIMAL(15,2) DEFAULT 0,
    `subtotal`     DECIMAL(15,2) DEFAULT 0,
    PRIMARY KEY (`detail_id`),
    KEY `idx_mrs` (`mrs_id`),
    KEY `idx_sparepart` (`sparepart_id`),
    CONSTRAINT `fk_mrsdetail_mrs` FOREIGN KEY (`mrs_id`) REFERENCES `transaksi_mrs` (`mrs_id`) ON DELETE CASCADE,
    CONSTRAINT `fk_mrsdetail_sp` FOREIGN KEY (`sparepart_id`) REFERENCES `sparepart` (`sparepart_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
