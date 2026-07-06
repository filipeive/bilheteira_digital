<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Ticket;
use App\Services\QrCodeService;

class ResequenceQuickSaleTickets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:resequence-quick-sale-tickets {--dry-run : Run without making actual changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Resequence all Quick Sale tickets chronologically and regenerate their QR payloads.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');
        if ($dryRun) {
            $this->warn("⚠️ RUNNING IN DRY-RUN MODE - NO CHANGES WILL BE SAVED");
        }

        $tickets = Ticket::where('buyer_name', 'like', 'Venda Rápida #%')
            ->orderBy('created_at', 'asc')
            ->get();

        $count = $tickets->count();
        $this->info("Found {$count} quick sale tickets to resequence.");

        if ($count === 0) {
            $this->info("Nothing to do.");
            return 0;
        }

        $qrService = app(QrCodeService::class);
        $changedCount = 0;

        foreach ($tickets as $index => $ticket) {
            $newNumber = $index + 1;
            $newName = "Venda Rápida #" . $newNumber;
            $oldName = $ticket->buyer_name;

            if ($oldName !== $newName) {
                $this->line("Ticket {$ticket->ticket_code} (created: {$ticket->created_at}): '{$oldName}' ➔ '{$newName}'");
                
                if (!$dryRun) {
                    // Update name first so generateSignedPayload uses the new name
                    $ticket->buyer_name = $newName;
                    $ticket->qr_payload = $qrService->generateSignedPayload($ticket);
                    $ticket->save();
                }
                $changedCount++;
            }
        }

        if ($dryRun) {
            $this->info("Dry-run complete. Would have updated {$changedCount} of {$count} tickets.");
        } else {
            $this->info("Successfully updated {$changedCount} of {$count} tickets.");
        }

        return 0;
    }
}
