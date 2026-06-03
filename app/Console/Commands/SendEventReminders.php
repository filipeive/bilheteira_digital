<?php

namespace App\Console\Commands;

use App\Jobs\SendEventReminderJob;
use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Console\Command;

class SendEventReminders extends Command
{
    protected $signature = 'app:send-reminders';
    protected $description = 'Send event reminders via WhatsApp 24h before the event';

    public function handle()
    {
        $events = Event::where('is_active', true)
            ->whereDate('date', now()->addDay()->toDateString())
            ->get();

        if ($events->isEmpty()) {
            $this->info('No events found for tomorrow.');
            return;
        }

        foreach ($events as $event) {
            $this->info("Sending reminders for event: {$event->name}");
            
            $tickets = Ticket::where('event_id', $event->id)
                ->where('status', 'confirmed')
                ->whereNotNull('buyer_phone')
                ->whereNull('reminder_sent_at')
                ->get();

            $count = 0;
            foreach ($tickets as $ticket) {
                SendEventReminderJob::dispatch($ticket);
                $count++;
            }
            
            $this->info("Dispatched {$count} reminder jobs for event {$event->id}.");
        }
    }
}
