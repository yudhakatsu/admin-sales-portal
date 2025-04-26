<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            background: #f8f8f8;
            background-image: url("{{ public_path('image/pdf-wallpaper.png') }}");
            filter: opacity(0.5); 
            padding: 20px;
            margin: 0;
        }
        /* .container {
            background: white;
            padding: 25px;
            border: 1px solid #ddd;
        } */
        h1, h4 {
            text-align: center;
            margin: 0;
        }
        h1 {
            font-size: 48px;
            font-weight: 400;
        }
        .center {
            text-align: center;
            font-size: 14px;
        }
        .info {
            margin-top: 15px;
            margin-bottom: 10px;
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            padding: 10px 20px;
        }
        .info-customers {
            display: flex;
            justify-content: space-between;
            margin: 0 10px;
        }
        .info-customers > div {
            width: 48%;
        }
        .info div {
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            margin-bottom: 15px;
        }
        table {
            /* border-bottom: 1px solid #ccc; */
            border-top: 1px solid #D4D0C2;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            padding-top: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #D4D0C2;
        }
        .total {
            text-align: end;
            padding-left: 360px;
            padding-bottom: 30px;
            /* text-align: right; */
            /* font-weight: bold; */
        }
        .footer {
            position: fixed;
            bottom: 20;
            left: 0;
            right: 0;
            text-align: center;
            padding: 10px 0;
            font-size: 12px;
            line-height: 0.2;
        }
        .bold {
            font-weight: bold;
        }
        hr {
            color: #D4D0C2;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>FHAFLORIST</h1>
    <div class="center">Pusatnya Buket, Hantaran & Mahar Kroya</div>
    <div class="center">Jl. Kepudang, Rt 02 Rw 05, Bajing Kulon, Kec. Kroya</div>
    <div class="center">Telp. 088983102727</div>
    <br>
    <hr>

    <div class="info">
        <p style="margin-bottom: 0; margin-left: 7px;">Atas Nama :</p>
        <table width="100%" style="border-collapse: collapse; margin-top: 0; border: none;">
            <tr>
                <td style="width: 40%; vertical-align: top; border: none;">
                    <p style="margin: 0; line-height: 1.5;"><span style="font-size: 24px;">{{ $transaction->customer->name }}</span><br>
                    {{ $transaction->phone_number }}<br>
                    {{ $transaction->address }}</p>
                </td>
                <td style="width: 50%; vertical-align: top; border: none; padding-left: 100px;">
                    <table style="width: 100%; border: none;">
                        <tr style="border: none;">
                            <td style="width: 45%; border: none; padding: 2px 0; line-height: 1.5;">Tanggal Pesan</td>
                            <td style="border: none; padding: 2px 0; line-height: 1.5;">: {{ \Carbon\Carbon::parse($transaction->order_date)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr style="border: none;">
                            <td style="border: none; padding: 2px 0; line-height: 1.5;">Waktu Ambil</td>
                            <td style="border: none; padding: 2px 0; line-height: 1.5;"> : {{ \Carbon\Carbon::parse($transaction->pickup_date)->translatedFormat('d F Y') }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    </div>

    <table>
        <thead>
            <tr>
                <th>Produk</th>
                <th>Harga</th>
                <th>Jumlah</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transaction->details as $item)
                <tr>
                    <td><strong>{{ $item->product->name }}</strong></td>
                    <td>Rp. {{ number_format($item->product->price, 0, ',', '.') }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>Rp. {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td></td>
                <td style="border-bottom: 1px solid #D4D0C2"></td>
                <td style="border-bottom: 1px solid #D4D0C2"></td>
                <td style="border-bottom: 1px solid #D4D0C2"></td>
            </tr>
        </tbody>
    </table>

    <div class="total">Total: <span style="font-size: 18px; padding-left: 80px;font-weight: bold;">Rp. {{ number_format($transaction->total, 0, ',', '.') }}</span></div>

    @if($transaction->payment_status == "dp")
        <div class="total" style="margin: 0; padding-bottom: 0; line-height: 1px;">
            Uang Muka: 
            <span style="font-size: 18px; padding-left: 35px; font-weight: bold;">
                Rp. {{ number_format($transaction->dp_amount, 0, ',', '.') }}
            </span>
        </div>
        <table style="border: none; margin: 0; padding: 0;">
            <tbody style="border: none; margin: 0; padding: 0;">
            <tr style="border: none; margin: 0; padding: 0;">
                <td style="width: 170px;"></td>
                <td style="border-bottom: 2px dashed #D4D0C2"></td>
                <td style="border-bottom: 2px dashed #D4D0C2"></td>
                <td style="border-bottom: 2px dashed #D4D0C2; text-align: right;"><span style="font-size: 16px;">-</span></td>
            </tr>
            </tbody>
        </table>
    @endif

    @if ($transaction->payment_status == 'dp')
        <div class="total">
            Sisa Pembayaran: 
            <span style="font-size: 18px; padding-left: 0px; font-weight: bold;">
                Rp. {{ number_format($transaction->total - $transaction->dp_amount, 0, ',', '.') }}
            </span>
        </div>
    @endif

    <div style="position: relative;top: -10px; border-top: 2px dashed #D4D0C2; width: 100%;"></div>

    <div class="total" style="margin-bottom: 30px;margin-top: 30px; padding-bottom: 0; line-height: 1px; padding-left: 200px;">
        Nominal Pembayaran: 
        <span style="font-size: 18px; padding-left: 140px; font-weight: bold;">
            Rp. {{ number_format($transaction->payment, 0, ',', '.') }}
        </span>
    </div>

    <div class="total" style="margin-bottom: 30px; padding-bottom: 0; line-height: 1px; padding-left: 200px;">
        Kembalian: 
        <span style="font-size: 18px; padding-left: 210px; font-weight: bold;">
            Rp. 
            @if ($transaction->dp_amount > 0 && $transaction->payment_status == 'lunas')
                {{ number_format($transaction->payment - ($transaction->total - $transaction->dp_amount), 0, ',', '.') }}
            @elseif ($transaction->payment_status == 'dp')
                {{ number_format($transaction->payment - $transaction->dp_amount, 0, '.', ',') }}
            @else
                {{ number_format($transaction->payment - $transaction->total, 0, ',', '.') }}
            @endif
        </span>
    </div>

    <hr>

    <p style="padding-bottom: 20px;">
        <strong>Catatan</strong>: {{ $transaction->note ? $transaction->note : '-' }}
    </p>
    <p><strong style="padding-right: 16px;">Status Pembayaran</strong>: {{ ucfirst($transaction->payment_status) }}</p>
    <p style="line-height: 1px;"><strong style="padding-right: 10px;">Metode Pembayaran</strong>: {{ ucfirst($transaction->payment_method) }}</p>

    <div class="footer" style="opacity: 30%;">
        <p>Terima Tasih Telah Terbelanja</p>
        <p>Simpan Struk Ini Sebagai Bukti Saat Pengambilan Barang</p>
        <p>fhaflorist.service@gmail.com | 088983102727</p>
        <br>
        <p>FHAFLORIST APP</p>
    </div>
</div>
</body>
</html>
