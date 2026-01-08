<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogChatContext
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('api/chat*')) {
            Log::debug('📨 Incoming chat request', [
                'query' => $request->input('query'),
                'context_from_frontend' => $request->input('current_context'),
                'history_count' => count($request->input('conversation_history', [])),
            ]);
        }

        $response = $next($request);

        if ($request->is('api/chat*') && $response->getStatusCode() === 200) {
            $data = json_decode($response->getContent(), true);
            
            Log::debug('📤 Outgoing chat response', [
                'extracted_entities' => $data['data']['extracted_entities'] ?? [],
                'source' => $data['data']['source'] ?? 'unknown',
            ]);
        }

        return $response;
    }
}