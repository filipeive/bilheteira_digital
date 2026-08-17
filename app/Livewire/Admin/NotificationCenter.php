<?php

namespace App\Livewire\Admin;

use App\Models\SystemNotification;
use Livewire\Component;

class NotificationCenter extends Component
{
    public function markAsRead(int $id)
    {
        $notification = SystemNotification::find($id);
        if ($notification) {
            $notification->update(['is_read' => true]);
            if ($notification->link) {
                return redirect($notification->link);
            }
        }
    }

    public function markAllAsRead()
    {
        SystemNotification::where('is_read', false)->update(['is_read' => true]);
    }

    public function render()
    {
        $unreadCount = SystemNotification::where('is_read', false)->count();
        $notifications = SystemNotification::latest()->take(8)->get();

        return view('livewire.admin.notification-center', compact('unreadCount', 'notifications'));
    }
}
