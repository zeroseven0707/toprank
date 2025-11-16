<?php

namespace App\Http\Controllers\Access;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();

        return Inertia::render('Access/Roles/Index', [
            'roles' => $roles,
        ]);
    }

    public function create()
    {
        // Ambil semua permission, lalu kelompokkan berdasarkan module (kata kedua)
        $permissions = Permission::all()->groupBy(function ($permission) {
            // ambil kata kedua (module name)
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'others';
        });

        return Inertia::render('Access/Roles/Create', [
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles',
            'permissions' => 'array',
        ]);

        $role = Role::create(['name' => $validated['name']]);

        if (!empty($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', '✅ Role created successfully.');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode(' ', $permission->name);
            return $parts[1] ?? 'others';
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return Inertia::render('Access/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'array',
        ]);

        $role->update(['name' => $validated['name']]);
        $role->syncPermissions($validated['permissions'] ?? []);

        return redirect()->route('roles.index')->with('success', '✅ Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        $role->delete();

        return back()->with('success', '🗑️ Role deleted successfully.');
    }
}
