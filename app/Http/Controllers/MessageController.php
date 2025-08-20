<?php
namespace App\Http\Controllers;
use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // POST /api/messages
    public function send(Request $request)
    {
        $receiver=User::find($request->input('receiver_id'));
        if (!$receiver) {
            return response()->json(['error' => 'Receiver not found'], 404);
        }

        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:2000',
        ]);

        $sender   = $request->user();     // JWT user
        $receiver = User::findOrFail($data['receiver_id']);

        // ✅ enforce teacher ↔ student pairing
        if ($sender->role === 'teacher') {
            $teacher = $sender->teacher;
            if (!$teacher || !$receiver->student || $receiver->student->assigned_teacher_id !== $teacher->id) {
                return response()->json(['error' => 'Not allowed'], 403);
            }
        } elseif ($sender->role === 'student') {
            $student = $sender->student;
            if (!$student || !$receiver->teacher || $student->assigned_teacher_id !== $receiver->teacher->id) {
                return response()->json(['error' => 'Not allowed'], 403);
            }
        } else {
            return response()->json(['error' => 'Role not allowed'], 403);
        }

        $message = Message::create([
            'sender_id'   => $sender->id,
            'receiver_id' => $receiver->id,
            'message'     => $data['message'],
        ]);

        // broadcast to receiver's channel
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    // GET /api/messages/{userId}  -> history between auth user and {userId}
public function index(User $user)
{
    $me = auth()->id();

    $messages = Message::where(function ($q) use ($me, $user) {
        $q->where('sender_id', $me)->where('receiver_id', $user->id);
    })->orWhere(function ($q) use ($me, $user) {
        $q->where('sender_id', $user->id)->where('receiver_id', $me);
    })->orderBy('created_at')->get();

    return response()->json($messages);
}
public function unreadCount()
{
    $count = Message::where('receiver_id', auth()->id())
        ->where('is_read', false)
        ->count();

    return response()->json(['unread_count' => $count]);
}

public function markAsRead(User $user)
{
    $me = auth()->id();

    Message::where('sender_id', $user->id)
        ->where('receiver_id', $me)
        ->where('is_read', false)
        ->update(['is_read' => true]);

    return response()->json(['success' => true]);
}
}
