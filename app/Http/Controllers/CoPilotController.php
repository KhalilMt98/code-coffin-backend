<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CoPilotController extends Controller
{
    public function getSuggestions(Request $request)
    {
        $apiKey = config('services.openai.api_key');
        \Log::info('OpenAI API Key:', ['api_key' => $apiKey]);
        $code = $request->input('code');
        $language = $request->input('language');
    
        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json',
            ])->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => "you will act as a copilot you will be providing code completion and correction can you also point at the error and if there's no code just don't say anything and make the reply look like you are not talking to me.  for this $language code:\n\n$code",
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
                'suggestions' => explode("\n", trim($suggestions)),
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

}
