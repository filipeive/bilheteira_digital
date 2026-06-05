<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Bilhete — {{ $ticket->event->name ?? 'Concerto Renúncia' }}</title>
    <style>
        /* ─── DOMPDF Page Setup ─── */
        @page {
            margin: 0px;
            size: 720px 250px;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            background: url('{{ public_path("images/ticket-bg-premium.png") }}');
            margin: 0px;
            padding: 0px;
            color: #ffffff;
            background-repeat: no-repeat;
            background-size: cover 100%;
            background-position: center;
        }

        /* ─── Wrapper do Bilhete ─── */
        .ticket-wrapper {
            width: 720px;
            height: 240px;
            position: absolute;
            top: 5px;
            left: 0;
            overflow: hidden;
            background: #0E0C15;
            border-radius: 16px;
        }

        /* ─── Elementos Absolutos (DomPDF Friendly) ─── */
        .bg-layer {
            position: absolute;
            top: 0;
            left: 0;
            width: 720px;
            height: 240px;
            z-index: 1;
            border-radius: 16px;
        }

        .tm-accent-bar {
            position: absolute;
            top: 0; left: 0;
            width: 4px; height: 240px;
            background: #C9A227;
            z-index: 2;
        }

        .punch-top {
            position: absolute;
            top: -12px;
            left: 541px;
            width: 24px;
            height: 24px;
            background: #ffffff;
            border-radius: 50%;
            z-index: 20;
        }

        .punch-bottom {
            position: absolute;
            bottom: -12px;
            left: 541px;
            width: 24px;
            height: 24px;
            background: #ffffff;
            border-radius: 50%;
            z-index: 20;
        }

        /* ─── Layout using Tables ─── */
        table.layout {
            width: 720px;
            height: 240px;
            border-collapse: collapse;
            table-layout: fixed;
            margin: 0;
            padding: 0;
            position: relative;
            z-index: 5;
        }

        table.layout td {
            vertical-align: top;
            box-sizing: border-box;
            padding: 0;
            margin: 0;
        }

        .col-main {
            position: absolute;
            left: 50px;
            width: 540px;
            height: 240px;
            top: -5px;
            margin-left: 50px;
            z-index: 10;
            transform: translateY(12px);
        }

        .col-perf {
            width: 26px;
            height: 240px;
            transform:  translateX(300px);
        }

        .col-stub {
            width: 154px;
            height: 240px;
            background: transparent;
            text-align: center;
            transform: translateY(12px) translateX(36px);
        }

        /* ─── Main Details ─── */
        .tm-inner {
            padding: 16px 16px 10px 30px;
        }

        table.tm-top {
            width: 100%;
            border-collapse: collapse;
        }

        .tm-brand {
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #C9A227;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .tm-event-name {
            font-size: 32px;
            font-weight: bold;
            color: #FFFFFF;
            text-transform: uppercase;
            line-height: 1.1;
            letter-spacing: 1px;
            margin-bottom: 5px;
            white-space: nowrap;
        }

        .tm-artists-table {
            border-collapse: collapse;
            border: none;
            margin-top: 8px;
        }

        .tm-artists-table td {
            border: none !important;
            padding: 0 !important;
            vertical-align: middle;
        }

        .tm-artist-img {
            width: 36px;
            height: 36px;
            border-radius: 18px;
            border: 1.5px solid #C9A227;
        }

        .tm-artists-text {
            font-size: 13px;
            font-weight: normal;
            color: #9A8E7A;
            letter-spacing: 3px;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Badge de data */
        .tm-date-badge {
            background: #171420;
            border: 1px solid rgba(201,162,39,0.32);
            border-radius: 10px;
            padding: 8px;
            text-align: center;
            width: 60px;
        }

        .tm-date-day {
            font-size: 34px;
            font-weight: bold;
            color: #F5D96B;
            line-height: 1;
            margin-bottom: 2px;
        }

        .tm-date-rest {
            font-size: 9px;
            font-weight: bold;
            color: #C9A227;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Bottom block */
        table.tm-bottom {
            width: 390px;
            border-collapse: collapse;
            margin-top: 15px;
            margin-left: 0;
            margin-right: auto;
        }
        
        table.tm-bottom td {
            border-right: 1px solid rgba(255,255,255,0.07);
            padding-right: 10px;
            padding-left: 10px;
            vertical-align: top;
        }
        
        table.tm-bottom td:first-child { padding-left: 0; }
        table.tm-bottom td:last-child { border-right: none; padding-right: 0; }

        .tm-type-pill {
            background: #C9A227;
            color: #0E0C15;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 3px 6px;
            border-radius: 4px;
            display: inline-block;
        }

        .tm-info-label {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #C9A227;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .tm-info-value {
            font-size: 13px;
            font-weight: bold;
            color: #FFFFFF;
        }

        /* ─── Perforation ─── */
        .perf-line {
            width: 1px;
            height: 220px;
            margin-top: 10px;
            margin-left: 12px;
            border-left: 2px dashed rgba(255,255,255,0.15);
        }

        /* ─── Stub ─── */
        .stub-inner {
            padding: 20px 14px 10px 14px;
            text-align: center;
        }

        .stub-title {
            font-size: 14px;
            font-weight: bold;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 3px;
        }

        .stub-date {
            font-size: 9px;
            color: #C9A227;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .stub-qr-wrapper {
            width: 80px;
            height: 80px;
            background: #FFFFFF;
            border-radius: 8px;
            padding: 4px;
            margin: 0 auto 6px auto;
        }

        .stub-qr-wrapper img {
            width: 80px;
            height: 80px;
        }

        .stub-code {
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
            color: #F5D96B;
            letter-spacing: 2px;
            background: #171420;
            border: 1px solid rgba(201,162,39,0.25);
            border-radius: 6px;
            padding: 3px 8px;
            display: inline-block;
            margin-bottom: 6px;
        }

        .stub-hint {
            font-size: 8px;
            color: #5E5340;
            text-align: center;
            line-height: 1.3;
            border-top: 1px dashed rgba(201,162,39,0.18);
            padding-top: 6px;
        }

        /* ─── Terms Section ─── */
        .tm-terms-section {
            text-align: center;
            padding-top: 6px;
        }

        .tm-terms-divider {
            width: 100%;
            height: 1px;
            border-top: 1px dashed rgba(201, 162, 39, 0.25);
            margin: 6px 0;
        }

        .tm-terms-label {
            color: #C9A227;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 2px;
            display: block;
        }

        .tm-terms-text {
            color: #9A8E7A;
            font-size: 7.5px;
            line-height: 1.3;
            margin: 0 auto;
            width: 440px;
            text-align: center;
        }

    </style>
</head>
<body>

    @php
        $date = \Carbon\Carbon::parse($ticket->event->date ?? '2026-07-11 18:00:00');
        $monthAbbr = mb_strtoupper(mb_substr($date->translatedFormat('F'), 0, 3));
        $doorsOpen = \App\Models\SiteSetting::getValue('event_doors_open', '16:00');
        $showTime = \App\Models\SiteSetting::getValue('event_show_time', '18:00');
    @endphp

    <div class="ticket-wrapper">
        <img src="{{ public_path('images/ticket-bg-premium.png') }}" class="bg-layer">
        
        <div class="tm-accent-bar"></div>
        <div class="punch-top"></div>
        <div class="punch-bottom"></div>

        <table class="layout">
            <tr>
                {{-- ══════════════════════════════
                     CORPO PRINCIPAL
                ══════════════════════════════ --}}
                <td class="col-main">
                    <div class="tm-inner">
                        <table class="tm-top">
                            <tr>
                                <td>
                                    <div class="tm-brand">
                                        {{ $ticket->event->organizer ?? 'Alpha Produções Apresenta:' }}
                                    </div>
                                    <div class="tm-event-name">
                                        {{ Str::limit($ticket->event->name ?? 'Concerto Renúncia', 30) }}
                                    </div>
                                    
                                    {{-- Artistas com Fotos em Círculos --}}
                                    <table class="tm-artists-table">
                                        <tr>
                                            <td style="padding-right: 8px !important;">
                                                <img src="{{ public_path('artists/abel-2.png') }}" class="tm-artist-img">
                                            </td>
                                            <td style="padding-right: 16px !important;">
                                                <img src="{{ public_path('artists/nair-2.jpg') }}" class="tm-artist-img">
                                            </td>
                                            <td class="tm-artists-text">
                                                {{ $ticket->event->artists ?? 'Abel Laste · Nair Nany' }}
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 100px; text-align: right; vertical-align: middle; transform: translateY(-20px) translateX(12px);">
                                    <div class="tm-date-badge">
                                        <div class="tm-date-day">{{ $date->format('d') }}</div>
                                        <div class="tm-date-rest">
                                            {{ $monthAbbr }} · {{ $showTime }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table class="tm-bottom" style="font-size: 18px;">
                            <tr>
                                <td style="width: 85px;">
                                    <div class="tm-info-label">Tipo</div>
                                    <div class="tm-type-pill">
                                        {{ mb_strtoupper($ticket->getTicketTypeLabel()) }}
                                    </div>
                                </td>
                                <td style="width: 140px;">
                                    <div class="tm-info-label">Titular</div>
                                    <div class="tm-info-value">
                                        {{ Str::limit($ticket->buyer_name, 18) }}
                                    </div>
                                </td>
                                <td style="width: 95px;">
                                    <div class="tm-info-label">Local</div>
                                    <div class="tm-info-value">
                                        {{ Str::limit($ticket->event->venue ?? 'Pavilhão', 14) }}
                                    </div>
                                </td>
                                <td style="width: 70px;">
                                    <div class="tm-info-label">Preço</div>
                                    <div class="tm-info-value">
                                        {{ number_format($ticket->price, 0, ',', '.') }} MT
                                    </div>
                                </td>
                            </tr>
                           <tr>
                               <td colspan="4" class="tm-terms-section">
                                   <div class="tm-terms-divider"></div>
                                   <div class="tm-terms-label">Termos &amp; Condições</div>
                                   <div class="tm-terms-text">
                                       Uma vez adquiridos, os bilhetes não podem ser trocados ou reembolsados, a menos que haja cancelamento ou adiamento do evento.
                                   </div>
                               </td>
                           </tr>
                        </table>
                    </div>
                </td>

                {{-- ══════════════════════════════
                     PERFURAÇÃO (picote)
                ══════════════════════════════ --}}
                <td class="col-perf">
                    <div class="perf-line"></div>
                </td>

                {{-- ══════════════════════════════
                     CANHOTO (STUB)
                ══════════════════════════════ --}}
                <td class="col-stub">
                    <div class="stub-inner">
                        <div class="stub-title">Entrada</div>
                        <div class="stub-date">
                            {{ $date->format('d/m/Y') }} <br> Portões abrem às {{ $doorsOpen }}
                        </div>

                        <div class="stub-qr-wrapper">
                            @isset($qrCode)
                                <img src="data:image/png;base64,{{ base64_encode($qrCode) }}" alt="QR Code">
                            @endisset
                        </div>

                        <div class="stub-code">{{ $ticket->ticket_code }}</div>

                        <div class="stub-hint">
                            Apresente este código na entrada.<br>
                            Bilhete único e intransferível.
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
