SELECT
    i.nama,
    i.kode_barang AS sku,
    i.barcode,
    i.status,
    i.kategori,
    u.nama AS unit_nama,
    s.jumlah,
    s.minimum_stock,
    s.tanggal_pembaruan
FROM
    item_stocks AS s
    LEFT JOIN masters AS u ON u.id = s.unit_id
    LEFT JOIN items AS i ON i.id = s.item_id