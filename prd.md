# Product Requirements Document (PRD)
# Garage Management System (BMS)

**Versi**: 1.0  
**Tanggal**: 15 Juli 2026  
**Status**: Draft

---

## 1. Ringkasan Proyek

Aplikasi web manajemen bengkel motor berbasis PHP Native dengan MySQLi (procedural). Aplikasi ini memudahkan pengelolaan data pelanggan, kendaraan, sparepart, mekanik, supplier, serta seluruh transaksi bengkel (servis, pembelian sparepart, purchase order, dan penerimaan barang). Dilengkapi laporan analitik dengan export PDF dan Excel.

---

## 2. Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Backend | PHP 8.x Native (MySQLi Procedural) |
| Database | MySQL 8.0 |
| Frontend | HTML5, Bootstrap 5, jQuery |
| Chart | Chart.js |
| Export PDF | DOMPDF |
| Export Excel | PhpSpreadsheet |
| Server | Laragon (Apache) |

---

## 3. User Roles & Hak Akses

| Role | Keterangan | Akses |
|------|------------|-------|
| **Admin** | Pengelola sistem | Full akses ke semua menu, CRUD semua master data, transaksi, laporan, dan pengaturan user |
| **Kasir** | Pengelola transaksi keuangan | Transaksi servis, transaksi pembelian sparepart, melihat laporan |
| **Manager** | Pengawas bengkel | Melihat semua laporan, approve PO, melihat data (read-only untuk master data tertentu) |

### Menu Access Matrix

| Menu | Admin | Kasir | Manager |
|------|:-----:|:-----:|:-------:|
| Dashboard | ✅ | ✅ | ✅ |
| Master Customer | CRUD | R | R |
| Master Kendaraan | CRUD | R | R |
| Master Sparepart | CRUD | R | R |
| Master Jasa | CRUD | R | R |
| Master Mekanik | CRUD | R | R |
| Master Supplier | CRUD | R | R |
| Transaksi Service | CRUD | CRUD | R |
| Transaksi Pembelian Sparepart | CRUD | CRUD | R |
| Transaksi PO (Purchase Order) | CRUD | R | CRUD (Approve) |
| Transaksi MRS (Penerimaan) | CRUD | R | R |
| Laporan Performa Mekanik | ✅ | ✅ | ✅ |
| Laporan Penjualan Sparepart | ✅ | ✅ | ✅ |
| Laporan Servis | ✅ | ✅ | ✅ |
| Laporan Omset vs Pembelian | ✅ | ✅ | ✅ |
| User Management | CRUD | ❌ | ❌ |

> **R** = Read Only, **CRUD** = Create, Read, Update, Delete

---

## 4. Database Schema

### 4.1 Tabel yang Sudah Ada (Existing)

#### `client`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| client_id | INT PK AUTO_INCREMENT | ID pelanggan |
| nama | VARCHAR(100) | Nama pelanggan |
| alamat | TEXT | Alamat |
| telepon | VARCHAR(20) | No. telepon |
| email | VARCHAR(100) | Email |
| tanggal_daftar | DATE | Tanggal registrasi |

#### `vehicle`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| vehicle_id | INT PK AUTO_INCREMENT | ID kendaraan |
| client_id | INT FK -> client | Pemilik kendaraan |
| no_polisi | VARCHAR(20) UNIQUE | No. polisi |
| merk | VARCHAR(50) | Merek kendaraan (Honda, Yamaha, dll) |
| tipe | VARCHAR(50) | Tipe (Vario, NMAX, dll) |
| cc | INT | Kapasitas mesin |
| tipe_kendaraan | VARCHAR(50) | Roda 2 / Lebih dari Roda 2 |
| tahun | INT | Tahun pembuatan |
| no_rangka | VARCHAR(50) | No. rangka |
| no_mesin | VARCHAR(50) | No. mesin |

#### `mekanik`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| mekanik_id | INT PK AUTO_INCREMENT | ID mekanik |
| nama | VARCHAR(100) | Nama mekanik |
| telepon | VARCHAR(20) | No. telepon |
| spesialis | VARCHAR(100) | Spesialisasi (Mesin, Kelistrikan, CVT, dll) |

#### `supplier`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| supplier_id | INT PK AUTO_INCREMENT | ID supplier |
| nama | VARCHAR(100) | Nama supplier |
| alamat | TEXT | Alamat |
| telepon | VARCHAR(20) | No. telepon |
| email | VARCHAR(100) | Email |

#### `sparepart`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| sparepart_id | INT PK AUTO_INCREMENT | ID sparepart |
| kode_sparepart | VARCHAR(50) UNIQUE | Kode unik sparepart |
| nama_sparepart | VARCHAR(100) | Nama sparepart |
| satuan | VARCHAR(20) | Satuan (Pcs, Botol, Set, Liter) |
| stok | INT DEFAULT 0 | Jumlah stok tersedia |
| harga_beli | DOUBLE DEFAULT 0 | Harga beli dari supplier |
| harga_jual | DOUBLE DEFAULT 0 | Harga jual ke customer |
| supplier_id | INT FK -> supplier | Supplier utama |

