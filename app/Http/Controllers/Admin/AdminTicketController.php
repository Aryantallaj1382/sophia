<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\Message;
use App\Models\TicketMessage;
use Illuminate\Http\Request;
use function Symfony\Component\Translation\t;

class AdminTicketController extends Controller
{
    // لیست همه تیکت‌ها
    public function index(Request $request)
    {
        $query = Ticket::with('user')->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tickets = $query->paginate(10);

        return view('admin.tickets.index', compact('tickets'));
    }


    // نمایش یک تیکت خاص
    public function show($id)
    {
        $ticket = Ticket::with(['user', 'messages'])->findOrFail($id);
        return view('admin.tickets.show', compact('ticket'));
    }

    // ارسال پاسخ به تیکت
    public function reply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $ticket = Ticket::findOrFail($id);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'message' => $request->message,
            'is_support_reply' => true,
            'user_id' => $ticket->user_id,
        ]);

        $ticket->update(['status' => 'answered']);

        return redirect()->route('admin.tickets.show', $ticket->id)
            ->with('success', 'پاسخ شما ارسال شد.');
    }
}
