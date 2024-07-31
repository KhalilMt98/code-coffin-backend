<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ExplainController extends Controller
{
    public function getExplanation(Request $request)
    {
        $apiKey = config('services.openai.api_key');
        \Log::info('OpenAI API Key:', ['api_key' => $apiKey]);
        $code = $request->input('code');
    
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "you will be explaining this code please be brief code:\n\n$code",
                    ],
                ],
                'max_tokens' => 150,
                'temperature' => 0.5,
                'top_p' => 1,
                'frequency_penalty' => 0,
                'presence_penalty' => 0,
            ]);
            \Log::info('Request Headers: ', [
                'Authorization' => "Bearer $apiKey"
            ]);
            $suggestions = $response->json()['choices'][0]['message']['content'] ?? '';
            return response()->json([
                'suggestions' => $suggestions,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
