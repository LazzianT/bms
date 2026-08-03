-- ============================================================
-- Local sync for current active PHP codebase
-- Use this AFTER importing your old/local garage_management dump
-- Safe to run multiple times on MySQL 8+
-- ============================================================

USE garage_management;

-- ==================== sparepart ====================
SET @dbname = DATABASE();

SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'sparepart'
              AND COLUMN_NAME = 'stok_minimum'
        ),
        'SELECT 1',
        'ALTER TABLE sparepart ADD COLUMN stok_minimum INT DEFAULT 5 AFTER stok'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE sparepart SET stok_minimum = 5 WHERE stok_minimum IS NULL;

-- ==================== transaksi_pendaftaran ====================
SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_pendaftaran'
              AND COLUMN_NAME = 'mekanik_id'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_pendaftaran ADD COLUMN mekanik_id INT NULL AFTER keluhan'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_pendaftaran'
              AND COLUMN_NAME = 'tanggal_mulai'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_pendaftaran ADD COLUMN tanggal_mulai DATETIME NULL AFTER tanggal_daftar'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- add FK if missing
SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE CONSTRAINT_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_pendaftaran'
              AND COLUMN_NAME = 'mekanik_id'
              AND REFERENCED_TABLE_NAME = 'mekanik'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_pendaftaran ADD CONSTRAINT fk_tp_mekanik FOREIGN KEY (mekanik_id) REFERENCES mekanik(mekanik_id)'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ==================== transaksi_servis ====================
SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_servis'
              AND COLUMN_NAME = 'mekanik_id'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_servis ADD COLUMN mekanik_id INT NULL AFTER vehicle_id'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_servis'
              AND COLUMN_NAME = 'registration_id'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_servis ADD COLUMN registration_id INT NULL AFTER mekanik_id'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_servis'
              AND COLUMN_NAME = 'metode_bayar'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_servis ADD COLUMN metode_bayar VARCHAR(50) DEFAULT ''Cash'' AFTER kembali'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
            WHERE TABLE_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_servis'
              AND COLUMN_NAME = 'user_kasir'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_servis ADD COLUMN user_kasir VARCHAR(50) NULL AFTER metode_bayar'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

UPDATE transaksi_servis
SET total_jasa = COALESCE(total_jasa, 0),
    total_sparepart = COALESCE(total_sparepart, 0),
    grand_total = COALESCE(grand_total, 0),
    bayar = COALESCE(bayar, 0),
    kembali = COALESCE(kembali, 0);

-- add FKs if missing
SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE CONSTRAINT_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_servis'
              AND COLUMN_NAME = 'registration_id'
              AND REFERENCED_TABLE_NAME = 'transaksi_pendaftaran'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_servis ADD CONSTRAINT fk_ts_registration FOREIGN KEY (registration_id) REFERENCES transaksi_pendaftaran(registration_id)'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

SET @preparedStatement = (
    SELECT IF(
        EXISTS(
            SELECT 1 FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE CONSTRAINT_SCHEMA = @dbname
              AND TABLE_NAME = 'transaksi_servis'
              AND COLUMN_NAME = 'mekanik_id'
              AND REFERENCED_TABLE_NAME = 'mekanik'
        ),
        'SELECT 1',
        'ALTER TABLE transaksi_servis ADD CONSTRAINT fk_ts_mekanik FOREIGN KEY (mekanik_id) REFERENCES mekanik(mekanik_id)'
    )
);
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ==================== transaksi_po ====================
CREATE TABLE IF NOT EXISTS transaksi_po (
    po_id INT NOT NULL AUTO_INCREMENT,
    nomor_po VARCHAR(30) NOT NULL,
    tanggal DATE NOT NULL,
    supplier_id INT NOT NULL,
    status ENUM('Draft','Sent','Partial Received','Fully Received','Cancelled') DEFAULT 'Draft',
    total_nilai DECIMAL(15,2) DEFAULT 0,
    keterangan TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (po_id),
    KEY idx_supplier (supplier_id),
    KEY idx_user (user_id),
    CONSTRAINT fk_po_supplier FOREIGN KEY (supplier_id) REFERENCES supplier(supplier_id),
    CONSTRAINT fk_po_user FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transaksi_po_detail (
    detail_id INT NOT NULL AUTO_INCREMENT,
    po_id INT NOT NULL,
    sparepart_id INT NOT NULL,
    qty_dipesan INT NOT NULL DEFAULT 0,
    qty_diterima INT NOT NULL DEFAULT 0,
    harga_beli DECIMAL(15,2) DEFAULT 0,
    subtotal DECIMAL(15,2) DEFAULT 0,
    PRIMARY KEY (detail_id),
    KEY idx_po (po_id),
    KEY idx_sparepart (sparepart_id),
    CONSTRAINT fk_podetail_po FOREIGN KEY (po_id) REFERENCES transaksi_po(po_id) ON DELETE CASCADE,
    CONSTRAINT fk_podetail_sp FOREIGN KEY (sparepart_id) REFERENCES sparepart(sparepart_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==================== transaksi_mrs ====================
CREATE TABLE IF NOT EXISTS transaksi_mrs (
    mrs_id INT NOT NULL AUTO_INCREMENT,
    nomor_mrs VARCHAR(30) NOT NULL,
    po_id INT NOT NULL,
    tanggal DATE NOT NULL,
    supplier_id INT NOT NULL,
    status ENUM('Received','Verified') DEFAULT 'Received',
    keterangan TEXT,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (mrs_id),
    KEY idx_po (po_id),
    KEY idx_supplier (supplier_id),
    KEY idx_user (user_id),
    CONSTRAINT fk_mrs_po FOREIGN KEY (po_id) REFERENCES transaksi_po(po_id) ON DELETE CASCADE,
    CONSTRAINT fk_mrs_supplier FOREIGN KEY (supplier_id) REFERENCES supplier(supplier_id),
    CONSTRAINT fk_mrs_user FOREIGN KEY (user_id) REFERENCES users(user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS transaksi_mrs_detail (
    detail_id INT NOT NULL AUTO_INCREMENT,
    mrs_id INT NOT NULL,
    sparepart_id INT NOT NULL,
    qty_diterima INT NOT NULL DEFAULT 0,
    harga_beli DECIMAL(15,2) DEFAULT 0,
    subtotal DECIMAL(15,2) DEFAULT 0,
    PRIMARY KEY (detail_id),
    KEY idx_mrs (mrs_id),
    KEY idx_sparepart (sparepart_id),
    CONSTRAINT fk_mrsdetail_mrs FOREIGN KEY (mrs_id) REFERENCES transaksi_mrs(mrs_id) ON DELETE CASCADE,
    CONSTRAINT fk_mrsdetail_sp FOREIGN KEY (sparepart_id) REFERENCES sparepart(sparepart_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;