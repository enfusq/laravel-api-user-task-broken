<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;


class TaskController extends Controller implements HasMiddleware
{
    public static function middleware() {
        return [
            new Middleware('auth:sanctum', except: ['store', 'show'])
        ];
    }
    
    public function index(Request $request)
    {
        // e.g. /api/tasks?status=pending
        if ($request->has('status')) {
            return $this->getUserTasks($request);
        }

        $tasks = Task::where('user_id', $request->user()->id)->get();
        return response()->json($tasks);
    }

    private function getUserTasks(Request $request)
    {
        $request->validate([
            'status' => 'required|in:pending,inprogress,completed',
        ]);

        $userId = $request->user()->id;
        // Call the stored procedure
        $tasks = \DB::select('CALL GetUserTasks(?, ?)', [$userId, $request['status']]);

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task = $request->user()->tasks()->create($request->all());
        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $task;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task->update($request->all());
        return response()->json($task, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(null, 204);
    }

    public function changeStatus(Request $request, Task $task)
    {
        $request["status_id"];
        $task->save();
        return response()->json(null, 204);
    }
}
