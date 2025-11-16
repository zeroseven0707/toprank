<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function index()
    {
        return response()->json(Tag::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:100']);
        $data = Tag::create($request->only('name'));
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $data = Tag::findOrFail($id);
        $data->update($request->only('name'));
        return response()->json($data);
    }

    public function destroy($id)
    {
        Tag::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
