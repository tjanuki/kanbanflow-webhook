<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class KanbanFlowWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Process the KanbanFlow webhook payload
        $data = $request->all();

        // For example, logging the received webhook
        Log::info('Received KanbanFlow webhook', $data);

        // Create or update the task
        $task = Task::updateOrCreate(
            ['kanbanflow_task_id' => $data['task']['_id']],
            [
                'name' => $data['task']['name'],
                'description' => $data['task']['description'],
                'color' => $data['task']['color'],
                'column_id' => $data['task']['columnId'],
                'total_seconds_spent' => $data['task']['totalSecondsSpent'],
                'total_seconds_estimate' => $data['task']['totalSecondsEstimate'],
                'changed_properties' => json_encode($data['changedProperties'])
            ]
        );

        // Assuming you're replacing all subtasks each time for simplicity
        $task->subTasks()->delete();
        foreach ($data['task']['subTasks'] as $subTaskData) {
            $task->subTasks()->create([
                'name' => $subTaskData['name'],
                'finished' => $subTaskData['finished'],
            ]);
        }

        // Respond to KanbanFlow
        return response()->json(['message' => 'Webhook received successfully']);
    }
}
