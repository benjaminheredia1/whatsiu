<?php

namespace App\Http\Controllers;

use App\Models\Memory;
use App\Models\Estado;
use App\Services\EvolutionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    protected EvolutionService $evolutionService;

    public function __construct(EvolutionService $evolutionService)
    {
        $this->evolutionService = $evolutionService;
    }

    public function index(): JsonResponse
    {
        // Limitar a las últimas 50 conversaciones para velocidad
        $conversations = Memory::selectRaw('session_id, MAX(id) as last_message_id, COUNT(*) as message_count')
            ->groupBy('session_id')
            ->orderByDesc('last_message_id')
            ->limit(50)
            ->get();

        $result = $conversations->map(function ($conv) {
            $lastMessage = Memory::find($conv->last_message_id);
            $estado = Estado::find($conv->session_id);

            return [
                'session_id' => $conv->session_id,
                'last_message' => $lastMessage ? $lastMessage->message : null,
                'last_activity' => null,
                'message_count' => $conv->message_count,
                'estado' => $estado ? $estado->estado : 'activo',
            ];
        });

        return response()->json(['success' => true, 'data' => $result]);
    }

    public function show(string $sessionId): JsonResponse
    {
        // Limitar a los últimos 100 mensajes por conversación
        $messages = Memory::where('session_id', $sessionId)
            ->orderBy('id', 'desc')
            ->limit(100)
            ->get()
            ->reverse()
            ->values();
            
        $estado = Estado::find($sessionId);

        return response()->json([
            'success' => true,
            'data' => [
                'session_id' => $sessionId,
                'estado' => $estado ? $estado->estado : 'activo',
                'messages' => $messages->map(fn($msg) => [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'created_at' => null,
                ]),
            ],
        ]);
    }

    public function send(Request $request, string $sessionId): JsonResponse
    {
        $request->validate(['message' => 'required|string']);
        $messageText = $request->input('message');

        $result = $this->evolutionService->sendMessage($sessionId, $messageText);

        if (!$result['success']) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to send message',
                'details' => $result['error'] ?? 'Unknown error',
            ], 500);
        }

        $memory = Memory::create([
            'session_id' => $sessionId,
            'message' => [
                'type' => 'ai',
                'content' => $messageText,
                'tool_calls' => [],
                'additional_kwargs' => [],
                'response_metadata' => ['source' => 'human_operator'],
                'invalid_tool_calls' => [],
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => ['message_id' => $memory->id, 'evolution_response' => $result['data']],
        ]);
    }

    public function updateEstado(Request $request, string $sessionId): JsonResponse
    {
        $request->validate(['estado' => 'required|string|in:activo,pausado']);

        $estado = Estado::updateOrCreate(
            ['id' => $sessionId],
            ['estado' => $request->input('estado')]
        );

        return response()->json([
            'success' => true,
            'data' => ['session_id' => $sessionId, 'estado' => $estado->estado],
        ]);
    }

    public function storeMemory(Request $request): JsonResponse
    {
        $request->validate(['session_id' => 'required|string', 'content' => 'required|string']);

        $memory = Memory::create([
            'session_id' => $request->input('session_id'),
            'message' => [
                'type' => 'human',
                'content' => $request->input('content'),
                'additional_kwargs' => [],
                'response_metadata' => [],
            ],
        ]);

        return response()->json([
            'success' => true,
            'data' => ['id' => $memory->id, 'session_id' => $memory->session_id, 'message' => $memory->message],
        ]);
    }
}
