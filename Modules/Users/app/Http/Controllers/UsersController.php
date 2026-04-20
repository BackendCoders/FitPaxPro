<?php

namespace Modules\Users\app\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index(Request $request)
    {
        $query = \App\Models\User::with('roles')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status == 'active');
        }

        $users = $query->paginate(15);
        return view('users::index', compact('users'));
    }

    /**
     * Show the specified resource.
     * @param string $id
     */
    public function show($id)
    {
        $user = \App\Models\User::with(['ownedGyms', 'gymSubscriptions.feePlan', 'attendanceLogs'])->findOrFail($id);
        return view('users::show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param string $id
     */
    public function edit($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users::edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     */
    public function update(Request $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'boolean',
            'roles' => 'array'
        ]);

        $user->update($request->only('name', 'email', 'phone', 'status'));
        
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        if ($request->has('custom_fields')) {
            $user->saveCustomFields($request->custom_fields);
        }

        return redirect()->route('users.index')->with('success', 'User deployment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     */
    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User node decommissioned.');
    }
}