#### `users`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| user_id | INT PK AUTO_INCREMENT | ID user |
| username | VARCHAR(50) UNIQUE | Username login |
| password_hash | VARCHAR(255) | Password (hash) |
| role | VARCHAR(20) DEFAULT 'admin' | admin / kasir / manager |
| nama_lengkap | VARCHAR(100) | Nama lengkap |

#### `transaksi_pendaftaran`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| registration_id | INT PK AUTO_INCREMENT | ID pendaftaran |
| vehicle_id | INT FK -> vehicle | Kendaraan yang didaftarkan |
| client_id | INT FK -> client | Pemilik kendaraan |
| keluhan | TEXT | Keluhan / kebutuhan servis |
| mekanik_id | INT FK -> mekanik | Mekanik yang ditugaskan |
| status | VARCHAR(50) DEFAULT 'Registered' | Registered / InProgress / Completed |
| tanggal_daftar | DATETIME | Waktu pendaftaran |
| tanggal_mulai | DATETIME | Waktu mulai dikerjakan |
| catatan | TEXT | Catatan tambahan |

#### `transaksi_servis`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| trans_id | INT PK AUTO_INCREMENT | ID transaksi |
| tanggal | DATETIME | Waktu transaksi |
| client_id | INT FK -> client | Pelanggan |
| vehicle_id | INT FK -> vehicle | Kendaraan |
| mekanik_id | INT FK -> mekanik | Mekanik pengerja |
| registration_id | INT FK -> transaksi_pendaftaran | Pendaftaran terkait |
| keluhan | TEXT | Keluhan |
| status_servis | VARCHAR(50) | Menunggu / Dikerjakan / Selesai Lunas |
| total_jasa | DOUBLE DEFAULT 0 | Total biaya jasa |
| total_sparepart | DOUBLE DEFAULT 0 | Total biaya sparepart |
| grand_total | DOUBLE DEFAULT 0 | Total seluruh |
| bayar | DOUBLE DEFAULT 0 | Jumlah dibayar |
| kembali | DOUBLE DEFAULT 0 | Kembalian |
| metode_bayar | VARCHAR(50) DEFAULT 'Cash' | Cash / Transfer / QRIS |
| user_kasir | VARCHAR(50) | User kasir yang memproses |

#### `transaksi_servis_detail`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| detail_id | INT PK AUTO_INCREMENT | ID detail |
| trans_id | INT FK -> transaksi_servis | Transaksi induk |
| sparepart_id | INT FK -> sparepart | Sparepart yang dipakai |
| qty | INT DEFAULT 1 | Jumlah |
| harga | DOUBLE DEFAULT 0 | Harga jual per item |
| subtotal | DOUBLE DEFAULT 0 | qty x harga |

#### `transaksi_servis_jasa`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| detail_id | INT PK AUTO_INCREMENT | ID detail |
| trans_id | INT FK -> transaksi_servis | Transaksi induk |
| nama_jasa | VARCHAR(100) | Nama jasa (akan diupdate ke FK) |
| harga | DOUBLE DEFAULT 0 | Harga jasa |
| qty | INT DEFAULT 1 | Jumlah |
| subtotal | DOUBLE DEFAULT 0 | qty x harga |

#### `transaksi_pembelian`
| Kolom | Tipe | Keterangan |
|-------|------|------------|
| purchase_id | INT PK AUTO_INCREMENT | ID pembelian |
| tanggal | DATETIME | Waktu pembelian |
| supplier_id | INT FK -> supplier | Supplier |
| sparepart_id | INT FK -> sparepart | Sparepart yang dibeli |
| qty | INT DEFAULT 1 | Jumlah |
| harga_beli | DOUBLE DEFAULT 0 | Harga beli per item |
| total_harga | DOUBLE DEFAULT 0 | qty x harga_beli |
| keterangan | TEXT | Keterangan |

---

### 4.2 Tabel Baru

#### `master_jasa` (BARU)
> Normalisasi data jasa yang sebelumnya hanya teks di `transaksi_servis_jasa`.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| jasa_id | INT PK AUTO_INCREMENT | ID jasa |
| nama_jasa | VARCHAR(100) | Nama jasa (Servis CVT, Ganti Oli, dll) |
| harga | DOUBLE DEFAULT 0 | Harga standar jasa |
| deskripsi | TEXT | Deskripsi jasa |
| kategori | VARCHAR(50) | Kategori (Servis Ringan, Servis Berat, Ganti Part, dll) |
| is_aktif | TINYINT(1) DEFAULT 1 | Status aktif/nonaktif |

#### `transaksi_po` (BARU - Purchase Order)
> Mencatat pemesanan sparepart ke supplier.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| po_id | INT PK AUTO_INCREMENT | ID Purchase Order |
| nomor_po | VARCHAR(50) UNIQUE | Nomor PO otomatis (PO-YYYYMMDD-XXX) |
| tanggal | DATETIME | Waktu pembuatan PO |
| supplier_id | INT FK -> supplier | Supplier yang dipesan |
| status | VARCHAR(50) DEFAULT 'Draft' | Draft / Sent / Partial Received / Fully Received / Cancelled |
| total_nilai | DOUBLE DEFAULT 0 | Total nilai PO |
| keterangan | TEXT | Keterangan |
| user_id | INT FK -> users | User yang membuat PO |

