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
            background: #ffffff;
            margin: 0px;
            padding: 0px;
            color: #ffffff;
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
            left: 511px;
            width: 24px;
            height: 24px;
            background: #ffffff;
            border-radius: 50%;
            z-index: 20;
        }

        .punch-bottom {
            position: absolute;
            bottom: -12px;
            left: 511px;
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
            width: 510px;
            height: 240px;
        }

        .col-perf {
            width: 26px;
            height: 240px;
        }

        .col-stub {
            width: 184px;
            height: 240px;
            background: transparent;
            text-align: center;
        }

        /* ─── Main Details ─── */
        .tm-inner {
            padding: 20px 26px 15px 30px;
        }

        table.tm-top {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .tm-brand {
            font-size: 10px;
            font-weight: bold;
            letter-spacing: 2px;
            color: #C9A227;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .tm-event-name {
            font-size: 32px;
            font-weight: bold;
            color: #FFFFFF;
            text-transform: uppercase;
            line-height: 1;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .tm-artists {
            font-size: 11px;
            font-weight: normal;
            color: #9A8E7A;
            letter-spacing: 3px;
            text-transform: uppercase;
        }

        /* Badge de data */
        .tm-date-badge {
            background: #171420;
            border: 1px solid rgba(201,162,39,0.32);
            border-radius: 10px;
            padding: 10px;
            text-align: center;
            width: 60px;
        }

        .tm-date-day {
            font-size: 38px;
            font-weight: bold;
            color: #F5D96B;
            line-height: 1;
            margin-bottom: 2px;
        }

        .tm-date-rest {
            font-size: 10px;
            font-weight: bold;
            color: #C9A227;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        /* Bottom block */
        table.tm-bottom {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            margin-top: 15px;
        }
        
        table.tm-bottom td {
            border-right: 1px solid rgba(255,255,255,0.07);
            padding-right: 12px;
            padding-left: 12px;
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
            padding: 4px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .tm-info-label {
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 1.5px;
            color: #C9A227;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        .tm-info-value {
            font-size: 13px;
            font-weight: bold;
            color: #FFFFFF;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
            padding: 18px 14px;
            text-align: center;
        }

        .stub-title {
            font-size: 16px;
            font-weight: bold;
            color: #FFFFFF;
            text-transform: uppercase;
            letter-spacing: 2.5px;
            margin-bottom: 4px;
        }

        .stub-date {
            font-size: 10px;
            color: #C9A227;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .stub-qr-wrapper {
            width: 90px;
            height: 90px;
            background: #FFFFFF;
            border-radius: 8px;
            padding: 5px;
            margin: 0 auto 12px auto;
        }

        .stub-qr-wrapper img {
            width: 90px;
            height: 90px;
        }

        .stub-code {
            font-family: monospace;
            font-size: 13px;
            font-weight: bold;
            color: #F5D96B;
            letter-spacing: 2px;
            background: #171420;
            border: 1px solid rgba(201,162,39,0.25);
            border-radius: 6px;
            padding: 6px 12px;
            display: inline-block;
            margin-bottom: 12px;
        }

        .stub-hint {
            font-size: 9px;
            color: #4D4535;
            text-align: center;
            line-height: 1.4;
            border-top: 1px dashed rgba(201,162,39,0.18);
            padding-top: 10px;
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
                                <td style="width: 370px;">
                                    <div class="tm-brand">
                                        {{ $ticket->event->organizer ?? 'Alpha Produções & Faith Apresentam' }}
                                    </div>
                                    <div class="tm-event-name">
                                        {{ Str::limit($ticket->event->name ?? 'Concerto Renúncia', 30) }}
                                    </div>
                                    <div class="tm-artists">
                                        {{ $ticket->event->artists ?? 'Abel Laste · Nair Nany' }}
                                    </div>
                                </td>
                                <td style="width: 84px; text-align: right; vertical-align: middle;">
                                    <div class="tm-date-badge">
                                        <div class="tm-date-day">{{ $date->format('d') }}</div>
                                        <div class="tm-date-rest">
                                            {{ $monthAbbr }} · {{ $showTime }}
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>

                        <table class="tm-bottom">
                            <!-- Linha 1: Badge Tipo (VIP, Normal, etc.) -->
                            <tr>
                                <td style="width: 100px; height: 20px;">
                                    <div class="tm-type-pill">
                                        {{ mb_strtoupper($ticket->getTicketTypeLabel()) }}
                                    </div>
                                </td>
                                <td style="width: 170px;"></td>
                                <td style="width: 110px;"></td>
                                <td style="width: 74px;"></td>
                            </tr>
                            
                            <!-- Linha 2: Labels -->
                            <tr>
                                <td style="padding-top: 5px;">
                                    <div class="tm-info-label">Tipo</div>
                                </td>
                                <td style="padding-top: 5px;">
                                    <div class="tm-info-label">Titular</div>
                                </td>
                                <td style="padding-top: 5px;">
                                    <div class="tm-info-label">Local</div>
                                </td>
                                <td style="padding-top: 5px;">
                                    <div class="tm-info-label">Preço</div>
                                </td>
                            </tr>

                            <!-- Linha 3: Valores dinâmicos da base de dados -->
                            <tr>
                                <td>
                                    <div class="tm-info-value" style="color:#F5D96B;">
                                        {{ $ticket->getTicketTypeLabel() }}
                                    </div>
                                </td>
                                <td>
                                    <div class="tm-info-value">
                                        {{ Str::limit($ticket->buyer_name, 22) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="tm-info-value">
                                        {{ Str::limit($ticket->event->venue ?? 'Pavilhão do Benfica', 16) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="tm-info-value">
                                        {{ number_format($ticket->price, 0, ',', '.') }} MT
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
                            {{ $date->format('d/m/Y') }} · Portas: {{ $doorsOpen }}
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
