<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<style>
  body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; margin: 0; padding: 20px; }
  .header { border-bottom: 2px solid #D4A017; padding-bottom: 12px; margin-bottom: 20px; }
  .header h1 { font-size: 22px; color: #0D0B07; margin: 0 0 4px; }
  .header p { font-size: 10px; color: #666; margin: 0; }
  .period { background: #f5f5f5; padding: 8px 12px; border-radius: 4px; margin-bottom: 20px; font-size: 11px; }
  .metrics { display: block; width: 100%; margin-bottom: 24px; }
  .metric { display: inline-block; width: 22%; margin-right: 2%; background: #0D0B07; color: #D4A017; padding: 12px; border-radius: 6px; text-align: center; }
  .metric-val { font-size: 22px; font-weight: bold; display: block; }
  .metric-label { font-size: 9px; color: rgba(240,232,213,0.6); text-transform: uppercase; letter-spacing: 0.08em; }
  h2 { font-size: 14px; color: #0D0B07; border-left: 3px solid #D4A017; padding-left: 8px; margin: 20px 0 10px; }
  table { width: 100%; border-collapse: collapse; font-size: 11px; }
  th { background: #0D0B07; color: #D4A017; padding: 8px 10px; text-align: left; font-size: 9px; text-transform: uppercase; letter-spacing: 0.06em; }
  td { padding: 8px 10px; border-bottom: 1px solid #eee; }
  tr:nth-child(even) td { background: #f9f9f9; }
  .footer { margin-top: 30px; padding-top: 12px; border-top: 1px solid #ddd; font-size: 9px; color: #999; text-align: center; }
</style>
</head>
<body>
<div class="header">
  <h1>Relatório de Bilhetagem — Concerto Renúncia 2026</h1>
  <p>Alpha Produções & Faith · Quelimane, Moçambique · Gerado em {{ now()->format('d/m/Y H:i') }}</p>
</div>

<div class="period">
  Período: {{ \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') }}
  até {{ \Carbon\Carbon::parse($dateTo)->format('d/m/Y') }}
</div>

<div class="metrics">
  <div class="metric">
    <span class="metric-val">{{ number_format($metrics['total_revenue'], 0, ',', '.') }}</span>
    <span class="metric-label">Receita (MT)</span>
  </div>
  <div class="metric">
    <span class="metric-val">{{ $metrics['total_tickets'] }}</span>
    <span class="metric-label">Total Bilhetes</span>
  </div>
  <div class="metric">
    <span class="metric-val">{{ $metrics['confirmed'] + $metrics['used'] }}</span>
    <span class="metric-label">Confirmados</span>
  </div>
  <div class="metric">
    <span class="metric-val">{{ $metrics['pending'] }}</span>
    <span class="metric-label">Pendentes</span>
  </div>
</div>

<h2>Vendas por Tipo de Bilhete</h2>
<table>
  <thead><tr><th>Tipo</th><th>Quantidade</th><th>Receita (MT)</th></tr></thead>
  <tbody>
    @foreach($byBatch as $b)
    <tr>
      <td>{{ ucfirst($b['ticket_type']) }}</td>
      <td>{{ $b['total'] }}</td>
      <td>{{ number_format($b['revenue'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<h2>Vendas por Forma de Pagamento</h2>
<table>
  <thead><tr><th>Pagamento</th><th>Quantidade</th><th>Receita (MT)</th></tr></thead>
  <tbody>
    @foreach($byPayment as $p)
    <tr>
      <td>{{ ucfirst($p['payment_method'] ?? 'outro') }}</td>
      <td>{{ $p['total'] }}</td>
      <td>{{ number_format($p['revenue'], 0, ',', '.') }}</td>
    </tr>
    @endforeach
  </tbody>
</table>

<div class="footer">
  Sistema Bilheteira Digital · Alpha Produções · Quelimane, Moçambique
</div>
</body>
</html>
