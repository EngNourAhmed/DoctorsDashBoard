<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\Report;
use App\Models\User;
use App\Notifications\CaseActivity;
use Illuminate\Http\Request;

class CaseChatController extends Controller
{
    /**
     * Get messages for a specific case chat
     * 
     * @param Request $request
     * @param string $batchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function messages(Request $request, $batchId)
    {
        try {
            $user = $request->user();
            \Illuminate\Support\Facades\Log::info('Case chat messages request', ['batch_id' => $batchId, 'user_id' => $user->id]);
            
            // Verify access
            if (!$this->canAccessCase($user, $batchId)) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            $conversation = $this->getOrCreateConversation($batchId);
            
            $query = $conversation->messages()
                ->with('sender')
                ->orderBy('created_at', 'asc');
            
            // Support polling with 'since' parameter
            if ($request->has('since')) {
                $query->where('id', '>', $request->since);
            }
            
            $messages = $query->get()->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'sender_name' => $message->sender ? $message->sender->name : 'Unknown',
                    'sender_id' => $message->sender_id,
                    'body' => $message->body,
                    'is_self' => (String)$message->sender_id === (String)$user->id,
                    'created_at' => $message->created_at->format('Y-m-d h:i A'),
                ];
            });
            
            return response()->json(['messages' => $messages]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Case chat messages error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load messages'], 500);
        }
    }
    
    /**
     * Send a message in case chat
     * 
     * @param Request $request
     * @param string $batchId
     * @return \Illuminate\Http\JsonResponse
     */
    public function send(Request $request, $batchId)
    {
        try {
            $user = $request->user();
            \Illuminate\Support\Facades\Log::info('Case chat send request', ['batch_id' => $batchId, 'user_id' => $user->id, 'message' => $request->message]);
            
            // Verify access
            if (!$this->canAccessCase($user, $batchId)) {
                return response()->json(['error' => 'Unauthorized access'], 403);
            }
            
            $request->validate([
                'message' => 'required|string|max:5000',
            ]);
            
            $conversation = $this->getOrCreateConversation($batchId);
            
            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $user->id,
                'body' => $request->message,
            ]);
            
            // Notify participants
            $this->notifyParticipants($conversation, $user, $batchId);
            
            return response()->json(['ok' => true, 'message' => $message]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Case chat send error: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }
    
    /**
     * Check if user can access a specific case
     * 
     * @param User $user
     * @param string $batchId
     * @return bool
     */
    protected function canAccessCase($user, $batchId)
    {
        // Staff can access all cases
        if (in_array($user->role, ['admin', 'assistant', 'admin_assistant'])) {
            \Illuminate\Support\Facades\Log::info('Access granted: user is staff', ['user_id' => $user->id, 'role' => $user->role]);
            return true;
        }
        
        // Users can only access their own cases
        $exists = Report::where('batch_id', $batchId)
            ->where('user_id', $user->id)
            ->exists();
            
        if (!$exists) {
            \Illuminate\Support\Facades\Log::warning('Access denied: user is not the owner', ['user_id' => $user->id, 'batch_id' => $batchId]);
        } else {
            \Illuminate\Support\Facades\Log::info('Access granted: user is owner', ['user_id' => $user->id, 'batch_id' => $batchId]);
        }
        
        return $exists;
    }
    
    /**
     * Get or create conversation for case chat
     * 
     * @param string $batchId
     * @return Conversation
     */
    protected function getOrCreateConversation($batchId)
    {
        return Conversation::firstOrCreate(
            [
                'type' => 'case_chat',
                'batch_id' => $batchId,
            ],
            [
                'admin_id' => null,
                'participant_id' => null,
            ]
        );
    }
    
    /**
     * Notify participants about new message
     * 
     * @param Conversation $conversation
     * @param User $sender
     * @param string $batchId
     * @return void
     */
    protected function notifyParticipants($conversation, $sender, $batchId)
    {
        $report = Report::where('batch_id', $batchId)->first();
        
        if (!$report) return;
        
        // Notify case owner if sender is staff
        if (in_array($sender->role, ['admin', 'assistant', 'admin_assistant'])) {
            $report->user->notify(new CaseActivity($report, 'message_received'));
        } else {
            // Notify all staff if sender is client
            $staff = User::whereIn('role', ['admin', 'assistant', 'admin_assistant'])->get();
            foreach ($staff as $person) {
                $person->notify(new CaseActivity($report, 'message_received'));
            }
        }
    }
}
