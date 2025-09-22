<?php

namespace App\Http\Controllers\Api\Ticket;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketMessage;
use Illuminate\Http\Request;

class TicketController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required',
            'department' => 'required',
            'message' => 'required|string|min:5',
        ]);

        $ticket = Ticket::create([
            'ticket_number' => mt_rand(100000, 999999),
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'department' => $request->department,
            'status' => 'pending',
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_support_reply' => false,
        ]);

        return api_response($ticket->id ,'تیکت با موفقیت ایجاد شد.');
    }


    public function addMessage(Request $request, $id)
    {
        $request->validate([
            'message' => 'nullable|string|min:3',
            'file' => 'nullable|file|max:20480', // حداکثر 20 مگابایت
        ]);

        $ticket = Ticket::findOrFail($id);



        $filePath = null;
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('ticket_files', 'public'); // ذخیره در storage/app/public/ticket_files
        }

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'message' => $request->message,
            'is_support_reply' => false,
            'file' => $filePath,
        ]);

        $ticket->update(['status' => 'pending']);

        return api_response([],'پیام شما با موفقیت ثبت شد.');
    }

    public function userTickets()
    {
        $tickets = Ticket::where('user_id', auth()->id())
            ->with(['messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->latest()
            ->paginate(10);

        $startIndex = ($tickets->currentPage() - 1) * $tickets->perPage() + 1;

        $tickets->setCollection(
            $tickets->getCollection()->map(function ($ticket) use (&$startIndex) {
                return [
                    'row_number' => $startIndex++,
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'subject' => $ticket->subject,
                    'department' => $ticket->department,
                    'status' => $ticket->status,
                    'created_at' => $ticket->created_at,
                    'last_message_at' => optional($ticket->messages->first())->created_at?->format('Y-m-d H:i'),
                ];
            })
        );

        return api_response($tickets);
    }



    public function showTicketConversation($id)
    {
        $ticket = Ticket::where('user_id', auth()->id())
            ->with(['messages' => function ($query) {
                $query->orderBy('created_at', 'asc'); // نمایش به ترتیب زمان
            }])
            ->findOrFail($id);

        return api_response([
            'id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'department' => $ticket->department,
            'status' => $ticket->status,
            'created_at' => $ticket->created_at->format('Y-m-d H:i'),
            'updated_at' => $ticket->updated_at->format('Y-m-d H:i'),
            'messages_count' => $ticket->messages->count(),

            'messages' => $ticket->messages->map(function ($message) {
                return [
                    'id' => $message->id,
                    'sender_name' => $message->user->name,
                    'is_support_reply' => $message->is_support_reply,
                    'message' => $message->message ?? null,
                    'file' => $message->file ? asset( $message->file) : null,
                    'sent_at' => $message->created_at->format('Y-m-d H:i'),
                ];
            }),
        ]);
    }


    public  function closeTicket($id)
    {
        $user = auth()->user();
        $ticket = Ticket::where('user_id',$user->id)->findOrFail($id);
        $ticket->update(['status' => 'closed']);

        return api_response([],'تیکت بسته شد');

    }




}

