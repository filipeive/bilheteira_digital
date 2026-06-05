<div>
    <h1 style="font-size: 2rem; color: var(--gold); margin-bottom: 24px;">AUDITORIA</h1>

    {{-- Filters --}}
    <div style="display: flex; gap: 12px; margin-bottom: 20px; flex-wrap: wrap;">
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Pesquisar por utilizador..." class="form-input" style="max-width: 300px;">
        <select wire:model.live="filterAction" class="form-select" style="max-width: 220px;">
            <option value="">Todas as acções</option>
            @foreach($actions as $action)
                <option value="{{ $action }}">{{ str_replace('_', ' ', $action) }}</option>
            @endforeach
        </select>
    </div>

    {{-- Table --}}
    <div style="background: var(--dark-card); border: 1px solid var(--dark-border); border-radius: 12px; overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Utilizador</th>
                    <th>Acção</th>
                    <th>Alvo</th>
                    <th>Detalhes da Modificação</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td><span class="mono" style="font-size: 0.8rem;">{{ $log->created_at->format('d/m H:i:s') }}</span></td>
                    <td>{{ $log->user?->name ?? '—' }}</td>
                    <td>
                        <span class="badge badge-gold">{{ str_replace('_', ' ', $log->action) }}</span>
                    </td>
                    <td>
                        @if($log->model_type)
                            <span style="font-size: 0.8rem; color: var(--text-muted);">{{ class_basename($log->model_type) }} #{{ $log->model_id }}</span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @php $changes = $log->getFormattedChanges(); @endphp
                        @if(!empty($changes))
                            <ul style="margin: 0; padding-left: 16px; list-style-type: disc; font-size: 0.85rem; color: var(--text-secondary); display: flex; flex-direction: column; gap: 4px;">
                                @foreach($changes as $change)
                                    <li>{!! $change !!}</li>
                                @endforeach
                            </ul>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.8rem;">—</span>
                        @endif
                    </td>
                    <td><span class="mono" style="font-size: 0.75rem; color: var(--text-muted);">{{ $log->ip_address }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align: center; padding: 40px; color: var(--text-muted);">Nenhum registo de auditoria.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">{{ $logs->links() }}</div>
</div>
