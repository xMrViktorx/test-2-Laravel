<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'permission:user-management']);
    }

    /**
     * Display a listing of all permissions and associated users.
     * @return Renderable
     */
    public function index()
    {
        $permissions = Permission::all();
        $users = User::with('permissions')->get(); // Fetch all users with their permissions

        return view('permissions.index', compact('permissions', 'users'));
    }

    /**
     * Show the form for creating a new permission.
     * @return Renderable
     */
    public function create()
    {
        return view('permissions.create');
    }

    /**
     * Store a newly created permission in the database.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);

        Permission::create(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing an existing permission.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $permission = Permission::findOrFail($id);

        return view('permissions.edit', compact('permission'));
    }

    /**
     * Update an existing permission in the database.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        $request->validate([
            'name' => 'required|unique:permissions,name,' . $permission->id,
        ]);

        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission updated successfully.');
    }

    /**
     * Delete a permission from the database.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return redirect()->route('permissions.index')->with('success', 'Permission deleted successfully.');
    }

    /**
     * Assign permissions to a user.
     * @param Request $request
     * @return Renderable
     */
    public function assignPermission(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ], [
            'user_id.required' => 'The user field is required.',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncPermissions($request->permissions);

        return redirect()->route('permissions.index')->with('success', 'Permissions assigned successfully.');
    }
}
