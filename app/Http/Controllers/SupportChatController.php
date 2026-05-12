<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class SupportChatController extends Controller
{
    /**
     * Display the support chat page.
     */
    public function index()
    {
        return view('support.chat');
    }

    /**
     * Handle the chat request to the AI API.
     */
    public function chat(Request $request)
    {
        try {
            // Validate the incoming message
            $request->validate([
                'message' => 'required|string|max:4000',
            ]);

            $userMessage = $request->input('message');

            // Prepare the request body for Ollama API
            $payload = [
                'model' => 'llama3',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a helpful support assistant. Keep answers short, clear, and friendly.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $userMessage,
                    ],
                ],
                'stream' => false,
            ];

            // Send request to Ollama API
            $response = Http::timeout(120)->post('http://localhost:11434/api/chat', $payload);

            // Check if the request was successful
            if ($response->successful()) {
                $data = $response->json();

                // Extract the answer from the response: prefer message.content, fallback to response
                $answer = $data['message']['content'] ?? ($data['response'] ?? 'Sorry, I couldn\'t generate a response.');

                return response()->json(['answer' => $answer]);
            } else {
                // Handle API failure
                return response()->json(['error' => 'AI service is currently unavailable. Please try again later.'], 503);
            }
        } catch (ValidationException $e) {
            // Handle validation errors
            return response()->json(['error' => 'Invalid input: ' . $e->getMessage()], 422);
        } catch (\Exception $e) {
            // Handle general exceptions
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }
}