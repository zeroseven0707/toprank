<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PlaceSetting;

class PlaceSettingController extends Controller
{
    public function index()
    {
        return response()->json(PlaceSetting::latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $data = PlaceSetting::create($request->only('name', 'description'));
        return response()->json($data);
    }

    public function update(Request $request, $id)
    {
        $data = PlaceSetting::findOrFail($id);
        $data->update($request->only('name', 'description'));
        return response()->json($data);
    }

    public function destroy($id)
    {
        PlaceSetting::destroy($id);
        return response()->json(['message' => 'Deleted successfully']);
    }
}
