<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\ContentCategory;
use App\Models\Section;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SectionController extends Controller
{
    // Tampilkan halaman section
    public function index()
    {
        $sections = Section::with('contentCategory')->orderBy('order')->get();
        $categories = ContentCategory::all();

        return Inertia::render('Settings/Sections/Index', [
            'sections' => $sections,
            'categories' => $categories,
        ]);
    }

    // Simpan section baru (Inertia compatible)
    public function store(Request $request)
    {
        $request->validate([
            'content_category_id' => 'required|exists:content_categories,id',
            'status' => 'nullable|in:active,inactive',
        ]);

        $maxOrder = Section::max('order') ?? 0;

        $section = Section::create([
            'content_category_id' => $request->content_category_id,
            'status' => $request->status ?? 'active',
            'order' => $maxOrder + 1,
        ]);

        // Return Inertia redirect dengan flash message
        return redirect()->route('sections.index')->with('success', 'Section added.');
    }

    // Update section
    public function update(Request $request, Section $section)
    {
        $request->validate([
            'content_category_id' => 'required|exists:content_categories,id',
            'status' => 'required|in:active,inactive',
        ]);

        $section->update($request->only('content_category_id', 'status'));

        return redirect()->route('sections.index')->with('success', 'Section updated.');
    }

    // Hapus section
    public function destroy(Section $section)
    {
        $section->delete();

        return redirect()->route('sections.index')->with('success', 'Section deleted.');
    }

    // Reorder sections (drag & drop)
    public function reorder(Request $request)
    {
        $request->validate(['order' => 'required|array']);

        foreach ($request->order as $index => $id) {
            Section::where('id', $id)->update(['order' => $index + 1]);
        }

    }
}
