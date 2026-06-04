<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bilhete Único - Concerto Renúncia</title>
    <style>
        @page {
            margin: 0px;
            size: 1400px 550px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0px;
            padding: 0px;
            background: #ffffff;
        }

        .page-container {
            width: 1400px;
            height: 550px;
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
        }

        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 1400px;
            height: 550px;
            z-index: -2;
            object-fit: cover;
        }

        .dark-overlay {
            position: absolute;
            top: 0;
            left: 360px;
            width: 1040px;
            height: 550px;
            background: rgba(0, 0, 0, 0.85);
            z-index: -1;
        }
        
        .artists-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 360px;
            height: 550px;
            background: rgba(0, 0, 0, 0.3);
            z-index: -1;
        }

        table.layout {
            width: 1400px;
            height: 550px;
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
            width: 660px;
            color: #ffffff;
            padding-left: 40px;
        }

        .col-qr {
            width: 380px;
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
            font-size: 13px;
            color: rgba(255, 255, 255, 0.65);
            margin-top: 12px;
            line-height: 1.4;
        }
        
        .header-section {
            border-bottom: 1px solid rgba(212,175,55,0.3);
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .alpha-logo {
            height: 50px;
            opacity: 0.9;
        }

        .event-details {
            background: linear-gradient(135deg, rgba(212,175,55,0.1) 0%, rgba(0,0,0,0) 100%);
            border: 1px solid rgba(212,175,55,0.2);
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

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
                    <table style="width: 100%; border-bottom: 1px solid rgba(212,175,55,0.3); padding-bottom: 10px; margin-bottom: 15px;">
                        <tr>
                            <td>
                                <div class="brand">ALPHA PRODUÇÕES E FAITH APRESENTAM</div>
                                <div class="title">{{ $ticket->event->name ?? 'CONCERTO RENÚNCIA' }}</div>
                                <div class="subtitle">ABEL LASTE & NAIR NANY</div>
                            </td>
                            <td style="text-align: right; vertical-align: top;">
                                <img src="{{ public_path('alpha-logo-gold.png') }}" class="alpha-logo">
                            </td>
                        </tr>
                    </table>

                    <div class="event-details">
                        <table style="width: 100%; color: #fff; border-collapse: collapse;">
                            <tr>
                                <td style="padding-bottom: 12px; width: 50%;">
                                    <div style="color: #D4AF37; font-size: 13px; font-weight: bold; letter-spacing: 1px; margin-bottom: 4px;">DATA E HORA</div>
                                    <div style="font-size: 16px; font-weight: bold;">{{ \Carbon\Carbon::parse($ticket->event->date)->translatedFormat('d \d\e F Y, H\hi') }}</div>
                                </td>
                                <td style="padding-bottom: 12px; width: 50%;">
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
                    </div>
                    
                    <div style="margin-top: 5px;">
                        <div class="badge">BILHETE {{ mb_strtoupper($ticket->getTicketTypeLabel()) }}</div>
                        <div class="badge" style="background: transparent; color: #D4AF37; border: 2px solid #D4AF37;">PREÇO: {{ number_format($ticket->price, 0, ',', '.') }} MT</div>
                    </div>
                </td>

                <!-- RIGHT: QR & VALIDATION -->
                <td class="col-qr">
                    <img src="data:image/png;base64,{!! base64_encode($qrCode) !!}" class="qr-image">
                    
                    <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                    
                    <div style="color: #D4AF37; font-weight: bold; font-size: 16px; text-transform: uppercase; margin-top: 10px;">
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

</body>
</html>
