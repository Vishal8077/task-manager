<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $tasks = Task::all();
        if ($request->ajax()) {
            return response()->json($tasks);
        }

        // dd($tasks);

        return view('tasks.index', compact('tasks'));
    }


    public function store(Request $request)
    {
        $request->validate(['task' => 'required|unique:tasks,task']);

        $task = Task::create([
            'task' => $request->task,
            'complete' => false,
        ]);

        return response()->json($task);
    }

    public function update($id, Request $request)
    {
        $task = Task::findOrFail($id);

        $task->complete = $request->complete;
        $task->save();

        return response()->json($task);
    }

    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
    public function showAllTasks()
    {
        $tasks = Task::all();
        return response()->json($tasks);
    }
}
