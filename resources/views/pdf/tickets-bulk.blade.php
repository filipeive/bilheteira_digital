<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bilhetes - Concerto Renúncia</title>
    <style>
        @page {
            margin: 0px;
            size: 1300px 500px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0px;
            padding: 0px;
            background: #ffffff;
        }

        .page-container {
            width: 1300px;
            height: 500px;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        .page-container:last-child {
            page-break-after: auto;
        }

        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 1300px;
            height: 500px;
            z-index: -2;
            object-fit: cover;
        }

        .dark-overlay {
            position: absolute;
            top: 0;
            left: 360px;
            width: 940px;
            height: 500px;
            background: rgba(0, 0, 0, 0.85);
            z-index: -1;
        }
        
        .artists-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 360px;
            height: 500px;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        table.layout {
            width: 1300px;
            height: 500px;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
            padding: 0;
        }

        table.layout td {
            vertical-align: middle;
            padding: 15px 25px;
            box-sizing: border-box;
        }

        .col-artists {
            width: 360px;
            text-align: right;
            padding-right: 35px;
        }

        .col-info {
            width: 600px;
            color: #ffffff;
            padding-left: 40px;
        }

        .col-qr {
            width: 340px;
            background: rgba(17, 17, 17, 0.95);
            color: #ffffff;
            text-align: center;
            border-left: 2px dashed rgba(212,175,55,0.4);
        }

        .artist-img {
            max-height: 180px;
            max-width: 180px;
            border-radius: 12px;
            border: 3px solid #D4AF37;
            margin: 5px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.6);
        }

        .brand {
            color: #D4AF37;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 10px;
        }

        .title {
            font-size: 38px;
            font-weight: bold;
            margin-bottom: 10px;
            text-transform: uppercase;
            line-height: 1.1;
        }

        .subtitle {
            font-size: 20px;
            color: #D4AF37;
            letter-spacing: 5px;
            margin-bottom: 15px;
        }

        .badge {
            background: #D4AF37;
            color: #000;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 18px;
            display: inline-block;
            margin-right: 15px;
        }

        .qr-image {
            width: 190px;
            height: 190px;
            background: #fff;
            padding: 8px;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .ticket-code {
            color: #D4AF37;
            font-size: 24px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 3px;
            margin-bottom: 5px;
        }
        
        .terms {
            font-size: 10px;
            color: #888;
            margin-top: 15px;
            text-align: center;
            line-height: 1.4;
            padding: 0 15px;
        }
    </style>
</head>
<body>

    @foreach($tickets as $ticket)
    <div class="page-container">
        <!-- Background image layer -->
        <img src="{{ public_path('images/abel-1-small.png') }}" class="bg-layer">
        
        <!-- Overlays -->
        <div class="artists-overlay"></div>
        <div class="dark-overlay"></div>

        <!-- Content Table -->
        <table class="layout" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <!-- LEFT: ARTISTS -->
                <td class="col-artists">
                    <img src="{{ public_path('images/nair-1-small.jpeg') }}" class="artist-img"><br>
                    <img src="{{ public_path('images/abel-2-small.png') }}" class="artist-img">
                </td>
                
                <!-- MIDDLE: INFO -->
                <td class="col-info">
                    <div class="brand">ALPHA PRODUÇÕES E FAITH APRESENTAM</div>
                    <div class="title">{{ $ticket->event->name ?? 'CONCERTO RENÚNCIA' }}</div>
                    <div class="subtitle">ABEL LASTE & NAIR NANY</div>

                    <table style="width: 100%; color: #fff; margin-bottom: 15px; border-collapse: collapse;">
                        <tr>
                            <td style="padding-bottom: 10px; width: 50%;">
                                <div style="color: #D4AF37; font-size: 13px; font-weight: bold; letter-spacing: 1px; margin-bottom: 4px;">DATA E HORA</div>
                                <div style="font-size: 16px; font-weight: bold;">{{ \Carbon\Carbon::parse($ticket->event->date)->translatedFormat('d \d\e F Y, H\hi') }}</div>
                            </td>
                            <td style="padding-bottom: 10px; width: 50%;">
                                <div style="color: #D4AF37; font-size: 13px; font-weight: bold; letter-spacing: 1px; margin-bottom: 4px;">LOCAL</div>
                                <div style="font-size: 16px; font-weight: bold;">{{ $ticket->event->venue ?? 'Pavilhão do Benfica' }}, {{ $ticket->event->city ?? 'Quelimane' }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="color: #D4AF37; font-size: 13px; font-weight: bold; letter-spacing: 1px; margin-bottom: 4px;">TITULAR DO BILHETE</div>
                                <div style="font-size: 16px; font-weight: bold;">{{ Str::limit($ticket->buyer_name, 25) }}</div>
                            </td>
                            <td>
                                <div style="color: #D4AF37; font-size: 13px; font-weight: bold; letter-spacing: 1px; margin-bottom: 4px;">APOIO AO CLIENTE</div>
                                <div style="font-size: 16px; font-weight: bold;">+258 87 541 1644</div>
                            </td>
                        </tr>
                    </table>
                    
                    <div style="margin-top: 10px;">
                        <div class="badge">BILHETE {{ $ticket->getTicketTypeLabel() }}</div>
                        <div class="badge">PREÇO: {{ number_format($ticket->price, 0, ',', '.') }} MT</div>
                    </div>
                </td>

                <!-- RIGHT: QR & VALIDATION -->
                <td class="col-qr">
                    <img src="data:image/png;base64,{!! base64_encode($qrCodeService->generateQrPng($ticket, 300)) !!}" class="qr-image">
                    
                    <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                    
                    <div style="color: #D4AF37; font-weight: bold; font-size: 18px; text-transform: uppercase;">
                        VALIDAÇÃO NA ENTRADA
                    </div>

                    <div class="terms">
                        O bilhete é único. A duplicação implica perda de validade.<br>
                        Não reembolsável exceto em caso de cancelamento.
                    </div>
                </td>
            </tr>
        </table>
    </div>
    @endforeach

</body>
</html>
