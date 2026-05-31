<div>
    <div style="margin-bottom: 32px; padding: 24px; border: 1px solid rgba(212,175,55,0.16); border-radius: 18px; background: linear-gradient(135deg, rgba(212,175,55,0.12), rgba(16,185,129,0.04)); display: flex; justify-content: space-between; gap: 16px; align-items: center; flex-wrap: wrap;">
        <div>
            <span class="badge badge-gold"><i data-lucide="layout-dashboard" class="w-4 h-4" style="margin-right: 6px;"></i> Operação</span>
            <h1 style="font-size: 2.8rem; color: var(--gold); margin-top: 8px;">DASHBOARD</h1>
            <p style="color: var(--text-secondary);">Visão geral do Concerto Renúncia</p>
        </div>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <a href="{{ route('admin.site') }}" class="btn-outline"><i data-lucide="settings" class="w-4 h-4"></i> Gerir site</a>
            <a href="{{ route('tickets.lookup.form') }}" target="_blank" class="btn-outline"><i data-lucide="search" class="w-4 h-4"></i> Consultar</a>
        </div>
    </div>

    <!-- Stats Grid -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 32px;">
        <div class="stat-card">
            <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><i data-lucide="ticket" class="w-4 h-4"></i> Total Bilhetes</p>
            <p class="mono" style="font-size: 2rem; font-weight: 700; color: var(--text-primary);">{{ number_format($this->stats['total']) }}</p>
        </div>
        <div class="stat-card" style="border-left: 3px solid #10B981;">
            <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><i data-lucide="check-circle" class="w-4 h-4"></i> Confirmados</p>
            <p class="mono" style="font-size: 2rem; font-weight: 700; color: #34D399;">{{ number_format($this->stats['confirmed']) }}</p>
        </div>
        <div class="stat-card" style="border-left: 3px solid #F59E0B;">
            <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><i data-lucide="alert-triangle" class="w-4 h-4"></i> Pendentes</p>
            <p class="mono" style="font-size: 2rem; font-weight: 700; color: #FBBF24;">{{ number_format($this->stats['pending']) }}</p>
        </div>
        <div class="stat-card" style="border-left: 3px solid #3B82F6;">
            <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><i data-lucide="scan-line" class="w-4 h-4"></i> Usados</p>
            <p class="mono" style="font-size: 2rem; font-weight: 700; color: #60A5FA;">{{ number_format($this->stats['used']) }}</p>
        </div>
        <div class="stat-card" style="border-left: 3px solid #EF4444;">
            <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><i data-lucide="x-circle" class="w-4 h-4"></i> Cancelados</p>
            <p class="mono" style="font-size: 2rem; font-weight: 700; color: #F87171;">{{ number_format($this->stats['cancelled']) }}</p>
        </div>
        <div class="stat-card" style="border-left: 3px solid var(--gold);">
            <p style="color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; margin-bottom: 4px; display: flex; align-items: center; gap: 6px;"><i data-lucide="banknote" class="w-4 h-4"></i> Receita Total</p>
            <p class="mono" style="font-size: 2rem; font-weight: 700; color: var(--gold);">{{ number_format($this->stats['revenue'], 0, ',', '.') }}<span style="font-size: 0.9rem; color: var(--text-muted);"> MT</span></p>
        </div>
    </div>

    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 32px;">
        <!-- Sales Line Chart -->
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
            <h3 style="font-size: 1.4rem; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;"><i data-lucide="trending-up" class="w-5 h-5" style="color: var(--gold);"></i> VENDAS (7 DIAS)</h3>
            <canvas id="salesChart" height="200"></canvas>
        </div>

        <!-- Type Donut Chart -->
        <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
            <h3 style="font-size: 1.4rem; color: var(--text-primary); margin-bottom: 16px; display: flex; align-items: center; gap: 8px;"><i data-lucide="chart-pie" class="w-5 h-5" style="color: var(--gold);"></i> POR TIPO</h3>
            <canvas id="typeChart" height="200"></canvas>
            <div style="margin-top: 12px;">
                @foreach (['promotional' => ['Promocional', '#10B981'], 'second_lot' => ['2º Lote', '#3B82F6'], 'gate' => ['No Portão', '#F59E0B'], 'vip' => ['VIP', '#D4AF37'], 'free' => ['Gratuito', '#8B5CF6']] as $key => [$label, $color])
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <div style="width: 10px; height: 10px; border-radius: 50%; background: {{ $color }};"></div>
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">{{ $label }}: {{ $this->stats['by_type'][$key] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Recent Tickets -->
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; padding: 24px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h3 style="font-size: 1.4rem; color: var(--text-primary); display: flex; align-items: center; gap: 8px;"><i data-lucide="clock" class="w-5 h-5" style="color: var(--gold);"></i> BILHETES RECENTES</h3>
            <a href="{{ url('/admin/tickets') }}" style="color: var(--gold); font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 6px;">Ver todos <i data-lucide="arrow-right" class="w-4 h-4"></i></a>
        </div>

        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Preço</th>
                        <th>Status</th>
                        <th>Data</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($this->recentTickets as $ticket)
                        <tr>
                            <td><span class="mono" style="color: var(--gold); font-size: 0.85rem;">{{ $ticket->ticket_code }}</span></td>
                            <td style="color: var(--text-primary);">{{ $ticket->buyer_name }}</td>
                            <td><span class="badge badge-gold">{{ $ticket->getTicketTypeLabel() }}</span></td>
                            <td class="mono">{{ number_format($ticket->price, 0, ',', '.') }} MT</td>
                            <td>
                                <span class="badge badge-{{ $ticket->getStatusColor() }}">{{ $ticket->getStatusLabel() }}</span>
                            </td>
                            <td style="font-size: 0.8rem;">{{ $ticket->created_at->format('d/m H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">
                                Nenhum bilhete registado ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Sales chart
            const salesData = @json($this->salesByDay);
            if (document.getElementById('salesChart')) {
                new Chart(document.getElementById('salesChart'), {
                    type: 'line',
                    data: {
                        labels: salesData.map(d => d.date),
                        datasets: [{
                            label: 'Bilhetes',
                            data: salesData.map(d => d.count),
                            borderColor: '#D4AF37',
                            backgroundColor: 'rgba(212, 175, 55, 0.1)',
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#D4AF37',
                            pointRadius: 4,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: { ticks: { color: '#8A7D6B' }, grid: { color: 'rgba(61,54,42,0.3)' } },
                            y: { ticks: { color: '#8A7D6B' }, grid: { color: 'rgba(61,54,42,0.3)' }, beginAtZero: true }
                        }
                    }
                });
            }

            // Type donut
            const typeData = @json($this->stats['by_type']);
            if (document.getElementById('typeChart')) {
                new Chart(document.getElementById('typeChart'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Promocional', '2º Lote', 'No Portão', 'VIP', 'Gratuito'],
                        datasets: [{
                            data: [typeData.promotional, typeData.second_lot, typeData.gate, typeData.vip, typeData.free],
                            backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#D4AF37', '#8B5CF6'],
                            borderWidth: 0,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { display: false } },
                        cutout: '60%',
                    }
                });
            }
        });
    </script>

    <style>
        @media (max-width: 768px) {
            div[style*="grid-template-columns: 2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</div>
