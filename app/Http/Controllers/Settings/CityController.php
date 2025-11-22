<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CityController extends Controller
{
    public function index()
    {
        $cities = City::latest()->paginate(10);

        return Inertia::render('Settings/City/Index', [
            'cities' => $cities,
        ]);
    }

    public function create()
    {
        return Inertia::render('Settings/City/Create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:cities,name',
            'status' => 'required|in:active,inactive',
        ]);

        City::create($request->all());

        return redirect()->route('cities.index')->with('success', 'City created successfully.');
    }

    // Gunakan route model binding di edit juga
    public function edit(City $city)
    {
        return Inertia::render('Settings/City/Edit', [
            'city' => $city
        ]);
    }

    public function update(Request $request, City $city)
    {
        $request->validate([
            'name' => 'required|string|unique:cities,name,' . $city->id,
            'status' => 'required|in:active,inactive',
            'is_current' => 'nullable|boolean',
        ]);

        $city->update($request->all());

        return redirect()->route('cities.index')->with('success', 'City updated successfully.');
    }

    public function destroy(City $city)
    {
        $city->delete();

        return redirect()->route('cities.index')->with('success', 'City deleted successfully.');
    }

    public function makeDefault(City $city)
    {
        City::where('is_current', true)->update(['is_current' => false]);
        $city->update(['is_current' => true]);
        return redirect()->route('cities.index')->with('success', 'Default city updated.');
    }
}
