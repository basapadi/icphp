<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: helvetica, arial, sans-serif;
            font-size: 10pt;
            color: #000000;
            margin: 0;
        }
        .po-info {
            margin: 0;
            font-size: 10pt;
        }
        h3 {
            margin: 15px 0 8px 0;
            font-size: 12pt;
        }
        p {
            font-size: 10pt;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .items th {
            border: 1px solid #000000;
            padding: 8px 5px;
            background-color: #DDDDDD;
            text-align: center;
            font-weight: bold;
            font-size: 10pt;
            vertical-align: middle;
        }
        .items td {
            border: 1px solid #000000;
            font-size: 10pt;
            vertical-align: middle;
            padding: 8px 12px;
        }
        .text-right { 
            text-align: right; 
        }
        .text-center { 
            text-align: center; 
        }
        .text-left { 
            text-align: left; 
        }
        .catatan-section {
            margin-top: 5px;
            font-size: 10pt;
        }
        .ttd-table {
            width: 100%;
            margin-top: 10px;
            border-collapse: collapse;
        }
        .ttd-table td {
            text-align: center;
            vertical-align: top;
            font-size: 10pt;
        }
        .ttd-space {
            height: 60px;
        }
        .footer-section {
            margin-top: 20px;
            font-size: 9pt;
            text-align: left;
            color: #333333;
        }
    </style>
</head>
<body>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <td style="width:50px;">No.PO</td>
            <td style="width:5px;">:</td>
            <td style="width:550px;"> {{ $po->kode }}</td>
        </tr>
        <tr>
            <td>Tanggal</td>
            <td>:</td>
            <td> {{ $po->tanggal_formatted }}</td>
        </tr>
    </table>
    <p/>
    <table class="items" cellpadding="5" cellspacing="0">
        <thead>
            <tr>
                <th style="width: 8%;">No</th>
                <th style="width: 38%;">Nama</th>
                <th style="width: 12%;">Jumlah</th>
                <th style="width: 22%;">Harga Satuan</th>
                <th style="width: 22%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($po->details as $i => $detail)
                <tr>
                    <td style="width: 8%;" class="text-center">{{ $i+1 }}</td>
                    <td style="width: 38%;" class="text-left">{{ $detail->item->nama }}</td>
                    <td style="width: 12%;" class="text-center">{{ $detail->jumlah }}</td>
                    <td style="width: 22%;" class="text-right">{{ $detail->harga_formatted }}</td>
                    <td style="width: 22%;" class="text-right">{{ $detail->total_harga_formatted }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <p/>
    <!-- TOTAL -->
    <table class="total" cellpadding="0" cellspacing="0">
        <tr>
            <td style="width: 15%;">Total</td>
            <td style="width: 3%;">:</td>
            <td style="width: 80%;font-size: 10pt;">{{ $po->total_formatted }}</td>
        </tr>
        <tr>
            <td style="width: 15%;">Terbilang</td>
            <td style="width: 3%;">:</td>
            <td style="width: 80%;font-size: 10pt;">{{ $po->total_terbilang }}</td>
        </tr>
        <tr>
            <td style="width: 15%;">Catatan</td>
            <td style="width: 3%;">:</td>
            <td style="width: 80%;font-size: 10pt;">{{ $po->catatan }}</td>
        </tr>
       
    </table>

    <!-- TANDA TANGAN -->
    <table class="ttd-table" cellpadding="5" cellspacing="0">
        <tr>
            <td style="width: 50%;"><strong>Supplier</strong></td>
            <td style="width: 50%;"><strong>{{ $company->namaToko }}</strong></td>
        </tr>
        <tr>
            <td class="ttd-space">&nbsp;</td>
            <td class="ttd-space">&nbsp;</td>
        </tr>
        <tr>
            <td>( {{$po->contact->nama}} )</td>
            <td>( {{$company->pemilik}} )</td>
        </tr>
    </table>
</body>
</html>
