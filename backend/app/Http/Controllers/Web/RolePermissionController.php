<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $systemRoles = ['Admin','Kepala Sekolah','Waka Kurikulum','Guru','Pegawai'];
        return view('admin.roles-permissions.index', compact('roles','permissions','systemRoles'));
    }

    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);
        Role::create(['name' => $request->name, 'guard_name' => 'web']);
        return redirect()->back()->with('success', 'Role berhasil dibuat.');
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:permissions,name',
        ]);
        Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        return redirect()->back()->with('success', 'Izin berhasil dibuat.');
    }

    public function updateRolePermissions(Request $request, Role $role)
    {
        $perms = $request->input('permissions', []);
        $role->syncPermissions($perms);
        // Refresh Spatie permission cache to apply changes immediately
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        // Propagate direct permissions to users of this role for immediate effect
        // Avoid User::role() because our model has a method role() that conflicts with Spatie scope
        $userIds = \DB::table('model_has_roles')
            ->where('role_id', $role->id)
            ->where('model_type', \App\Models\User::class)
            ->pluck('model_id');
        $usersWithRole = \App\Models\User::whereIn('id', $userIds)->get();
        foreach ($usersWithRole as $user) {
            $user->syncPermissions($perms);
        }
        return redirect()->back()->with('success', 'Izin untuk role '.$role->name.' telah diperbarui. (Cache izin disegarkan)');
    }

    public function destroyRole(Role $role)
    {
        $systemRoles = ['Admin','Kepala Sekolah','Waka Kurikulum','Guru','Pegawai'];
        if (in_array($role->name, $systemRoles)) {
            return redirect()->back()->with('error', 'Role sistem tidak dapat dihapus.');
        }
        $role->delete();
        return redirect()->back()->with('success', 'Role berhasil dihapus.');
    }
}


