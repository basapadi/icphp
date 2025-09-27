<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Purchase Order {{ $po->kode }}</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 14px;
            color: #333;
        }
        .header h2 {
            margin: 0;
            color: #444;
        }
        .info, .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info td {
            padding: 4px 8px;
        }
        .items th, .items td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
        }
        .items th {
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 30px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Purchase Order</h2>
        <p>No: <strong>{{ $po->kode }}</strong></p>
        <p>Tanggal: {{ $po->tanggal_formatted }}</p>
        <p>Perkiraan Tiba: {{ $po->tanggal_perkiraan_formatted }}</p>
        <p>Status: {{ $po->status_label }} ({{ $po->approval_status_label }})</p>
    </div>

    <h3>Data Supplier</h3>
    <table class="info">
        <tr>
            <td><strong>Nama:</strong></td>
            <td>{{ $po->contact->nama }}</td>
        </tr>
        <tr>
            <td><strong>Alamat:</strong></td>
            <td>{{ $po->contact->alamat }}</td>
        </tr>
        <tr>
            <td><strong>Telepon:</strong></td>
            <td>{{ $po->contact->telepon }}</td>
        </tr>
        <tr>
            <td><strong>Email:</strong></td>
            <td>{{ $po->contact->email }}</td>
        </tr>
    </table>

    <h3>Detail Pesanan</h3>
    <table class="items">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->details as $i => $detail)
                <tr>
                    <td>{{ $i+1 }}</td>
                    <td>Item #{{ $detail->item->nama }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td>{{ $detail->harga_formatted }}</td>
                    <td>{{ $detail->total_harga_formatted }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total: {{ $po->total_formatted }} ({{ $po->total_terbilang }})</strong></p>

    @if(!empty($po->catatan))
        <p><strong>Catatan:</strong> {{ $po->catatan }}</p>
    @endif

    <div class="footer">
        <p>Terima kasih atas kerjasamanya.</p>
        <p>Hormat kami,</p>
        <p><em>Ihand Cashier</em></p>
    </div>
</body>
</html>
