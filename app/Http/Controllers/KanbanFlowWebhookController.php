<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KanbanFlowWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Validate the request if necessary

        // Process the KanbanFlow webhook payload
        $payload = $request->all();

        // For example, logging the received webhook
        Log::info('Received KanbanFlow webhook', $payload);

        // Respond to KanbanFlow
        return response()->json(['message' => 'Webhook received successfully']);
    }
}
