<?php

namespace App\Http\Controllers;

use App\Exports\DynamicExport;
use App\Exports\ProjectExport;
use App\Jobs\ExportProjectToExcel;
use App\Jobs\ImportProjectFromExcel;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Auditor;

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
            'audit_note' => 'nullable|string|max:255', // Optional field for audit notes
        ]);

        $project = new Project($data);
        $project->id = (string)Str::uuid();
        $project->auditCustomNote = $data['audit_note'] ?? 'Store - Project'; // Set custom audit note if provided
        $project->save();

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
            'audit_note' => 'nullable|string|max:255', // Optional field for audit notes
        ]);

        $project->auditCustomNote = $data['audit_note'] ?? 'Update - Project'; // Set custom audit note if provided
        $project->update($data);

        return redirect()->route('projects.show', $project->id)->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $project->auditCustomNote = 'Delete - Project'; // Set custom audit note for deletion
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

    public function show(Project $project)
    {
        $project->load('audits');
        return Inertia::render('Projects/Show', [
            'project' => $project,
            'audits' => $project->audits()->with('user')->latest()->get(),
        ]);
    }

    // public function exportExcel(Request $request)
    // {
    //     $fields = explode(',', $request->get('fields', 'id,name'));
    //     // Dispatch job to queue
    //     $fileName = 'projects_export_' . time() . '.xlsx';
    //     Excel::queue(new DynamicProjectExport($fields), $fileName, 'exports');
    //     return back()->with('success', 'Export sedang diproses di background, silahkan check halaman download nanti.');
    // }

    // public function exportExcel(Request $request)
    // {
    //     $fields = $request->get('fields', 'id,name');
    //     ExportProjectToExcel::dispatch(
    //         $fields,
    //         auth()->user->id
    //     );
    //     return back()->with('success', 'Export sedang diproses di background, silahkan check halaman download nanti.');
    // }

    // public function importExcel(Request $request)
    // {
    //     $request->validate([
    //         'file' => 'required|file|mimes:xlsx,csv',
    //         'mapping' => 'required|array',
    //     ]);

    //     $path = $request->file('file')->store('imports');
    //     ImportProjectFromExcel::dispatch(
    //         $path,
    //         $request->input('mapping')
    //     );

    //     return back()->with('success', 'Import sedang diproses di background, silahkan check halaman proyek nanti.');
    // }

    // public function export()
    // {
    //     return Excel::download(new ProjectExport, 'projects.xlsx', true, ['X-Vapor-Base64-Encode' => 'True']);
    // }

    // public function export(Request $request)
    // {
    //     $fields = $request->get('fields', 'id,name');
    //     $fileName = 'projects_export_' . now()->timestamp . '.xlsx';
    //     // QUEUE JOB
    //     Excel::queue(new DynamicExport(\App\Models\Project::class, $fields), $fileName, 'exports');
    //     return back()->with('success', 'Export sedang diproses di background, silahkan check halaman download nanti.');
    // }
}
