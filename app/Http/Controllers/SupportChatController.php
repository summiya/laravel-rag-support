<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\RAG\RetrievalService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SupportChatController extends Controller
{
    /**
     * Display the support chat page with persistent session memory.
     */
    public function index(Request $request)
    {
        $sessionId = $request->session()->getId();

        $conversation = Conversation::firstOrCreate(
            ['session_id' => $sessionId],
            ['user_id' => Auth::id(), 'title' => null]
        );

        $messages = $conversation->messages()->get();

        return view('support.chat', [
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    /**
     * Handle the chat request and persist user/assistant history.
     */
    public function chat(Request $request, RetrievalService $retrievalService)
    {
        try {
            $request->validate([
                'message' => 'required|string|max:4000',
            ]);

            $sessionId = $request->session()->getId();

            $conversation = Conversation::firstOrCreate(
                ['session_id' => $sessionId],
                ['user_id' => Auth::id(), 'title' => null]
            );

            $userMessage = $request->input('message');

            $conversation->messages()->create([
                'role' => 'user',
                'content' => $userMessage,
            ]);

            $history = $conversation->messages()
                ->latest('created_at')
                ->limit(10)
                ->get()
                ->reverse()
                ->values();

            $retrievedChunks = $retrievalService->retrieve($userMessage);
            $context = $retrievalService->buildContext($retrievedChunks);

            $systemPrompt = 'You are a helpful support assistant. Answer ONLY using the provided company knowledge for the latest user message. If previous messages mention a different product or topic, do not reuse that older topic unless the latest user message asks for it. If the answer is not in the knowledge, say "I could not find this information in the knowledge base."';

            if ($context !== 'No relevant knowledge found.') {
                $systemPrompt .= "\n\nCompany knowledge:\n".$context;
            }

            $payloadMessages = collect([
                [
                    'role' => 'system',
                    'content' => $systemPrompt,
                ],
            ])->concat($history->map(fn (Message $message) => [
                'role' => $message->role,
                'content' => $message->content,
            ]))->all();

            $payload = [
                'model' => 'llama3',
                'messages' => $payloadMessages,
                'stream' => false,
            ];

            $response = Http::timeout(120)->post('http://localhost:11434/api/chat', $payload);

            if (! $response->successful()) {
                return response()->json([
                    'error' => 'AI service is currently unavailable. Please try again later.',
                ], 503);
            }

            $data = $response->json();

            $answer = Arr::get($data, 'message.content')
                ?? Arr::get($data, 'response')
                ?? 'The assistant could not generate a response right now.';

            $conversation->messages()->create([
                'role' => 'assistant',
                'content' => $answer,
            ]);

            return response()->json(['answer' => $answer]);
        } catch (ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input: '.$e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred. Please try again.',
            ], 500);
        }
    }

    /**
     * Clear the current session conversation history.
     */
    public function clear(Request $request)
    {
        $sessionId = $request->session()->getId();

        $conversation = Conversation::where('session_id', $sessionId)->first();

        if ($conversation) {
            $conversation->messages()->delete();
            $conversation->delete();
        }

        return response()->json(['status' => 'cleared']);
    }
}
