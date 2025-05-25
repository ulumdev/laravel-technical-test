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
        $showTrash = $request->get('trash') == 1;
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $builder = $showTrash ? Task::onlyTrashed() : Task::query();

        $tasks = $builder
            ->when($query, fn($q) => $q->where('title', 'like', "%{$query}%"))
            ->orderBy($sort, $order)
            ->withCount('attachments')
            ->paginate(10)
            ->withQueryString();
        // $tasks = Task::with('project')
        //     ->when($query, function ($q) use ($query) {
        //         return $q->where('title', 'like', "%{$query}%");
        //     })->orderBy($request->get('sort', 'created_at'), $request->get('order', 'desc'))
        //     ->paginate(10);

        return Inertia::render('Tasks/Index', [
            'tasks' => $tasks,
            'filters' => $request->only(['query', 'sort', 'order', 'trash']),
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
            'audit_note' => 'nullable|string|max:255', // Optional field for audit notes
        ]);

        $task = new Task($data);
        $task->auditCustomNote = $data['audit_note'] ?? 'Store - Task'; // Set custom audit note if provided
        $task->id = (string) Str::uuid(); // Generate a new UUID for the task
        $task->save();

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
            'audit_note' => 'nullable|string|max:255', // Optional field for audit notes
        ]);

        $task->auditCustomNote = $data['audit_note'] ?? 'Update - Task'; // Set custom audit note if provided
        $task->update($data);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $task->auditCustomNote = 'Delete - Task'; // Set custom audit note for deletion
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }

    public function restore($id)
    {
        Task::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('tasks.index')->with('success', 'Task restored successfully.');
    }

    public function forceDelete($id)
    {
        $task = Task::onlyTrashed()->findOrFail($id);
        $task->forceDelete();
        return redirect()->route('tasks.index')->with('success', 'Task permanently deleted successfully.');
    }

    public function show(Task $task)
    {
        $task->load('audits');
        return Inertia::render('Tasks/Show', [
            'task' => $task,
            'audits' => $task->audits()->with('user')->latest()->get(),
        ]);
    }
}
