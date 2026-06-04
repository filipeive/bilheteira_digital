<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f5; color: #1f2937; padding: 40px 20px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
        <div style="background-color: #1A1610; padding: 30px; text-align: center; border-bottom: 4px solid #D4AF37;">
            <h1 style="color: #D4AF37; margin: 0; font-size: 28px; text-transform: uppercase;">Concerto Renúncia</h1>
            <p style="color: #B8A890; margin: 10px 0 0 0;">O seu bilhete digital está pronto!</p>
        </div>

        <div style="padding: 30px;">
            <p style="font-size: 16px; margin-bottom: 20px;">Olá <strong>{{ $tickets[0]->buyer_name }}</strong>,</p>

            <p style="font-size: 16px; margin-bottom: 20px; line-height: 1.5;">Obrigado por confirmar a sua presença no Concerto Renúncia. O(s) seu(s) bilhete(s) {{ count($tickets) > 1 ? 'estão anexados' : 'está anexado' }} a este email em formato PDF.</p>

            <div style="background-color: #f3f4f6; border-radius: 6px; padding: 20px; margin-bottom: 20px;">
                <h3 style="margin-top: 0; margin-bottom: 15px; color: #374151;">Detalhes d{{ count($tickets) > 1 ? 'os Bilhetes' : 'o Bilhete' }}</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; width: 40%;">Códigos:</td>
                        <td style="padding: 8px 0; font-weight: bold; font-family: monospace; font-size: 16px;">
                            @foreach($tickets as $t)
                                {{ $t->ticket_code }} ({{ $t->getTicketTypeLabel() }})<br>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Data:</td>
                        <td style="padding: 8px 0; font-weight: bold;">{{ $tickets[0]->event->date->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280;">Local:</td>
                        <td style="padding: 8px 0; font-weight: bold;">{{ $tickets[0]->event->venue }}, {{ $tickets[0]->event->city }}</td>
                    </tr>
                </table>
            </div>

            <p style="font-size: 14px; color: #6b7280; text-align: center; margin-top: 30px;">
                Por favor, apresente o PDF anexado (no telemóvel ou impresso) na entrada do evento. O QR Code será digitalizado.
            </p>
        </div>

        <div style="background-color: #f9fafb; padding: 20px; text-align: center; font-size: 12px; color: #9ca3af; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0;">© {{ date('Y') }} Alpha Produções. Todos os direitos reservados.</p>
        </div>
    </div>
</body>
</html>
