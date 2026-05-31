<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bilhete - Concerto Renúncia</title>
    <style>
        @page {
            margin: 0px;
            size: 1000px 323px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0px;
            padding: 0px;
            background: #ffffff;
        }

        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 1000px;
            height: 323px;
            z-index: -2;
        }

        .dark-overlay {
            position: absolute;
            top: 0;
            left: 320px;
            width: 530px;
            height: 323px;
            background: rgba(0, 0, 0, 0.75);
            z-index: -1;
        }
        
        .artists-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 320px;
            height: 323px;
            background: rgba(0, 0, 0, 0.4);
            z-index: -1;
        }

        table.layout {
            width: 1000px;
            height: 323px;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
            padding: 0;
        }

        table.layout td {
            vertical-align: middle;
            padding: 20px;
            box-sizing: border-box;
        }

        .col-artists {
            width: 320px;
            text-align: right;
            padding-right: 30px;
        }

        .col-info {
            width: 530px;
            color: #ffffff;
        }

        .col-stub {
            width: 150px;
            background: #ffffff;
            color: #000000;
            text-align: center;
            border-left: 2px dashed #cccccc;
        }

        .artist-img {
            max-height: 120px;
            max-width: 120px;
            border-radius: 8px;
            border: 2px solid #D4AF37;
            margin: 5px;
        }

        .brand {
            color: #D4AF37;
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }

        .title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 16px;
            color: #D4AF37;
            letter-spacing: 4px;
            margin-bottom: 15px;
        }

        .badge {
            background: #D4AF37;
            color: #000;
            padding: 6px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 14px;
            display: inline-block;
            margin-right: 10px;
        }

        .outline-badge {
            border: 1px solid #D4AF37;
            color: #D4AF37;
            padding: 5px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 13px;
            display: inline-block;
            margin-bottom: 10px;
        }

        /* --- BACK --- */
        .ticket-back {
            background: #ffffff;
            color: #333333;
            width: 100%;
            height: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
            padding: 0;
        }

        .ticket-back td {
            vertical-align: top;
            box-sizing: border-box;
        }

        .col-qr {
            width: 320px;
            background: #111111;
            color: #ffffff;
            text-align: center;
            padding: 30px;
        }

        .col-terms {
            width: 680px;
            padding: 30px 40px;
        }

        .qr-image {
            width: 160px;
            height: 160px;
            background: #fff;
            padding: 5px;
            border-radius: 8px;
        }

        .ticket-code {
            color: #D4AF37;
            font-size: 20px;
            font-weight: bold;
            font-family: monospace;
            margin-top: 15px;
            letter-spacing: 2px;
        }

        .terms-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
            color: #111;
            text-transform: uppercase;
        }

        .terms-list {
            font-size: 13px;
            line-height: 1.5;
            padding-left: 20px;
        }

        .terms-list li {
            margin-bottom: 10px;
        }
        
        .page-container {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
            page-break-after: always;
        }

        .page-container:last-child {
            page-break-after: auto;
        }
    </style>
</head>
<body>

    <!-- FRONT PAGE -->
    <div class="page-container">
        <!-- Background image layer -->
        <img src="{{ public_path('images/abel-1-small.png') }}" class="bg-layer">
        
        <!-- Overlays -->
        <div class="artists-overlay"></div>
        <div class="dark-overlay"></div>

        <!-- Content Table -->
        <table class="layout" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="col-artists">
                    <img src="{{ public_path('images/nair-1-small.jpeg') }}" class="artist-img"><br>
                    <img src="{{ public_path('images/abel-2-small.png') }}" class="artist-img">
                </td>
                <td class="col-info">
                    <div class="brand">ALPHA PRODUÇÕES</div>
                    <div class="title">ABEL LASTE<br>& NAIR NANY</div>
                    <div class="subtitle">CONCERTO RENÚNCIA</div>

                    <div class="outline-badge">11 DE JULHO DE 2026 @ 16H00</div><br>
                    <div class="outline-badge">{{ Str::limit($ticket->buyer_name, 25) }}</div><br>
                    
                    <div style="margin-top: 10px;">
                        <div class="badge">BILHETE {{ $ticket->getTicketTypeLabel() }}</div>
                        <div class="badge">PREÇO: {{ number_format($ticket->price, 0, ',', '.') }} MT</div>
                    </div>
                </td>
                <td class="col-stub">
                    <div style="font-weight: bold; font-size: 24px; color: #333; letter-spacing: 5px;">
                        V<br>A<br>L<br>I<br>D<br>A<br>R
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <!-- BACK PAGE -->
    <div class="page-container">
        <table class="ticket-back" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td class="col-qr">
                    <img src="data:image/png;base64,{!! base64_encode($qrCode) !!}" class="qr-image">
                    <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                    <div style="margin-top: 10px; font-size: 14px; text-transform: uppercase;">{{ Str::limit($ticket->buyer_name, 20) }}</div>
                    <div style="margin-top: 5px; color: #D4AF37; font-weight: bold;">BILHETE {{ $ticket->getTicketTypeLabel() }}</div>
                </td>
                <td class="col-terms">
                    <div class="terms-title">Termos e condições</div>
                    <ul class="terms-list">
                        <li>Uma vez adquiridos os bilhetes não podem ser trocados ou reembolsados a menos que haja cancelamento ou adiamento do evento.</li>
                        <li>A gerência não se responsabiliza por qualquer perda, lesão ou dano a pessoas ou propriedades nas instalações.</li>
                        <li>O promotor reserva o direito de alterar o programa conforme necessário.</li>
                        <li>Admissão só através de bilhete completo e válido, com QR code perfeitamente visível.</li>
                        <li>O bilhete é único e será lido à entrada. A sua duplicação implica perda de validade e proibição de entrada.</li>
                    </ul>
                    <div style="text-align: right; margin-top: 5px;">
                        <img src="{{ public_path('alpha-logo-gold.png') }}" style="height: 40px; opacity: 0.8;">
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
