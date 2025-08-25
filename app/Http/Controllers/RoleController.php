<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'sometimes|array'
        ]);

        $role = Role::create(['name' => $validatedData['name']]);

        if (isset($validatedData['permissions'])) {
            $role->syncPermissions($validatedData['permissions']);
        }

        return response()->json($role->load('permissions'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role)
    {
        return response()->json($role->load('permissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $validatedData = $request->validate([
            'name' => ['sometimes', 'required', 'string', Rule::unique('roles')->ignore($role)],
            'permissions' => 'sometimes|array'
        ]);

        if (isset($validatedData['name'])) {
            $role->name = $validatedData['name'];
            $role->save();
        }

        if (isset($validatedData['permissions'])) {
            $role->syncPermissions($validatedData['permissions']);
        }

        return response()->json($role->load('permissions'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deleting essential roles
        if (in_array($role->name, ['User', 'Admin', 'Super Admin'])) {
            return response()->json(['message' => 'Cannot delete a default system role.'], 403);
        }

        $role->delete();

        return response()->json(['message' => 'Role successfully deleted.']);
    }
}
