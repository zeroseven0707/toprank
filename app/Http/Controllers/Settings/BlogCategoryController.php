<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BlogCategoryController extends Controller
{
    public function index()
    {
        $categories = BlogCategory::latest()->paginate(10);

        return Inertia::render('Settings/BlogCategory/Index', [
            'categories' => $categories,
        ]);
    }

    public function create()
    {
        return Inertia::render('Settings/BlogCategory/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        BlogCategory::create($request->only('name', 'description', 'status'));

        return redirect()->route('blog-categories.index')->with('success', 'Blog category created successfully.');
    }

    public function edit($id)
    {
        $category = BlogCategory::findOrFail($id);

        return Inertia::render('Settings/BlogCategory/Edit', [
            'category' => $category,
        ]);
    }

    public function update(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:blog_categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($request->only('name', 'description', 'status'));

        return redirect()->route('blog-categories.index')->with('success', 'Blog category updated successfully.');
    }

    public function destroy($id)
    {
        BlogCategory::destroy($id);
        return redirect()->route('blog-categories.index')->with('success', 'Blog category deleted successfully.');
    }
}
