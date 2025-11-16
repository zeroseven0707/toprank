<?php
namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ContentCategory;
use Inertia\Inertia;

class ContentSettingController extends Controller
{
    public function index()
    {
        $categories = ContentCategory::latest()->paginate(10);

        return Inertia::render('Settings/ContentCategory/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Settings/ContentCategory/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        ContentCategory::create($request->only('name', 'description'));

        return redirect()->route('content-categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = ContentCategory::findOrFail($id);

        return Inertia::render('Settings/ContentCategory/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = ContentCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category->update($request->only('name', 'description'));

        return redirect()->route('content-categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        ContentCategory::destroy($id);
        return redirect()->route('content-categories.index')->with('success', 'Category deleted successfully.');
    }
}
