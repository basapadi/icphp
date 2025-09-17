flowchart TD
    %% Alur Purchase Order
    A1[Mulai PO] --> B1[Input PO di sistem]
    B1 --> C1[Verifikasi stok & supplier]
    C1 --> D1{Stok tersedia?}
    D1 -- Ya --> E1[Konfirmasi PO ke supplier]
    D1 -- Tidak --> F1[Request restock / ubah supplier]
    E1 --> G1[Supplier mengirim barang]
    G1 --> H1[Terima barang & update stok]
    H1 --> I1[Invoice dari supplier diterima]
    I1 --> J1[Bayar supplier]
    J1 --> K1[Selesai PO]

    %% Alur Sales Order
    A2[Mulai SO] --> B2[Input SO di sistem]
    B2 --> C2[Verifikasi stok tersedia?]
    C2 -- Ya --> D2[Konfirmasi ke customer]
    C2 -- Tidak --> E2[Informasikan ke customer / PO barang]
    D2 --> F2[Pick & Pack barang]
    F2 --> G2[Barang dikirim ke customer]
    G2 --> H2[Update stok & catat pengiriman]
    H2 --> I2[Invoice dikirim ke customer]
    I2 --> J2[Terima pembayaran customer]
    J2 --> K2[Selesai SO]

    %% Hubungan PO dan SO
    E2 --> B1  %% Jika stok tidak tersedia, trigger PO