#### `transaksi_po_detail` (BARU)
> Detail item yang dipesan dalam PO.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| detail_id | INT PK AUTO_INCREMENT | ID detail |
| po_id | INT FK -> transaksi_po | PO induk |
| sparepart_id | INT FK -> sparepart | Sparepart yang dipesan |
| qty_dipesan | INT DEFAULT 0 | Jumlah yang dipesan |
| qty_diterima | INT DEFAULT 0 | Jumlah yang sudah diterima |
| harga_beli | DOUBLE DEFAULT 0 | Harga beli per item |
| subtotal | DOUBLE DEFAULT 0 | qty_dipesan x harga_beli |

#### `transaksi_mrs` (BARU - Material Receipt Sheet)
> Penerimaan barang dari supplier berdasarkan PO.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| mrs_id | INT PK AUTO_INCREMENT | ID MRS |
| nomor_mrs | VARCHAR(50) UNIQUE | Nomor MRS otomatis (MRS-YYYYMMDD-XXX) |
| po_id | INT FK -> transaksi_po | PO terkait |
| tanggal | DATETIME | Waktu penerimaan |
| supplier_id | INT FK -> supplier | Supplier pengirim |
| status | VARCHAR(50) DEFAULT 'Received' | Received / Verified / Cancelled |
| keterangan | TEXT | Keterangan (kondisi barang, dll) |
| user_id | INT FK -> users | User yang menerima |

#### `transaksi_mrs_detail` (BARU)
> Detail item yang diterima dalam MRS.

| Kolom | Tipe | Keterangan |
|-------|------|------------|
| detail_id | INT PK AUTO_INCREMENT | ID detail |
| mrs_id | INT FK -> transaksi_mrs | MRS induk |
| sparepart_id | INT FK -> sparepart | Sparepart yang diterima |
| qty_diterima | INT DEFAULT 0 | Jumlah yang diterima |
| harga_beli | DOUBLE DEFAULT 0 | Harga beli per item |
| subtotal | DOUBLE DEFAULT 0 | qty_diterima x harga_beli |

---

### 4.3 Perubahan pada Tabel Existing

#### Update `transaksi_servis_jasa`
- Tambah kolom `jasa_id` (INT, FK -> master_jasa)
- Kolom `nama_jasa` tetap ada sebagai fallback, tapi data utama diambil dari `master_jasa`

#### Update `sparepart`
- Tambah kolom `stok_minimum` (INT DEFAULT 5) untuk alert stok rendah

---

### 4.4 ERD (Entity Relationship)

```
+----------+     +----------+
|  client   |-----<| vehicle  |
+----------+     +----------+
     |                  |
     |                  |
     v                  v
+--------------------------+
| transaksi_pendaftaran    |
+--------------------------+
     |
     v
+--------------------------+      +----------------------+
|   transaksi_servis       |----->| transaksi_servis_detail|
+--------------------------+      +----------------------+
     |                                   |
     |                                   v
     |                            +--------------+
     |                            |  sparepart   |
     |                            +--------------+
     |                                   ^
     v                                   |
+--------------------------+      +----------------------+
| transaksi_servis_jasa    |      |   transaksi_po_detail |
+--------------------------+      +----------------------+
     |                                   |
     v                                   v
+--------------+                +------------------+
| master_jasa  |                |  transaksi_po    |
+--------------+                +------------------+
                                         |
                                         v
                                +------------------+
                                |  transaksi_mrs   |
                                +------------------+
                                         |
                                         v
                                +----------------------+
                                | transaksi_mrs_detail  |
                                +----------------------+
```

---

## 5. Menu & Fitur

### 5.1 Login & Authentication

| Fitur | Keterangan |
|-------|------------|
| Halaman Login | Form username + password |
| Session Management | Session timeout 30 menit |
| Proteksi Halaman | Setiap halaman dicek session & role |
| Logout | Hapus session, redirect ke login |

---

### 5.2 Dashboard

| Widget | Keterangan |
|--------|------------|
| Total Customer | Jumlah pelanggan terdaftar |
| Total Kendaraan | Jumlah kendaraan terdaftar |
| Total Transaksi Hari Ini | Jumlah transaksi servis hari ini |
| Omset Hari Ini | Total pendapatan hari ini |
| Stok Menipis | Jumlah sparepart dengan stok <= stok_minimum |
| Servis Aktif | Jumlah servis yang sedang dikerjakan (InProgress) |
| Chart Pendapatan Bulanan | Grafik line/chart pendapatan 6 bulan terakhir |
| Chart Top Sparepart | Grafik bar 5 sparepart terlaris |

---

### 5.3 Master Data

#### 5.3.1 Master Customer (`master_customer.php`)

