<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('query');
        $showTrash = $request->get('trash') == 1;
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $builder = $showTrash ? Project::onlyTrashed() : Project::query();

        $projects = $builder
            ->when($query, fn($q) => $q->where('name', 'like', "%{$query}%"))
            ->orderBy($sort, $order)
            ->withCount('tasks')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only('query', 'sort', 'order', 'trash'),
        ]);
    }

    public function create()
    {
        return Inertia::render('Projects/Create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'details' => 'nullable|json',
            'is_active' => 'boolean',
            'start_date' => 'nullable|date',
        ]);

        $data['id'] = Str::uuid();

        Project::create($data);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Projects/Edit', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'details' => 'nullable|json',
            'is_active' => 'required|boolean',
            'start_date' => 'nullable|date',
        ]);

        $project->update($data);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project deleted successfully.');
    }

    public function restore($id)
    {
        Project::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('projects.index')->with('success', 'Project restored successfully.');
    }

    public function forceDelete($id)
    {
        $project = Project::onlyTrashed()->findOrFail($id);
        $project->forceDelete();
        return redirect()->route('projects.index')->with('success', 'Project permanently deleted successfully.');
    }
}
