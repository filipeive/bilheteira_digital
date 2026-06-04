<?php
namespace App\Livewire\Admin;

use App\Models\Ticket;
use App\Models\TicketBatch;
use App\Models\Event;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Reports extends Component
{
    public string $dateFrom    = '';
    public string $dateTo      = '';
    public string $filterBatch = '';
    public string $activeTab   = 'daily';

    public function mount(): void
    {
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
        $this->dateTo   = now()->format('Y-m-d');
    }

    // ── MÉTRICAS GLOBAIS ────────────────────────────────────
    public function getOverviewMetricsProperty(): array
    {
        $q = Ticket::whereBetween('created_at', [
            Carbon::parse($this->dateFrom)->startOfDay(),
            Carbon::parse($this->dateTo)->endOfDay(),
        ]);

        return [
            'total_tickets'    => (clone $q)->count(),
            'confirmed'        => (clone $q)->where('status', 'confirmed')->count(),
            'used'             => (clone $q)->where('status', 'used')->count(),
            'pending'          => (clone $q)->where('status', 'pending')->count(),
            'cancelled'        => (clone $q)->where('status', 'cancelled')->count(),
            'total_revenue'    => (clone $q)->whereIn('status', ['confirmed','used'])->sum('price'),
            'avg_ticket_price' => (clone $q)->whereIn('status', ['confirmed','used'])->avg('price') ?? 0,
        ];
    }

    // ── VENDAS POR DIA ──────────────────────────────────────
    public function getDailySalesProperty(): array
    {
        return Ticket::selectRaw('DATE(created_at) as date, COUNT(*) as total, SUM(price) as revenue')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ])
            ->whereIn('status', ['confirmed', 'used'])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->map(fn($r) => [
                'date'    => Carbon::parse($r->date)->format('d/m'),
                'total'   => $r->total,
                'revenue' => $r->revenue,
            ])
            ->toArray();
    }

    // ── VENDAS POR LOTE ─────────────────────────────────────
    public function getByBatchProperty(): array
    {
        return Ticket::selectRaw('ticket_type, COUNT(*) as total, SUM(price) as revenue')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ])
            ->whereIn('status', ['confirmed', 'used'])
            ->groupBy('ticket_type')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    // ── VENDAS POR FORMA DE PAGAMENTO ───────────────────────
    public function getByPaymentProperty(): array
    {
        return Ticket::selectRaw('payment_method, COUNT(*) as total, SUM(price) as revenue')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ])
            ->whereIn('status', ['confirmed', 'used'])
            ->groupBy('payment_method')
            ->orderByDesc('total')
            ->get()
            ->toArray();
    }

    // ── VENDAS POR MODO (online vs presencial) ──────────────
    public function getByModeProperty(): array
    {
        return Ticket::selectRaw('ticket_mode, COUNT(*) as total, SUM(price) as revenue')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ])
            ->whereIn('status', ['confirmed', 'used'])
            ->groupBy('ticket_mode')
            ->get()
            ->toArray();
    }

    // ── EXPORTAR CSV ────────────────────────────────────────
    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $tickets = Ticket::with('batch')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay(),
            ])
            ->orderByDesc('created_at')
            ->get();

        return response()->streamDownload(function () use ($tickets) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8

            fputcsv($handle, [
                'Código', 'Nome', 'Telefone', 'Email',
                'Tipo', 'Valor (MT)', 'Pagamento', 'Estado',
                'Modo', 'Data de Compra', 'Email Enviado', 'WhatsApp Enviado'
            ], ';');

            foreach ($tickets as $t) {
                fputcsv($handle, [
                    $t->ticket_code,
                    $t->buyer_name,
                    $t->buyer_phone ?? '-',
                    $t->buyer_email ?? '-',
                    $t->ticket_type,
                    $t->price,
                    $t->payment_method ?? '-',
                    $t->status,
                    $t->ticket_mode ?? 'personalized',
                    $t->created_at->format('d/m/Y H:i'),
                    $t->email_sent_at?->format('d/m/Y H:i') ?? 'Não',
                    $t->whatsapp_sent_at?->format('d/m/Y H:i') ?? 'Não',
                ], ';');
            }
            fclose($handle);
        }, 'relatorio-bilhetes-' . now()->format('Ymd-His') . '.csv');
    }

    // ── EXPORTAR PDF DO RELATÓRIO ───────────────────────────
    public function exportPdf()
    {
        $metrics  = $this->overviewMetrics;
        $byBatch  = $this->byBatch;
        $byPayment = $this->byPayment;
        $dateFrom = $this->dateFrom;
        $dateTo   = $this->dateTo;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report', compact(
            'metrics', 'byBatch', 'byPayment', 'dateFrom', 'dateTo'
        ))->setPaper('a4', 'portrait');

        return response()->streamDownload(
            fn() => print($pdf->output()),
            'relatorio-' . now()->format('Ymd') . '.pdf'
        );
    }

    public function render()
    {
        return view('livewire.admin.reports', [
            'metrics'   => $this->overviewMetrics,
            'daily'     => $this->dailySales,
            'byBatch'   => $this->byBatch,
            'byPayment' => $this->byPayment,
            'byMode'    => $this->byMode,
        ])->layout('layouts.admin', ['title' => 'Relatórios']);
    }
}
