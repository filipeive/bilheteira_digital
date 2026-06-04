<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aguardar Confirmação</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-top: 4px solid #D4A017;">
        <h2 style="color: #D4A017; text-align: center; margin-bottom: 20px;">O seu pedido foi recebido!</h2>
        
        <p>Olá <strong>{{ $tickets[0]->buyer_name }}</strong>,</p>
        
        <p>O seu pedido de bilhete(s) para o <strong>{{ $tickets[0]->event->name ?? 'Concerto Renúncia' }}</strong> foi registado com sucesso.</p>
        
        <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #eee;">
            <p style="margin: 5px 0;"><strong>Quantidade:</strong> {{ count($tickets) }}</p>
            <p style="margin: 5px 0;"><strong>Valor Total:</strong> {{ collect($tickets)->sum('price') }} MT</p>
            <hr style="border: 0; border-top: 1px solid #ddd; margin: 10px 0;">
            @foreach($tickets as $t)
                <p style="margin: 2px 0;"><strong>{{ $t->ticket_code }}</strong> ({{ $t->getTicketTypeLabel() }})</p>
            @endforeach
        </div>
        
        <p><strong>E agora?</strong><br>
        O seu pedido está no estado <span style="color: #E08A3A; font-weight: bold;">Pendente</span>. Assim que a nossa equipa confirmar o seu pagamento (Ref: {{ $tickets[0]->payment_ref }} - {{ strtoupper($tickets[0]->payment_method) }}), receberá um novo email com o(s) seu(s) bilhete(s) contendo o QR Code de entrada.</p>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #777;">
            Se tiver alguma dúvida, entre em contacto connosco através do WhatsApp: +258 87 541 1644.<br>
            Agradecemos a sua preferência!<br>
            Equipa Alpha Produções
        </p>
    </div>
</body>
</html>
