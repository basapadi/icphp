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
        .header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        .header img {
            max-height: 60px;
            margin-right: 15px;
        }
        .header .company-info h2 {
            margin: 0;
            color: #444;
        }
        .header .company-info p {
            margin: 2px 0;
            font-size: 13px;
            color: #666;
        }
        .info, .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .info td {
            padding: 2px 8px;
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
        .po {
            margin: 0;
        }
        .po h3 {
            margin: 0;
            color: #444;
        }
        .po p {
            margin: 2px 0;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        @if(!empty($company->logo))
            <img src="{{ $company->logo }}" alt="Logo {{ $company->namaToko }}">
        @endif
        <div class="company-info">
            <h2>{{ $company->namaToko }}</h2>
            <p>{{ $company->alamat }}</p>
            <p>Telp: {{ $company->telepon }} | Email: {{ $company->email }}</p>
        </div>
    </div>

    <div class="po">
        <h3>Purchase Order</h3>
        <p>No : <strong>{{ $po->kode }}</strong></p>
        <p>Tanggal : {{ $po->tanggal_formatted }}</p>
    </div>

    <p>Yth. Bapak/Ibu <strong>{{ $po->contact->nama }}</strong>,</p>

    <p>
    Bersama email ini, kami dari <strong>{{ $company->namaToko }}</strong> ingin menyampaikan 
    Purchase Order (PO) terbaru dengan nomor <strong>{{ $po->kode }}</strong>. 
    Dokumen ini berisi daftar barang yang kami pesan sesuai kebutuhan operasional kami.
    </p>

    <p>
    Kami berharap pesanan ini dapat segera diproses dan dikirim sesuai dengan tanggal perkiraan kedatangan 
    (<strong>{{ $po->tanggal_perkiraan_formatted }}</strong>). 
    Apabila ada hal yang perlu dikonfirmasi lebih lanjut, silakan hubungi kami melalui 
    telepon <strong>{{ $company->telepon }}</strong> atau email <strong>{{ $company->email }}</strong>.
    </p>

    <h3>Detail Pesanan</h3>
    <table class="items">
        <thead>
            <tr>
                <th style="text-align: center;">No</th>
                <th>Nama Barang</th>
                <th>Jumlah</th>
                <th style="text-align: right;">Harga</th>
                <th style="text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->details as $i => $detail)
                <tr>
                    <td style="text-align: center;">{{ $i+1 }}</td>
                    <td>{{ $detail->item->nama }}</td>
                    <td>{{ $detail->jumlah }}</td>
                    <td style="text-align: right;">{{ $detail->harga_formatted }}</td>
                    <td style="text-align: right;">{{ $detail->total_harga_formatted }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total : {{ $po->total_formatted }} ({{ $po->total_terbilang }})</strong></p>

    @if(!empty($po->catatan))
        <p><strong>Catatan:</strong> {{ $po->catatan }}</p>
    @endif

    <div class="footer">
        <p>Terima kasih atas kerjasamanya.</p>
        <p>Hormat kami,</p>
        <p><em>{{ $company->namaToko }}</em></p>
    </div>
</body>
</html>
