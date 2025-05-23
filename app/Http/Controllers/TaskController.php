<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('query');
        $tasks = Task::with('project')
            ->when($query, function ($q) use ($query) {
                return $q->where('title', 'like', "%{$query}%");
            })->orderBy($request->get('sort', 'created_at'), $request->get('order', 'desc'))
            ->paginate(10);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['query', 'sort', 'order']),
        ]);
    }

    public function create()
    {
        $projects = Project::all(['id', 'name']);
        return Inertia::render('Tasks/Create', [
            'projects' => $projects,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'project_id' => 'required|uuid|exists:projects,id',
            'title' => 'required|string',
            'metadata' => 'nullable|json',
            'is_done' => 'boolean',
            'due_date' => 'nullable|date',
        ]);

        $data['id'] = Str::uuid();

        Task::create($data);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $projects = Project::all(['id', 'name']);
        return Inertia::render('Tasks/Edit', [
            'task' => $task,
            'projects' => $projects,
        ]);
    }

    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'project_id' => 'required|uuid|exists:projects,id',
            'title' => 'required|string',
            'metadata' => 'nullable|json',
            'is_done' => 'boolean',
            'due_date' => 'nullable|date',
        ]);

        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