| Kolom Tabel | Form Input | Tipe |
|-------------|-----------|------|
| client_id | Auto | - |
| nama | Input text | Wajib |
| alamat | Textarea | Opsional |
| telepon | Input text | Wajib |
| email | Input email | Opsional |
| tanggal_daftar | Date picker | Otomatis hari ini |

**Fitur:**
- Tabel daftar customer dengan pencarian dan pagination
- Tombol Tambah, Edit, Hapus (konfirmasi)
- Pencarian berdasarkan nama, telepon, email
- Saat hapus client, kendaraan terkait juga terhapus (CASCADE)

#### 5.3.2 Master Kendaraan (`master_kendaraan.php`)

| Kolom Tabel | Form Input | Tipe |
|-------------|-----------|------|
| vehicle_id | Auto | - |
| client_id | Select (data client) | Wajib |
| no_polisi | Input text | Wajib, Unique |
| merk | Input text / Select | Wajib |
| tipe | Input text | Wajib |
| cc | Input number | Wajib |
| tipe_kendaraan | Select (Roda 2 / Lebih dari Roda 2) | Wajib |
| tahun | Input number / Select year | Wajib |
| no_rangka | Input text | Opsional |
| no_mesin | Input text | Opsional |

**Fitur:**
- Tabel daftar kendaraan dengan kolom nama pemilik (join client)
- Filter berdasarkan pemilik
- Pencarian berdasarkan no_polisi, merk, tipe

#### 5.3.3 Master Sparepart (`master_sparepart.php`)

| Kolom Tabel | Form Input | Tipe |
|-------------|-----------|------|
| sparepart_id | Auto | - |
| kode_sparepart | Input text | Wajib, Unique |
| nama_sparepart | Input text | Wajib |
| satuan | Select (Pcs, Botol, Set, Liter) | Wajib |
| stok | Input number | Default 0 |
| stok_minimum | Input number | Default 5 |
| harga_beli | Input number (rupiah) | Wajib |
| harga_jual | Input number (rupiah) | Wajib |
| supplier_id | Select (data supplier) | Wajib |

**Fitur:**
- Tabel daftar sparepart dengan indicator stok (hijau = aman, kuning = menipis, merah = kosong)
- Filter berdasarkan supplier, kategori stok
- Pencarian berdasarkan kode, nama sparepart
- Kolom margin (harga_jual - harga_beli)

#### 5.3.4 Master Jasa (`master_jasa.php`) — **BARU**

| Kolom Tabel | Form Input | Tipe |
|-------------|-----------|------|
| jasa_id | Auto | - |
| nama_jasa | Input text | Wajib |
| harga | Input number (rupiah) | Wajib |
| deskripsi | Textarea | Opsional |
| kategori | Select (Servis Ringan, Servis Berat, Ganti Part, Tune Up, Overhaul) | Wajib |
| is_aktif | Checkbox | Default aktif |

**Fitur:**
- Tabel daftar jasa dengan kategori
- Filter berdasarkan kategori
- Toggle aktif/nonaktif

#### 5.3.5 Master Mekanik (`master_mekanik.php`)

| Kolom Tabel | Form Input | Tipe |
|-------------|-----------|------|
| mekanik_id | Auto | - |
| nama | Input text | Wajib |
| telepon | Input text | Wajib |
| spesialis | Input text / Select | Wajib |

**Fitur:**
- Tabel daftar mekanik dengan spesialisasi
- Pencarian berdasarkan nama, spesialis

#### 5.3.6 Master Supplier (`master_supplier.php`)

| Kolom Tabel | Form Input | Tipe |
|-------------|-----------|------|
| supplier_id | Auto | - |
| nama | Input text | Wajib |
| alamat | Textarea | Opsional |
| telepon | Input text | Wajib |
| email | Input email | Opsional |

**Fitur:**
- Tabel daftar supplier
- Pencarian berdasarkan nama, telepon

---

### 5.4 Transaksi

#### 5.4.1 Transaksi Service Kendaraan (`transaksi_servis.php`)

**Alur Transaksi:**

```
+---------+    +----------+    +-----------+    +----------+
| Antre    |--->| Dikerjakan|--->| Selesai    |--->| Lunas    |
|(Antrean) |    |(Mulai)    |    |(Finish)    |    |(Bayar)   |
+---------+    +----------+    +-----------+    +----------+
     |              |
     |              |
     +--------------+
     Bisa tambah sparepart
```

**Status Transaksi:**
1. **Antrean (Antre)** — Pendaftaran diterima, menunggu mulai dikerjakan
2. **Dikerjakan (InProgress)** — Mekanik sedang mengerjakan
3. **Selesai (Completed)** — Pengerjaan selesai, menunggu pembayaran
4. **Lunas (Selesai Lunas)** — Sudah dibayar

**Halaman Utama (`transaksi_servis.php`):**
- Tab/List: Semua | Antrean | Dikerjakan | Selesai | Lunas
- Setiap card/baris menampilkan: No. Transaksi, Customer, Kendaraan, Mekanik, Keluhan, Status, Tanggal
- Tombol Aksi per status:
  - Antrean -> "Mulai Kerjakan" (assign mekanik jika belum ada)
  - Dikerjakan -> "Selesai" + Form input sparepart & jasa
  - Selesai -> "Bayar" + Form pembayaran

