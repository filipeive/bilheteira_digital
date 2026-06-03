<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Aguardar Confirmação</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; line-height: 1.6; padding: 20px;">
    <div style="max-width: 600px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); border-top: 4px solid #D4A017;">
        <h2 style="color: #D4A017; text-align: center; margin-bottom: 20px;">O seu pedido foi recebido!</h2>
        
        <p>Olá <strong>{{ $ticket->buyer_name }}</strong>,</p>
        
        <p>O seu pedido de bilhete para o <strong>{{ $ticket->event->name ?? 'Concerto Renúncia' }}</strong> foi registado com sucesso.</p>
        
        <div style="background: #f9f9f9; padding: 15px; border-radius: 6px; margin: 20px 0; border: 1px solid #eee;">
            <p style="margin: 5px 0;"><strong>Código do Bilhete:</strong> {{ $ticket->ticket_code }}</p>
            <p style="margin: 5px 0;"><strong>Tipo:</strong> {{ $ticket->getTicketTypeLabel() }}</p>
            <p style="margin: 5px 0;"><strong>Quantidade:</strong> 1</p>
            <p style="margin: 5px 0;"><strong>Valor:</strong> {{ $ticket->price }} MT</p>
        </div>
        
        <p><strong>E agora?</strong><br>
        O seu bilhete está no estado <span style="color: #E08A3A; font-weight: bold;">Pendente</span>. Assim que a nossa equipa confirmar o seu pagamento (Ref: {{ $ticket->payment_ref }} - {{ strtoupper($ticket->payment_method) }}), receberá um novo email com o seu bilhete final contendo o QR Code de entrada.</p>
        
        <p style="margin-top: 30px; font-size: 0.9em; color: #777;">
            Se tiver alguma dúvida, entre em contacto connosco através do WhatsApp: +258 87 541 1644.<br>
            Agradecemos a sua preferência!<br>
            Equipa Alpha Produções
        </p>
    </div>
</body>
</html>
