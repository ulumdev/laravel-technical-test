<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AttachmentController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->get('query');
        $attachments = Attachment::with('task')
            ->when($query, function ($q) use ($query) {
                return $q->where('file_path', 'like', "%{$query}%");
            })->orderBy($request->get('sort', 'created_at'), $request->get('order', 'desc'))
            ->paginate(10);
        return Inertia::render('Attachments/Index', [
            'attachments' => $attachments,
            'filters' => $request->only(['query', 'sort', 'order']),
        ]);
    }

    public function create()
    {
        $tasks = Task::all(['id', 'title']);
        return Inertia::render('Attachments/Create', [
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'task_id' => 'required|uuid|exists:tasks,id',
            'file' => 'required|mimes:pdf|file|between:100,500',
            'info' => 'nullable|json',
            'is_public' => 'boolean',
        ]);

        $data['id'] = Str::uuid();
        $data['uploaded_at'] = now();

        $data['file_path'] = $request->file('file')->store('attachments', 'public');

        Attachment::create($data);

        return redirect()->route('attachments.index')->with('success', 'Attachment created successfully.');
    }

    public function edit(Attachment $attachment)
    {
        $tasks = Task::all(['id', 'title']);
        return Inertia::render('Attachments/Edit', [
            'attachment' => $attachment,
            'tasks' => $tasks,
        ]);
    }

    public function update(Request $request, Attachment $attachment)
    {
        $data = $request->validate([
            'task_id' => 'required|uuid|exists:tasks,id',
            'info' => 'nullable|json',
            'is_public' => 'boolean',
        ]);

        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'mimes:pdf|file|between:100,500',
            ]);
            Storage::disk('public')->delete($attachment->file_path);
            $data['file_path'] = $request->file('file')->store('attachments', 'public');
            $data['uploaded_at'] = now();
        }

        $attachment->update($data);

        return redirect()->route('attachments.index')->with('success', 'Attachment updated successfully.');
    }

    public function destroy(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->file_path);
        $attachment->delete();

        return redirect()->route('attachments.index')->with('success', 'Attachment deleted successfully.');
    }
}
