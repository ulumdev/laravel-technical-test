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
        $showTrash = $request->get('trash') == 1;
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $builder = $showTrash ? Attachment::onlyTrashed() : Attachment::query();

        $attachments = $builder
            ->when($query, fn($q) => $q->where('file_path', 'like', "%$query%"))
            ->orderBy($sort, $order)
            ->with(['task.project'])
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Attachments/Index', [
            'attachments' => $attachments,
            'filters' => $request->only('q', 'sort', 'order', 'trash'),
        ]);
    }

    public function create()
    {
        $tasks = Task::select(['id', 'title'])->get();
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
            'audit_note' => 'nullable|string|max:255', // Optional field for audit notes
        ]);

        $attachment = new Attachment($data);
        $attachment->auditCustomNote = $data['audit_note'] ?? 'Store - Attachment'; // Set custom audit note if provided
        $attachment->id = (string) Str::uuid(); // Generate a new UUID for the attachment
        $attachment->uploaded_at = now(); // Set the uploaded_at timestamp
        $attachment->file_path = $request->file('file')->store('attachments', 'public'); // Store the file
        $attachment->save();

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
            'audit_note' => 'nullable|string|max:255', // Optional field for audit notes
            // 'file' => 'sometimes|mimes:pdf|file|between:100,500',
        ]);

        if ($request->hasFile('file')) {
            $request->validate([
                'file' => 'mimes:pdf|file|between:100,500',
            ]);
            Storage::disk('public')->delete($attachment->file_path);
            $data['file_path'] = $request->file('file')->store('attachments', 'public');
            $data['uploaded_at'] = now();
        }

        $attachment->auditCustomNote = $data['audit_note'] ?? 'Update - Attachment'; // Set custom audit note if provided
        $attachment->update($data);

        return redirect()->route('attachments.index')->with('success', 'Attachment updated successfully.');
    }

    public function destroy(Attachment $attachment)
    {
        // Storage::disk('public')->delete($attachment->file_path);
        $attachment->auditCustomNote = 'Delete - Attachment'; // Set custom audit note for deletion
        $attachment->delete();

        return redirect()->route('attachments.index')->with('success', 'Attachment deleted successfully.');
    }

    public function restore($id)
    {
        Attachment::onlyTrashed()->findOrFail($id)->restore();
        return redirect()->route('attachments.index')->with('success', 'Attachment restored successfully.');
    }

    public function forceDelete($id)
    {
        $attachment = Attachment::onlyTrashed()->findOrFail($id);
        Storage::disk('public')->delete($attachment->file_path); // Delete the file from storage
        $attachment->forceDelete();
        return redirect()->route('attachments.index')->with('success', 'Attachment permanently deleted successfully.');
    }

    public function show(Attachment $attachment)
    {
        $attachment->load('audits');
        return Inertia::render('Attachments/Show', [
            'attachment' => $attachment,
            'audits' => $attachment->audits()->with('user')->latest()->get(),
        ]);
    }
}
