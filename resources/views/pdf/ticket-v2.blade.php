<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bilhete Único - Concerto Renúncia</title>
    <style>
        @page {
            margin: 0px;
            size: 1400px 450px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0px;
            padding: 0px;
            background: #ffffff;
            color: #ffffff;
        }

        .ticket-wrapper {
            width: 1400px;
            height: 450px;
            position: absolute;
            top: 0;
            left: 0;
            overflow: hidden;
            background: #111;
        }

        /* Fundo do Corpo Principal */
        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 1050px;
            height: 450px;
            z-index: -3;
            object-fit: cover;
        }
        
        .dark-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 1050px;
            height: 450px;
            background: linear-gradient(90deg, rgba(15,15,15,0.95) 0%, rgba(15,15,15,0.8) 40%, rgba(15,15,15,0.6) 100%);
            z-index: -2;
        }

        /* Fundo do Stub */
        .stub-layer {
            position: absolute;
            top: 0;
            left: 1050px;
            width: 350px;
            height: 450px;
            background: #0A0A0A;
            z-index: -1;
            border-left: 3px dashed #D4AF37;
        }

        /* Efeito de Picote / Punch holes */
        .punch-top {
            position: absolute;
            top: -20px;
            left: 1030px;
            width: 40px;
            height: 40px;
            background: #ffffff;
            border-radius: 50%;
            z-index: 10;
        }
        
        .punch-bottom {
            position: absolute;
            bottom: -20px;
            left: 1030px;
            width: 40px;
            height: 40px;
            background: #ffffff;
            border-radius: 50%;
            z-index: 10;
        }

        /* Tabela Estrutural Principal */
        table.layout {
            width: 1400px;
            height: 450px;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
            padding: 0;
        }
        
        table.layout td {
            vertical-align: top;
            padding: 30px 40px;
            box-sizing: border-box;
        }
        
        .col-main { width: 1050px; height: 450px; }
        .col-stub { width: 350px; height: 450px; text-align: center; }

        /* Estilos do Corpo Principal */
        .date-box {
            text-align: center;
            border-right: 2px solid #D4AF37;
            padding-right: 25px;
            margin-right: 15px;
        }
        
        .date-day {
            font-size: 65px;
            font-weight: bold;
            color: #ffffff;
            line-height: 1;
        }
        
        .date-month-time {
            font-size: 16px;
            color: #D4AF37;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-top: 5px;
        }

        .brand {
            color: #D4AF37;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 8px;
        }
        
        .title {
            font-size: 42px;
            font-weight: bold;
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 5px;
        }
        
        .subtitle {
            font-size: 20px;
            color: #e0e0e0;
            letter-spacing: 5px;
        }
        
        .alpha-logo {
            height: 45px;
            opacity: 0.95;
        }

        /* Bloco Inferior de Informações */
        .info-bar {
            width: 100%;
            margin-top: 35px;
            background: linear-gradient(135deg, rgba(212,175,55,0.15) 0%, rgba(0,0,0,0) 100%);
            border: 1px solid rgba(212,175,55,0.3);
            border-radius: 12px;
            padding: 15px 25px;
        }
        
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            vertical-align: top;
            padding: 0 15px;
            border-right: 1px solid rgba(255,255,255,0.15);
        }
        
        .info-table td:first-child { padding-left: 0; }
        .info-table td:last-child { border-right: none; padding-right: 0; }
        
        .info-label {
            color: #D4AF37;
            font-size: 12px;
            font-weight: bold;
            letter-spacing: 1px;
            margin-bottom: 6px;
            text-transform: uppercase;
        }
        
        .info-value {
            font-size: 16px;
            font-weight: bold;
            color: #ffffff;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Estilos do Canhoto (Stub) */
        .qr-wrapper {
            background: #ffffff;
            padding: 10px;
            border-radius: 8px;
            display: inline-block;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        
        .qr-image {
            width: 150px;
            height: 150px;
            display: block;
        }
        
        .ticket-code {
            color: #D4AF37;
            font-size: 22px;
            font-weight: bold;
            font-family: monospace;
            letter-spacing: 2px;
            margin-bottom: 20px;
            background: rgba(212,175,55,0.1);
            padding: 6px 12px;
            border-radius: 4px;
            display: inline-block;
        }
        
        .stub-title {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }
        
        .stub-subtitle {
            color: #D4AF37;
            font-size: 13px;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        
        .validation-text {
            color: #a0a0a0;
            font-size: 11px;
            line-height: 1.4;
            border-top: 1px dashed rgba(212,175,55,0.3);
            padding-top: 15px;
            margin-top: auto;
        }
        
        .badge-type {
            background: #D4AF37;
            color: #000;
            padding: 5px 12px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>

    <div class="ticket-wrapper">
        <!-- Fundo Main -->
        <img src="{{ public_path('images/abel-1-small.png') }}" class="bg-layer">
        <div class="dark-overlay"></div>
        
        <!-- Fundo Stub + Picotado -->
        <div class="stub-layer"></div>
        <div class="punch-top"></div>
        <div class="punch-bottom"></div>

        <table class="layout">
            <tr>
                <!-- CORPO PRINCIPAL -->
                <td class="col-main">
                    
                    <table style="width: 100%;">
                        <tr>
                            <!-- Esquerda: Data -->
                            <td style="width: 130px; vertical-align: top;">
                                <div class="date-box">
                                    @php
                                        $date = \Carbon\Carbon::parse($ticket->event->date ?? '2026-07-11 18:00');
                                    @endphp
                                    <div class="date-day">{{ $date->format('d') }}</div>
                                    <div class="date-month-time">
                                        {{ mb_substr($date->translatedFormat('F'), 0, 3) }}<br>
                                        {{ $date->format('H:i') }}
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Centro: Título -->
                            <td style="vertical-align: top;">
                                <div class="brand">ALPHA PRODUÇÕES E FAITH APRESENTAM</div>
                                <div class="title">{{ Str::limit($ticket->event->name ?? 'CONCERTO RENÚNCIA', 35) }}</div>
                                <div class="subtitle">ABEL LASTE & NAIR NANY</div>
                            </td>

                            <!-- Direita: Logo -->
                            <td style="width: 120px; vertical-align: top; text-align: right;">
                                <img src="{{ public_path('alpha-logo-gold.png') }}" class="alpha-logo">
                            </td>
                        </tr>
                    </table>

                    <!-- Imagens dos Artistas -->
                    <div style="margin-top: 35px; height: 130px; overflow: hidden; white-space: nowrap;">
                        <img src="{{ public_path('images/nair-1-small.jpeg') }}" style="height: 130px; border-radius: 8px; border: 2px solid rgba(212,175,55,0.8); margin-right: 15px;">
                        <img src="{{ public_path('images/abel-2-small.png') }}" style="height: 130px; border-radius: 8px; border: 2px solid rgba(212,175,55,0.8);">
                    </div>

                    <!-- Informações do Bilhete -->
                    <div class="info-bar">
                        <table class="info-table">
                            <tr>
                                <td style="width: 20%;">
                                    <div class="info-label">TIPO</div>
                                    <div class="info-value" style="color: #D4AF37;">{{ mb_strtoupper($ticket->getTicketTypeLabel()) }}</div>
                                </td>
                                <td style="width: 40%;">
                                    <div class="info-label">TITULAR DO BILHETE</div>
                                    <div class="info-value">{{ Str::limit($ticket->buyer_name, 25) }}</div>
                                </td>
                                <td style="width: 25%;">
                                    <div class="info-label">LOCAL</div>
                                    <div class="info-value">{{ Str::limit($ticket->event->venue ?? 'Pavilhão do Benfica', 20) }}</div>
                                </td>
                                <td style="width: 15%;">
                                    <div class="info-label">PREÇO</div>
                                    <div class="info-value">{{ number_format($ticket->price, 0, ',', '.') }} MT</div>
                                </td>
                            </tr>
                        </table>
                    </div>

                </td>

                <!-- CANHOTO (STUB) -->
                <td class="col-stub">
                    <div class="badge-type">BILHETE {{ mb_strtoupper($ticket->getTicketTypeLabel()) }}</div>
                    
                    <div class="stub-title">Entrada</div>
                    <div class="stub-subtitle">{{ $date->format('d/m/Y') }} · {{ $date->format('H:i') }}</div>

                    <div class="qr-wrapper">
                        <img src="data:image/png;base64,{!! base64_encode($qrCode) !!}" class="qr-image">
                    </div>
                    
                    <div class="ticket-code">{{ $ticket->ticket_code }}</div>
                    
                    <div class="validation-text">
                        Apresente este código na entrada.<br>
                        Bilhete único. A duplicação<br>implica perda de validade.
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