**Form Pendaftaran Servis Baru (`tambah_servis.php`):**
1. Pilih/Create Customer
2. Pilih Kendaraan (dropdown terfilter berdasarkan customer)
3. Input Keluhan
4. Pilih Mekanik (optional saat pendaftaran, wajib saat mulai kerja)
5. Tanggal Daftar (otomatis)
6. Catatan (opsional)
7. Tombol "Daftarkan" -> status = Antrean

**Form Edit/Detail Servis (`detail_servis.php`):**
- Info: Customer, Kendaraan, Mekanik, Keluhan, Status, Tanggal
- **Bagian Sparepart**: Tabel daftar sparepart yang dipakai + tombol "Tambah Sparepart"
  - Modal: Pilih sparepart, qty, harga jual (otomatis dari master)
  - Subtotal otomatis
- **Bagian Jasa**: Tabel daftar jasa + tombol "Tambah Jasa"
  - Modal: Pilih jasa dari master_jasa (harga otomatis) atau input manual
  - Subtotal otomatis
- **Ringkasan**: Total Jasa + Total Sparepart = Grand Total
- **Pembayaran** (saat status Selesai):
  - Grand Total
  - Input Bayar
  - Kembalian (otomatis)
  - Metode Bayar (Cash / Transfer / QRIS)

#### 5.4.2 Transaksi Pembelian Sparepart (`transaksi_pembelian_sparepart.php`)

> Transaksi beli sparepart langsung dari customer (tanpa servis).

**Halaman Utama:**
- Tabel daftar transaksi pembelian: No., Tanggal, Customer, Total, Bayar, Status

**Form Pembelian Baru:**
1. Pilih Customer (atau "Walk-in Customer")
2. Pilih Kendaraan (optional)
3. Tambah item sparepart:
   - Pilih sparepart -> otomatis ambil harga jual
   - Input qty
   - Subtotal = qty x harga jual
4. Ringkasan: Total
5. Pembayaran: Bayar, Kembalian, Metode Bayar

**Fitur:**
- Otomatis update stok sparepart (kurangi) saat transaksi selesai/lunas
- Cetak struk pembelian

#### 5.4.3 Transaksi Purchase Order / PO (`transaksi_po.php`)

> Pemesanan sparepart ke supplier.

**Halaman Utama:**
- Tabel daftar PO: No. PO, Tanggal, Supplier, Total Nilai, Status, User Pembuat
- Filter: Semua | Draft | Sent | Partial Received | Fully Received

**Status PO:**
1. **Draft** — PO baru dibuat, bisa diedit/dihapus
2. **Sent** — PO sudah dikirim ke supplier, tidak bisa dihapus
3. **Partial Received** — Sebagian barang sudah diterima
4. **Fully Received** — Semua barang sudah diterima
5. **Cancelled** — PO dibatalkan

**Form Buat PO Baru (`tambah_po.php`):**
1. Nomor PO (otomatis: PO-YYYYMMDD-XXX)
2. Pilih Supplier
3. Tambah item sparepart:
   - Pilih sparepart
   - Qty Dipesan
   - Harga Beli (otomatis dari master sparepart)
   - Subtotal
4. Ringkasan: Total Nilai PO
5. Keterangan (opsional)
6. Tombol "Simpan Draft" atau "Kirim PO"

**Detail PO (`detail_po.php`):**
- Info PO: Nomor, Tanggal, Supplier, Status, Keterangan
- Tabel item: Sparepart, Qty Dipesan, Qty Diterima, Harga, Subtotal
- Tombol aksi:
  - Draft -> "Kirim PO" (ubah status ke Sent)
  - Sent -> "Terima Barang" (buka form MRS)

#### 5.4.4 Transaksi Penerimaan Barang / MRS (`transaksi_mrs.php`)

> Penerimaan barang dari supplier berdasarkan PO.

**Halaman Utama:**
- Tabel daftar MRS: No. MRS, Tanggal, No. PO, Supplier, Status

**Form MRS Baru (`tambah_mrs.php`):**
1. Pilih PO (hanya PO dengan status Sent atau Partial Received)
2. Sistem otomatis tampilkan item PO yang belum/belum sepenuhnya diterima
3. Input qty diterima per item (tidak boleh melebihi qty_dipesan)
4. Harga beli (otomatis dari PO)
5. Keterangan kondisi barang
6. Tombol "Simpan Penerimaan"

**Aksi Saat MRS Disimpan:**
1. Update `qty_diterima` di `transaksi_po_detail`
2. Update stok sparepart (tambah qty_diterima)
3. Update status PO:
   - Jika semua item sudah diterima penuh -> "Fully Received"
   - Jika sebagian -> "Partial Received"
4. Generate nomor MRS otomatis (MRS-YYYYMMDD-XXX)

**Detail MRS (`detail_mrs.php`):**
- Info MRS: Nomor, Tanggal, PO Terkait, Supplier, Status
- Tabel item: Sparepart, Qty Diterima, Harga, Subtotal

---

### 5.5 Laporan

