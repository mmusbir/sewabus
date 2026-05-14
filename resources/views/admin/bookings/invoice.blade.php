<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $booking->booking_code }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            color: #111827;
            background: #f8fafc;
        }
        .page {
            max-width: 840px;
            margin: 24px auto;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 24px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 20px;
        }
        .brand {
            margin-bottom: 10px;
        }
        .brand img {
            height: 36px;
            width: auto;
            object-fit: contain;
            max-width: 200px;
        }
        .title {
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        .muted {
            color: #64748b;
            font-size: 13px;
            margin-top: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        th, td {
            border: 1px solid #e5e7eb;
            text-align: left;
            padding: 10px;
            font-size: 13px;
            vertical-align: top;
        }
        th {
            background: #f8fafc;
            width: 34%;
        }
        .summary {
            margin-top: 20px;
            max-width: 340px;
            margin-left: auto;
        }
        .summary td:first-child {
            width: 60%;
        }
        .signatures {
            margin-top: 42px;
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }
        .signature-box {
            border-top: 1px dashed #94a3b8;
            padding-top: 8px;
            min-height: 96px;
        }
        .signature-label {
            font-size: 12px;
            color: #475569;
            font-weight: 600;
            margin-bottom: 42px;
        }
        .signature-name {
            font-size: 12px;
            color: #64748b;
        }
        .invoice-footer {
            margin-top: 28px;
            text-align: center;
            font-size: 11px;
            color: #64748b;
        }
        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .status.paid {
            background: #dcfce7;
            color: #166534;
        }
        .status.unpaid {
            background: #fef3c7;
            color: #92400e;
        }
        .toolbar {
            max-width: 840px;
            margin: 20px auto 0;
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }
        .btn {
            border: 1px solid #cbd5e1;
            background: white;
            color: #0f172a;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
        }
        .btn.primary {
            border-color: #02803d;
            background: #02803d;
            color: white;
        }
        @media print {
            body {
                background: #fff;
            }
            .toolbar {
                display: none;
            }
            .page {
                margin: 0;
                border: none;
                border-radius: 0;
                max-width: none;
                padding: 0;
            }
        }
    </style>
</head>
<body>
    @php
        $totalPrice = (float) $booking->markup_price;
        $totalDp = (float) $booking->dp_amount;
    @endphp

    <div class="toolbar">
        <button class="btn" onclick="window.close()">Tutup</button>
        <button class="btn primary" onclick="window.print()">Cetak Invoice</button>
    </div>

    <div class="page">
        <div class="brand">
            @if(filled(setting('header_logo_image')))
                <img src="{{ setting('header_logo_image') }}" alt="Logo MultiBus">
            @else
                <strong>MultiBus</strong>
            @endif
        </div>

        <div class="header">
            <div>
                <h1 class="title">Invoice Booking Unit</h1>
                <p class="muted">Kode: <strong>{{ $booking->booking_code }}</strong></p>
                <p class="muted">Tanggal Cetak: {{ now()->format('d M Y H:i') }}</p>
            </div>
            <span class="status {{ $booking->is_cancelled ? 'unpaid' : ($booking->is_paid ? 'paid' : 'unpaid') }}">
                {{ $booking->is_cancelled ? 'DIBATALKAN' : ($booking->is_paid ? 'LUNAS' : 'BELUM LUNAS') }}
            </span>
        </div>

        <table>
            <tbody>
                <tr>
                    <th>Tanggal Berangkat</th>
                    <td>{{ optional($booking->departure_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Nama Customer</th>
                    <td>{{ $booking->customer_name }}</td>
                </tr>
                <tr>
                    <th>No HP Customer</th>
                    <td>{{ $booking->customer_phone }}</td>
                </tr>
                <tr>
                    <th>Jam Penjemputan</th>
                    <td>{{ $booking->pickup_time }}</td>
                </tr>
                <tr>
                    <th>Berangkat Dari</th>
                    <td>{{ $booking->departure_from }}</td>
                </tr>
                <tr>
                    <th>Tujuan</th>
                    <td>{{ $booking->destination }}</td>
                </tr>
                <tr>
                    <th>Tanggal Pulang</th>
                    <td>{{ optional($booking->return_date)->format('d M Y') }}</td>
                </tr>
                <tr>
                    <th>Jenis Layanan</th>
                    <td>
                        {{ $booking->service_type }}
                        @if($booking->service_type === 'DLL' && filled($booking->service_type_note))
                            <div class="muted">{{ $booking->service_type_note }}</div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>PO Yang Dipilih</th>
                    <td>{{ filled($booking->po_key) ? gallery_po_label($booking->po_key, $booking->po_key) : '-' }}</td>
                </tr>
                <tr>
                    <th>Unit Kendaraan</th>
                    <td>{{ $booking->gallery?->title ?? '-' }}</td>
                </tr>
            </tbody>
        </table>

        <table class="summary">
            <tbody>
                <tr>
                    <th>Total Harga</th>
                    <td><strong>Rp {{ number_format($totalPrice, 0, ',', '.') }}</strong></td>
                </tr>
                @if($booking->is_paid)
                    <tr>
                        <th>Status Pembayaran</th>
                        <td><strong>LUNAS</strong></td>
                    </tr>
                @else
                    <tr>
                        <th>Total DP</th>
                        <td><strong>Rp {{ number_format($totalDp, 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="signatures">
            <div class="signature-box">
                <p class="signature-label">Hormat kami,</p>
                <p class="signature-name">Admin MultiBus</p>
            </div>
            <div class="signature-box">
                <p class="signature-label">Pelanggan,</p>
                <p class="signature-name">{{ $booking->customer_name }}</p>
            </div>
        </div>

        <p class="invoice-footer">www.multibus.id</p>
    </div>
</body>
</html>
