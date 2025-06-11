<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class TaskController extends Controller implements HasMiddleware
{
    public static function middleware() {
        return [
            new Middleware('auth:sanctum', except: ['show'])
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
            'status_id' => 'nullable|exists:task_statuses,id',
            'category_id' => 'nullable|exsists:task_categories,id'
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
        Gate::authorize('update', $task);
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
        Gate::authorize('delete', $task);
        $task->delete();
        return response()->json(null, 204);
    }

    public function changeStatus(Request $request, Task $task)
    {
        $request->validate([
            "status_id" => "required|exists:task_statuses,id"
        ]);
        $task->update($request->all());
        return response()->json(null, 204);
    }
}