#### 5.5.1 Laporan Performa Mekanik (`laporan_performa_mekanik.php`)

**Filter:**
- Periode: Dari - Sampai (date picker)
- Mekanik: Semua / Pilih tertentu

**Kolom Tabel:**
| Kolom | Keterangan |
|-------|------------|
| Nama Mekanik | Nama mekanik |
| Total Servis | Jumlah transaksi yang dikerjakan |
| Servis Selesai | Jumlah yang status Lunas |
| Total Jasa | Total pendapatan jasa |
| Total Sparepart Terpakai | Total harga sparepart yang terpakai |
| Total Pendapatan | Total jasa + sparepart |
| Rata-rata per Servis | Total Pendapatan / Total Servis |

**Visualisasi:**
- Bar chart perbandingan total servis per mekanik
- Pie chart proporsi pendapatan per mekanik

**Export:**
- PDF: Laporan dengan tabel + chart
- Excel: Data tabel lengkap

#### 5.5.2 Laporan Penjualan Sparepart (`laporan_penjualan_sparepart.php`)

**Filter:**
- Periode: Dari - Sampai
- Sparepart: Semua / Pilih tertentu
- Kategori: Berdasarkan transaksi servis / transaksi pembelian / Semua

**Kolom Tabel:**
| Kolom | Keterangan |
|-------|------------|
| Kode Sparepart | Kode |
| Nama Sparepart | Nama |
| Qty Terjual | Total quantity terjual |
| Total Penjualan | Total harga jual |
| Harga Rata-rata | Rata-rata harga jual |
| Stok Saat Ini | Stok tersisa |

**Visualisasi:**
- Bar chart: Top 10 sparepart terlaris
- Line chart: Tren penjualan per bulan

**Export:**
- PDF & Excel

#### 5.5.3 Laporan Servis (`laporan_servis.php`)

**Filter:**
- Periode: Dari - Sampai
- Status: Semua / Lunas / Dikerjakan
- Mekanik: Semua / Pilih tertentu

**Kolom Tabel:**
| Kolom | Keterangan |
|-------|------------|
| No. Transaksi | ID transaksi |
| Tanggal | Tanggal servis |
| Customer | Nama pelanggan |
| Kendaraan | No. polisi + tipe |
| Mekanik | Nama mekanik |
| Keluhan | Keluhan |
| Total Jasa | Biaya jasa |
| Total Sparepart | Biaya sparepart |
| Grand Total | Total |
| Status | Status pembayaran |

**Summary (Footer):**
- Total Transaksi
- Total Jasa
- Total Sparepart
- Grand Total Keseluruhan

**Visualisasi:**
- Line chart: Tren jumlah servis per bulan
- Bar chart: Total pendapatan servis per bulan

**Export:**
- PDF & Excel

#### 5.5.4 Laporan Omset vs Pembelian (`laporan_omset_pembelian.php`)

**Filter:**
- Periode: Dari - Sampai
- Interval: Harian / Bulanan / Tahunan

**Kolom Tabel:**
| Kolom | Keterangan |
|-------|------------|
| Periode | Bulan/Tanggal |
| Omset Servis | Total pendapatan dari servis |
| Omset Sparepart | Total pendapatan dari penjualan sparepart |
| Total Omset | Omset Servis + Omset Sparepart |
| Total Pembelian (PO) | Total pengeluaran beli sparepart ke supplier |
| Profit | Total Omset - Total Pembelian |
| Margin | Profit / Total Omset x 100% |

**Visualisasi:**
- Line chart: Omset vs Pembelian per periode
- Bar chart: Profit per periode

**Export:**
- PDF & Excel

---

## 6. Struktur Halaman & Navigasi

### Sidebar Navigation

```
+---------------------+
|  BMS - Bengkel       |
|  Management System   |
+---------------------+
|  Dashboard           |
+---------------------+
|  Master Data         |
|    |- Customer       |
|    |- Kendaraan      |
|    |- Sparepart      |
|    |- Jasa           |
|    |- Mekanik        |
|    |- Supplier       |
+---------------------+
|  Transaksi           |
|    |- Service        |
|    |- Pembelian Part |
|    |- Purchase Order |
|    |- Penerimaan(MRS)|
+---------------------+
|  Laporan             |
|    |- Performa Mekanik|
|    |- Penjualan Part |
|    |- Servis         |
|    |- Omset vs PO    |
+---------------------+
|  Settings            |
|    |- Profil         |
|    |- User Management|
+---------------------+
|  [Nama User] [Role]  |
|     [Logout]         |
+---------------------+
```

---

## 7. Struktur Folder

