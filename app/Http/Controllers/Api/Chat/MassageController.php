<?php

namespace App\Http\Controllers\Api\Chat;


use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MassageController extends Controller
{
    public function sendMessage(Request $request, $receiverId)
    {
        $request->validate([
            'message' => 'nullable|string|max:255',
            'file' => 'nullable|file',
            'voice' => 'nullable',
        ], [], [
            'message' => 'پیام',
            'file' => 'فایل',
            'voice' => 'صوت',
        ]);
        $sender = auth()->user();
        $receiver = User::findOrFail($receiverId);

        $conversation = Conversation::firstOrCreate([
            'user1_id' => min($sender->id, $receiver->id),
            'user2_id' => max($sender->id, $receiver->id),
        ]);

        $data = [
            'sender_id' => $sender->id,
        ];
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('uploads/files', 'public');
            $data['file_path'] = $path;
            $data['message_type'] = 'file';
        } elseif ($request->hasFile('voice')) {
            $path = $request->file('voice')->store('uploads/voices', 'public');
            $data['voice_path'] = $path;
            $data['message_type'] = 'voice';
        } else {
            $data['message'] = $request->message;
            $data['message_type'] = 'text';
        }

        $message = $conversation->messages()->create($data);

//        event(new ChatUpdated($conversation->id, $sender->id, $receiver->id));
//        event(new ChatUpdated($conversation->id, $receiver->id, $sender->id));
//
//        broadcast(new MessageSent([
//            'user2_id' => $conversation->user1_id,
//            'user1_id' => $conversation->user2_id,
//            'message' => $message->message,
//            'message_type' => $message->message_type,
//            'file_path' => $message->file_path,
//            'voice_path' => $message->voice_path,
//            'sender_id' => $sender->id,
//            'conversation_id' => $conversation->id,
//        ]))->toOthers();

        return response()->json(['status' => 'Message sent']);
    }


    public function getMessagesWithUser($receiverId)
    {
        $authUserId = auth()->id();

        $user1 = min($authUserId, $receiverId);
        $user2 = max($authUserId, $receiverId);

        $conversation = Conversation::firstOrCreate([
            'user1_id' => $user1,
            'user2_id' => $user2,
        ]);

        $messages = $conversation->messages()
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(40);

        // تغییر collection داخل paginate
        $messages->setCollection(
            $messages->getCollection()
                ->map(function ($message) {
                    $fileType = null;
                    $fileSize = null;

                    if ($message->voice_path) {
                        $fileType = 'audio';
                        $fullPath = public_path(str_replace(url('/'), '', url($message->file_path)));
                        if (file_exists($fullPath)) {
                            $fileSize = $this->formatFileSize(filesize($fullPath));
                        }
                    } elseif ($message->file_path) {
                        $extension = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));

                        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp'])) {
                            $fileType = 'image';
                        } elseif (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac'])) {
                            $fileType = 'audio';
                        } elseif (in_array($extension, ['mp4', 'mov', 'avi', 'mkv'])) {
                            $fileType = 'video';
                        } else {
                            $fileType = 'file';
                        }

                        $fullPath = public_path(str_replace(url('/'), '', $message->file_path));
                        if (file_exists($fullPath)) {
                            $fileSize = $this->formatFileSize(filesize($fullPath));
                        }
                    }

                    return [
                        'id' => $message->id,
                        'text' => $message->message,
                        'sender_id' => $message->sender_id,
                        'message_type' => $message->message_type,
                        'file_path' => $message->file_path ? url($message->file_path) : null,
                        'voice_path' => $message->voice_path ? url($message->voice_path) : null,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'sent_at' => $message->created_at,
                    ];
                })
                ->reverse() // چت ها از قدیمی‌تر به جدیدتر باشند
                ->values()
        );

        return api_response($messages);
    }

    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            $bytes = $bytes . ' bytes';
        } elseif ($bytes == 1) {
            $bytes = $bytes . ' byte';
        } else {
            $bytes = '0 bytes';
        }

        return $bytes;
    }


    public function getMyChats()
    {
        $authUserId = auth()->id();
        $user = auth()->user();

        $conversations = Conversation::where('user1_id', $authUserId)
            ->orWhere('user2_id', $authUserId)
            ->with([
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
                'user1',
                'user2'
            ])
            ->paginate();

        $conversations->setCollection(
            $conversations->getCollection()->map(function ($conversation) use ($authUserId) {
                $otherUser = $conversation->user1_id == $authUserId ? $conversation->user2 : $conversation->user1;
                $lastMessage = $conversation->messages->first();


                $profile = $otherUser->profile ?? null;

                $lastMessageText = null;
                if ($lastMessage) {
                    $lastMessageText = match ($lastMessage->message_type) {
                        'text' => $lastMessage->message,
                        'file' => 'فایل',
                        'voice' => 'فایل صوتی',
                        default => 'پیام',
                    };
                }

                return [
                    'id' => $otherUser->id,
                    'user_name' => $otherUser->name,
                    'user_image' => $profile,
                    'last_message' => $lastMessageText,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at->timezone('Asia/Tehran')->format('H:i') : null,
                ];
            })
        );

        return api_response($conversations);
    }



    public function user_details($id)
    {
        $user = User::find($id);

        return api_response([
            'id' => $user ? $user->id : null,
//            'role'=>$role,
//            'role_id'=>$p,
            'name' => $user ? $user->name : null,
            'profile' => $user ?  $user->profile : null,
        ]);
    }



    public function chat_info(Request $request, $receiverId)
    {
        $authUserId = auth()->id();

        $user1 = min($authUserId, $receiverId);
        $user2 = max($authUserId, $receiverId);

        $conversation = Conversation::firstOrCreate([
            'user1_id' => $user1,
            'user2_id' => $user2,
        ]);

        $type = $request->input('type');

        $query = $conversation->messages()->with('sender')->orderBy('created_at');

        // فیلتر بر اساس نوع
        if ($type == 'image') {
            $query->whereNotNull('file_path')
                ->whereIn(DB::raw('LOWER(SUBSTRING_INDEX(file_path, ".", -1))'), ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp']);
        } elseif ($type == 'audio') {
            $query->where(function ($q) {
                $q->whereNotNull('voice_path')
                    ->orWhere(function ($q2) {
                        $q2->whereNotNull('file_path')
                            ->whereIn(DB::raw('LOWER(SUBSTRING_INDEX(file_path, ".", -1))'), [
                                'mp3', 'wav', 'ogg', 'm4a', 'aac', 'wma', 'flac', 'amr', 'opus', 'aiff', 'alac'
                            ]);
                    });
            });
        } elseif ($type == 'video') {
            $query->whereNotNull('file_path')
                ->whereIn(DB::raw('LOWER(SUBSTRING_INDEX(file_path, ".", -1))'), ['mp4', 'mov', 'avi', 'mkv']);
        } elseif ($type == 'file') {
            $query->whereNotNull('file_path')
                ->whereNotIn(DB::raw('LOWER(SUBSTRING_INDEX(file_path, ".", -1))'), [
                    'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp',
                    'mp3', 'wav', 'ogg', 'm4a', 'aac',
                    'mp4', 'mov', 'avi', 'mkv'
                ]);
        } elseif ($type == 'link') {
            $query->whereRaw('message REGEXP "^(http|https)://"');
        } else {
            return api_response([], 'نوع درخواستی نامعتبر است', 400);
        }

        $messages = $query->paginate(40);

        $messages->getCollection()->transform(function ($message) use ($type) {
            $baseData = [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'sent_at' => $message->created_at->toDateTimeString(),
            ];

            if (in_array($type, ['image', 'video', 'file'])) {
                $filePath = $message->file_path;
                $fileInfo = pathinfo($filePath);
                $fileName = $fileInfo['basename'] ?? null;
                $fileExtension = $fileInfo['extension'] ?? null;
                $fullPath = public_path($filePath);

                $fileSizeInBytes = file_exists($fullPath) ? filesize($fullPath) : 0;

                if ($fileSizeInBytes >= 1024 * 1024) {
                    $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB';
                } elseif ($fileSizeInBytes > 0) {
                    $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB';
                } else {
                    $fileSize = 'Unknown size';
                }

                $baseData['path'] = $filePath ? $filePath : null;
                $baseData['file_name'] = $fileName;
                $baseData['file_extension'] = $fileExtension;
                $baseData['file_size'] = $fileSize;
            } elseif ($type == 'audio') {
                $audioType = null;
                $path = null;

                if ($message->voice_path) {
                    $audioType = 'voice';
                    $path = $message->voice_path;
                } elseif ($message->file_path) {
                    $extension = strtolower(pathinfo($message->file_path, PATHINFO_EXTENSION));
                    if (in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'aac', 'wma', 'flac', 'amr', 'opus', 'aiff', 'alac'])) {
                        $audioType = 'audio_file';
                        $path = $message->file_path;
                    }
                }

                if ($path) {
                    $fileInfo = pathinfo($path);
                    $fileName = $fileInfo['basename'] ?? 'unknown';
                    $fileExtension = $fileInfo['extension'] ?? 'unknown';
                    $fileSizeInBytes = file_exists(public_path($path)) ? filesize(public_path($path)) : 0;

                    if ($fileSizeInBytes >= 1024 * 1024) {
                        $fileSize = round($fileSizeInBytes / (1024 * 1024), 2) . ' MB';
                    } elseif ($fileSizeInBytes > 0) {
                        $fileSize = round($fileSizeInBytes / 1024, 2) . ' KB';
                    } else {
                        $fileSize = 'Unknown size';
                    }

                    $senderName = auth()->id() == $message->sender_id ? 'شما' : ($message->sender->first_name ?? 'نامشخص');

                    $baseData['path'] = $path;
                    $baseData['audio_type'] = $audioType;
                    $baseData['file_name'] = $fileName;
                    $baseData['file_extension'] = $fileExtension;
                    $baseData['file_size'] = $fileSize;
                    $baseData['sender_name'] = $senderName;
                }
            } elseif ($type == 'link') {
                preg_match('/https?\:\/\/[^\s"]+/i', $message->message, $match);

                $link = $match[0] ?? null;
                $textWithoutLink = trim(str_replace($link, '', $message->message));

                $baseData['link'] = $link;
                $baseData['text'] = $textWithoutLink;
            }

            return $baseData;
        });

        return api_response($messages, '', 200);
    }





}
