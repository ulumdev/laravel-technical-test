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
        $projects = Project::when($query, function ($q) use ($query) {
            return $q->where('name', 'like', "%{$query}%");
        })->orderBy($request->get('sort', 'created_at'), $request->get('order', 'desc'))->with('tasks')->paginate(10);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['query', 'sort', 'order']),
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
            'is_active' => 'boolean',
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
}