```
bms/
|-- prd.md                          <- Dokumen ini
|-- config/
|   |-- database.php                <- Koneksi database (MySQLi procedural)
|   +-- auth.php                    <- Fungsi autentikasi & session
|-- includes/
|   |-- header.php                  <- HTML head, navbar, sidebar
|   |-- footer.php                  <- Script closing, footer
|   +-- functions.php               <- Fungsi helper (format rupiah, dll)
|-- auth/
|   |-- login.php                   <- Halaman login
|   +-- logout.php                  <- Proses logout
|-- dashboard.php                   <- Dashboard utama
|-- master/
|   |-- customer.php                <- CRUD Customer
|   |-- kendaraan.php               <- CRUD Kendaraan
|   |-- sparepart.php               <- CRUD Sparepart
|   |-- jasa.php                    <- CRUD Jasa
|   |-- mekanik.php                 <- CRUD Mekanik
|   +-- supplier.php                <- CRUD Supplier
|-- transaksi/
|   |-- servis_list.php             <- Daftar transaksi servis
|   |-- servis_tambah.php           <- Pendaftaran servis baru
|   |-- servis_detail.php           <- Detail & proses servis
|   |-- pembelian_list.php          <- Daftar pembelian sparepart
|   |-- pembelian_tambah.php        <- Transaksi pembelian baru
|   |-- po_list.php                 <- Daftar PO
|   |-- po_tambah.php               <- Buat PO baru
|   |-- po_detail.php               <- Detail PO
|   |-- mrs_list.php                <- Daftar MRS
|   |-- mrs_tambah.php              <- Form penerimaan barang
|   +-- mrs_detail.php              <- Detail MRS
|-- laporan/
|   |-- performa_mekanik.php        <- Laporan performa mekanik
|   |-- penjualan_sparepart.php     <- Laporan penjualan sparepart
|   |-- servis.php                  <- Laporan servis
|   |-- omset_pembelian.php         <- Laporan omset vs pembelian
|   +-- export.php                  <- Handler export PDF/Excel
|-- api/
|   |-- get_kendaraan.php           <- AJAX: ambil kendaraan by client
|   |-- get_sparepart.php           <- AJAX: ambil data sparepart
|   |-- get_harga_jasa.php          <- AJAX: ambil harga jasa
|   |-- update_stok.php             <- AJAX: update stok
|   +-- get_po_detail.php           <- AJAX: ambil detail PO untuk MRS
|-- assets/
|   |-- css/
|   |   +-- style.css               <- Custom CSS
|   |-- js/
|   |   +-- app.js                  <- Custom JavaScript
|   +-- img/
|       +-- logo.png                <- Logo bengkel
+-- database/
    +-- garage_management.sql        <- Database dump (updated)
```

---

## 8. Alur Transaksi Detail

### 8.1 Alur Transaksi Servis

```
Customer Datang
      |
      v
[1] Pendaftaran Servis
    - Input: Customer, Kendaraan, Keluhan
    - Status: "Antrean"
    - Stok sparepart: BELUM berubah
      |
      v
[2] Mulai Dikerjakan
    - Assign Mekanik (jika belum ada)
    - Status: "Dikerjakan"
    - Tanggal Mulai tercatat
    - Stok sparepart: BELUM berubah
      |
      v
[3] Tambah Sparepart (selama proses)
    - Bisa ditambah kapan saja saat status Antrean/Dikerjakan
    - Pilih sparepart -> qty -> harga jual
    - Stok sparepart: BELUM berubah (baru dikurangi saat lunas)
      |
      v
[4] Tambah Jasa
    - Pilih dari master_jasa atau input manual
    - Harga otomatis dari master
      |
      v
[5] Selesai Dikerjakan
    - Status: "Selesai"
    - Grand Total terhitung: Total Jasa + Total Sparepart
      |
      v
[6] Pembayaran
    - Input: Bayar, Metode Bayar
    - Kembalian = Bayar - Grand Total
    - Status: "Lunas"
    - Stok sparepart: DIKURANGI sekarang
    - Catatan: user_kasir tercatat
```

### 8.2 Alur PO & MRS

```
[1] Buat PO
    - Input: Supplier, Item sparepart, Qty, Harga Beli
    - Status: "Draft"
    - Stok: TIDAK BERUBAH
      |
      v
[2] Kirim PO
    - Status: "Sent"
    - Tidak bisa edit/hapus
      |
      v
[3] Buat MRS (Penerimaan)
    - Pilih PO yang statusnya Sent/Partial
    - Input: Qty diterima per item
    - Status MRS: "Received"
      |
      v
[4] Simpan MRS
    - qty_diterima di PO_detail diupdate
    - Stok sparepart: DITAMBAH
    - Status PO diupdate:
        - Semua item diterima penuh -> "Fully Received"
        - Sebagian -> "Partial Received"
      |
      v
[5] (Opsional) Verifikasi MRS
    - Status MRS: "Verified"
    - Untuk audit trail
```

### 8.3 Alur Pembelian Sparepart Only

```
Customer Beli Sparepart (tanpa servis)
      |
      v
[1] Buat Transaksi
    - Pilih Customer (atau Walk-in)
    - Tambah item sparepart
    - Subtotal otomatis
      |
      v
[2] Pembayaran
    - Input Bayar, Metode Bayar
    - Status: "Lunas"
    - Stok sparepart: DIKURANGI
```

---

## 9. Aturan Bisnis

### 9.1 Stok Sparepart
1. Stok hanya berubah saat:
   - Transaksi servis/status Lunas -> stok dikurangi
   - Transaksi pembelian sparepart Lunas -> stok dikurangi
   - MRS diterima -> stok ditambah
