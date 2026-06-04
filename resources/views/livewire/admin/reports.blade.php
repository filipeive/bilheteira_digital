<div>
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <h1 style="font-size: 2.5rem; color: var(--gold);">RELATÓRIOS</h1>
            <p style="color: var(--text-muted);">Análise de vendas e receitas</p>
        </div>
    </div>

    {{-- FILTROS --}}
    <div class="report-filters">
        <div class="filter-group">
            <label class="filter-label">De</label>
            <input type="date" wire:model.live="dateFrom" class="filter-input">
        </div>
        <div class="filter-group">
            <label class="filter-label">Até</label>
            <input type="date" wire:model.live="dateTo" class="filter-input">
        </div>
        <div class="filter-actions">
            <button wire:click="exportCsv" class="btn-export">
                <i data-lucide="download" class="w-4 h-4"></i> CSV
            </button>
            <button wire:click="exportPdf" class="btn-export-pdf">
                <i data-lucide="file-text" class="w-4 h-4"></i> PDF
            </button>
        </div>
    </div>

    {{-- MÉTRICAS PRINCIPAIS --}}
    <div class="report-grid-4">
        <div class="report-stat gold">
            <div class="rs-icon"><i data-lucide="banknote" class="w-5 h-5"></i></div>
            <div class="rs-val">{{ number_format($metrics['total_revenue'], 0, ',', '.') }}</div>
            <div class="rs-label">Receita Total (MT)</div>
        </div>
        <div class="report-stat green">
            <div class="rs-icon"><i data-lucide="ticket" class="w-5 h-5"></i></div>
            <div class="rs-val">{{ $metrics['total_tickets'] }}</div>
            <div class="rs-label">Total Bilhetes</div>
        </div>
        <div class="report-stat blue">
            <div class="rs-icon"><i data-lucide="check-circle" class="w-5 h-5"></i></div>
            <div class="rs-val">{{ $metrics['confirmed'] + $metrics['used'] }}</div>
            <div class="rs-label">Confirmados + Usados</div>
        </div>
        <div class="report-stat orange">
            <div class="rs-icon"><i data-lucide="clock" class="w-5 h-5"></i></div>
            <div class="rs-val">{{ $metrics['pending'] }}</div>
            <div class="rs-label">Pendentes</div>
        </div>
    </div>

    {{-- TABS --}}
    <div class="report-tabs">
        <button wire:click="$set('activeTab','daily')"
                class="rtab {{ $activeTab === 'daily' ? 'active' : '' }}">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Vendas por Dia
        </button>
        <button wire:click="$set('activeTab','batch')"
                class="rtab {{ $activeTab === 'batch' ? 'active' : '' }}">
            <i data-lucide="layers" class="w-4 h-4"></i> Por Tipo de Bilhete
        </button>
        <button wire:click="$set('activeTab','payment')"
                class="rtab {{ $activeTab === 'payment' ? 'active' : '' }}">
            <i data-lucide="credit-card" class="w-4 h-4"></i> Por Pagamento
        </button>
        <button wire:click="$set('activeTab','mode')"
                class="rtab {{ $activeTab === 'mode' ? 'active' : '' }}">
            <i data-lucide="monitor-smartphone" class="w-4 h-4"></i> Online vs Presencial
        </button>
    </div>

    {{-- CONTEÚDO DAS TABS --}}
    @if($activeTab === 'daily')
        <div class="report-section">
            <canvas id="chart-daily" height="80"></canvas>
            <div class="report-table-wrap" style="margin-top: 20px;">
                <table class="report-table">
                    <thead><tr><th>Data</th><th>Bilhetes</th><th>Receita (MT)</th></tr></thead>
                    <tbody>
                        @forelse($daily as $d)
                        <tr>
                            <td>{{ $d['date'] }}</td>
                            <td>{{ $d['total'] }}</td>
                            <td>{{ number_format($d['revenue'], 0, ',', '.') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align: center; padding: 20px; color: var(--text-muted);">Sem dados neste período</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if($activeTab === 'batch')
        <div class="report-section">
            <div class="report-batch-grid">
                @forelse($byBatch as $b)
                <div class="batch-report-card">
                    <div class="brc-type">{{ ucfirst($b['ticket_type']) }}</div>
                    <div class="brc-count">{{ $b['total'] }}</div>
                    <div class="brc-revenue">{{ number_format($b['revenue'], 0, ',', '.') }} MT</div>
                    <div class="brc-bar">
                        @php $pct = $metrics['total_tickets'] > 0 ? ($b['total'] / $metrics['total_tickets']) * 100 : 0 @endphp
                        <div class="brc-fill" style="width: {{ $pct }}%"></div>
                    </div>
                    <div class="brc-pct">{{ round($pct) }}% do total</div>
                </div>
                @empty
                <p style="color: var(--text-muted); padding: 20px;">Sem dados neste período</p>
                @endforelse
            </div>
        </div>
    @endif

    @if($activeTab === 'payment')
        <div class="report-section">
            <table class="report-table">
                <thead><tr><th>Forma de Pagamento</th><th>Bilhetes</th><th>Receita (MT)</th><th>%</th></tr></thead>
                <tbody>
                    @forelse($byPayment as $p)
                    @php $pct = $metrics['total_tickets'] > 0 ? round(($p['total'] / $metrics['total_tickets']) * 100) : 0 @endphp
                    <tr>
                        <td>{{ ucfirst($p['payment_method'] ?? 'outro') }}</td>
                        <td>{{ $p['total'] }}</td>
                        <td>{{ number_format($p['revenue'], 0, ',', '.') }}</td>
                        <td>
                            <div class="inline-bar">
                                <div class="inline-fill" style="width: {{ $pct }}%"></div>
                                <span>{{ $pct }}%</span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" style="text-align: center; padding: 20px; color: var(--text-muted);">Sem dados neste período</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    @if($activeTab === 'mode')
        <div class="report-section">
            <div class="mode-grid">
                @forelse($byMode as $m)
                <div class="mode-card">
                    <div class="mode-icon">
                        @if(($m['ticket_mode'] ?? '') === 'quick_sale')
                            <i data-lucide="zap" class="w-8 h-8"></i>
                        @else
                            <i data-lucide="globe" class="w-8 h-8"></i>
                        @endif
                    </div>
                    <div class="mode-label">{{ ($m['ticket_mode'] ?? '') === 'quick_sale' ? 'Venda Rápida' : 'Online' }}</div>
                    <div class="mode-count">{{ $m['total'] }} bilhetes</div>
                    <div class="mode-revenue">{{ number_format($m['revenue'], 0, ',', '.') }} MT</div>
                </div>
                @empty
                <p style="color: var(--text-muted); padding: 20px;">Sem dados neste período</p>
                @endforelse
            </div>
        </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('livewire:navigated', initCharts);
    document.addEventListener('DOMContentLoaded', initCharts);

    function initCharts() {
        const canvas = document.getElementById('chart-daily');
        if (!canvas) return;

        const data = @json($daily);
        if (!data.length) return;

        const existing = Chart.getChart(canvas);
        if (existing) existing.destroy();

        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: data.map(d => d.date),
                datasets: [{
                    label: 'Bilhetes',
                    data: data.map(d => d.total),
                    backgroundColor: 'rgba(212,160,23,0.3)',
                    borderColor: '#D4A017',
                    borderWidth: 1,
                    borderRadius: 3,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            afterLabel: (ctx) => 'Receita: ' + data[ctx.dataIndex].revenue.toLocaleString('pt-MZ') + ' MT'
                        }
                    }
                },
                scales: {
                    x: { ticks: { color: 'rgba(240,232,213,0.4)', font: { family: 'JetBrains Mono', size: 10 } }, grid: { color: 'rgba(212,160,23,0.05)' } },
                    y: { ticks: { color: 'rgba(240,232,213,0.4)', font: { family: 'JetBrains Mono', size: 10 } }, grid: { color: 'rgba(212,160,23,0.05)' } }
                }
            }
        });
    }
    </script>
</div>
