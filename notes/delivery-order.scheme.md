# 📦 Database Structure: Delivery Order (DO)

## 1️⃣ Tabel Utama: `delivery_order` (Header)
| Kolom                 | Tipe Data (Contoh) | Keterangan                                                                 |
|------------------------|--------------------|-----------------------------------------------------------------------------|
| **do_id** *(PK)*       | VARCHAR / UUID     | ID unik DO (misal: `DO/2025/0542`).                                        |
| so_id / invoice_id     | VARCHAR / UUID     | Relasi ke **Sales Order** atau **Invoice**.                                |
| do_number              | VARCHAR            | Nomor dokumen resmi (bisa beda dari PK/UUID).                              |
| do_date                | DATE/TIMESTAMP     | Tanggal penerbitan DO.                                                     |
| delivery_date          | DATE/TIMESTAMP     | Tanggal rencana pengiriman.                                                |
| customer_id            | VARCHAR / UUID     | Referensi ke tabel **customer**.                                           |
| shipping_address       | TEXT               | Alamat pengiriman detail (snapshot agar tetap sama walau customer update). |
| contact_person         | VARCHAR            | Nama PIC penerima barang.                                                  |
| contact_phone          | VARCHAR            | Nomor telepon penerima.                                                    |
| delivery_method        | ENUM / TEXT        | Contoh: `Kurir Internal`, `Ekspedisi`, `Grab/Gojek`.                       |
| vehicle_info           | VARCHAR            | Nomor polisi kendaraan / info ekspedisi.                                   |
| driver_id / courier_id | VARCHAR / UUID     | Relasi ke tabel karyawan/kurir (jika internal).                             |
| status                 | ENUM / TEXT        | `Draft`, `Scheduled`, `On Delivery`, `Delivered`, `Canceled`.              |
| remarks                | TEXT               | Catatan tambahan (misal: “harap hubungi sebelum sampai”).                  |
| created_by             | VARCHAR / UUID     | User yang membuat DO.                                                      |
| created_at             | TIMESTAMP          | Waktu pencatatan.                                                          |
| updated_at             | TIMESTAMP          | Waktu pembaruan terakhir.                                                  |

---

## 2️⃣ Tabel Detail: `delivery_order_items`
| Kolom                 | Tipe Data (Contoh) | Keterangan                                                        |
|------------------------|--------------------|-------------------------------------------------------------------|
| **do_item_id** *(PK)*  | VARCHAR / UUID     | ID unik item DO.                                                  |
| do_id *(FK)*           | VARCHAR / UUID     | Relasi ke tabel `delivery_order` (header).                        |
| product_id             | VARCHAR / UUID     | Barang yang dikirim.                                              |
| description            | TEXT               | Nama/uraian barang.                                               |
| quantity_ordered       | DECIMAL            | Jumlah dipesan (mengacu ke Sales Order).                          |
| quantity_delivered     | DECIMAL            | Jumlah yang benar-benar dikirim (bisa kurang untuk *partial*).    |
| uom                    | VARCHAR            | Unit of measure (pcs, box, kg, dll).                              |
| batch_no / serial_no   | VARCHAR            | Jika perlu pelacakan batch/serial.                                |
| remarks                | TEXT               | Catatan per item (misal barang pengganti).                        |

---

## 🔑 Catatan Desain
1. **Relasi ke Sales Order** memastikan DO hanya dibuat untuk pesanan yang sah.  
2. **Status pengiriman** penting untuk tracking real-time (*On Delivery* → *Delivered*).  
3. **Audit trail** (`created_by`, `created_at`) menjaga akuntabilitas.  
4. **Quantity delivered** mendukung *partial delivery* (pengiriman sebagian).  
5. Simpan **alamat pengiriman** sebagai snapshot text untuk menjaga keakuratan historis.  
6. Gunakan **UUID atau autonumber** berbeda dari nomor dokumen agar tetap unik walau format nomor DO berubah per tahun/bulan.