2. Stok tidak boleh negatif (validasi sebelum transaksi)
3. Jika stok <= stok_minimum, tampilkan warning di dashboard

### 9.2 Harga
1. **harga_jual** sparepart: digunakan untuk transaksi ke customer
2. **harga_beli** sparepart: digunakan untuk PO ke supplier
3. Harga bisa di-override per transaksi (diskon/negotiasi)
4. Harga jasa diambil dari master_jasa, bisa di-override

### 9.3 Nomor Transaksi
| Tipe | Format | Contoh |
|------|--------|--------|
| PO | PO-YYYYMMDD-XXX | PO-20260715-001 |
| MRS | MRS-YYYYMMDD-XXX | MRS-20260715-001 |

### 9.4 Pembayaran
- Grand Total = Total Jasa + Total Sparepart
- Kembalian = Bayar - Grand Total (harus >= 0)
- Metode bayar: Cash, Transfer, QRIS

### 9.5 Penghapusan Data
| Data | Aturan |
|------|--------|
| Client | CASCADE hapus kendaraan terkait |
| Kendaraan | Tidak bisa hapus jika ada transaksi aktif (status Antrean/Dikerjakan) |
| Sparepart | Tidak bisa hapus jika ada riwayat transaksi |
| Supplier | SET NULL pada sparepart jika dihapus |

---

## 10. Export PDF & Excel

### 10.1 Implementasi
- **PDF**: Menggunakan DOMPDF library
- **Excel**: Menggunakan PhpSpreadsheet library
- **Installasi**: Melalui Composer (`composer require dompdf/dompdf` dan `composer require phpoffice/phpspreadsheet`)

### 10.2 Format Export PDF
- Header: Logo bengkel, judul laporan, periode
- Body: Tabel data + chart (jika ada)
- Footer: Tanggal cetak, halaman

### 10.3 Format Export Excel
- Sheet 1: Data tabel
- Sheet 2: Summary (opsional)
- Header row dengan bold
- Border pada tabel
- Format Rupiah pada kolom harga

### 10.4 Endpoint Export
```
laporan/export.php?type=pdf&report=performa_mekanik&from=2026-01-01&to=2026-07-15
laporan/export.php?type=excel&report=penjualan_sparepart&from=2026-01-01&to=2026-07-15
```

---

## 11. Teknis & Implementasi

### 11.1 Koneksi Database (`config/database.php`)
```php
<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "garage_management";
$conn = mysqli_connect($host, $username, $password, $database);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
mysqli_set_charset($conn, "utf8mb4");
?>
```

### 11.2 Convention PHP Native (MySQLi Procedural)
- Semua query menggunakan `mysqli_connect()`, `mysqli_query()`, `mysqli_fetch_assoc()`, `mysqli_num_rows()`
- **TIDAK** menggunakan OOP `$conn->` atau PDO
- **TIDAK** menggunakan `mysqli_prepare()` (prepared statement)
- Sanitasi input dengan `mysqli_real_escape_string()`
- Error handling dengan `mysqli_error()`

### 11.3 AJAX Calls (jQuery)
- Load data dinamis (kendaraan by client, harga sparepart, dll)
- Format response: JSON
- Endpoint di folder `api/`

### 11.4 Responsive Design
- Sidebar collapsible untuk mobile
- Tabel responsive (horizontal scroll)
- Form modal untuk input cepat

---

## 12. Milestone / Timeline

| No | Fase | Estimasi | Detail |
|----|------|----------|--------|
| 1 | Setup & Database | 1 hari | Buat database, update schema, koneksi |
| 2 | Auth & Layout | 1 hari | Login, sidebar, header, footer, session |
| 3 | Master Data | 3 hari | CRUD semua master (Customer, Kendaraan, Sparepart, Jasa, Mekanik, Supplier) |
| 4 | Transaksi Servis | 3 hari | Pendaftaran, alur status, sparepart & jasa, pembayaran |
| 5 | Transaksi Pembelian Part | 1 hari | Transaksi beli sparepart langsung |
| 6 | Transaksi PO & MRS | 2 hari | Buat PO, terima barang, update stok |
| 7 | Laporan | 2 hari | 4 laporan + filter + visualisasi chart |
| 8 | Export PDF & Excel | 1 hari | DOMPDF + PhpSpreadsheet |
| 9 | Testing & Bug Fix | 1 hari | Test semua alur |
| **Total** | | **15 hari** | |

---

## 13. Catatan Tambahan

1. **Password**: Untuk sementara password masih plain text di database (sesuai SQL dump existing). Di production sebaiknya menggunakan `password_hash()` dan `password_verify()`
2. **Backup**: Lakukan backup database secara berkala
3. **Print Struk**: Fitur cetak struk pembayaran tersedia di halaman detail transaksi
4. **Notifikasi**: Alert stok menipis ditampilkan di dashboard
5. **Audit Trail**: Pertimbangkan untuk menambah log aktivitas di masa depan

---

**Dokumen ini akan terus diperbarui seiring perkembangan proyek.**
