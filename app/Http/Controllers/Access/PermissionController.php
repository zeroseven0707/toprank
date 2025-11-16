<?php
namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::all();
        return Inertia::render('Access/Permissions/Index', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate(['name' => 'required|string|unique:permissions']);
        Permission::create($validated);
        return back()->with('success', 'Permission created.');
    }

    public function update(Request $request, Permission $permission)
    {
        $validated = $request->validate(['name' => 'required|string|unique:permissions,name,' . $permission->id]);
        $permission->update($validated);
        return back()->with('success', 'Permission updated.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('success', 'Permission deleted.');
    }
}
