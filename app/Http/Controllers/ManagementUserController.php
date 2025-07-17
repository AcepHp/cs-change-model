<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ManagementUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('username', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%');
        }

        $users = $query->orderBy('name')->paginate(10); // tampil 10 per halaman

        return view('management-user.index', compact('users'));
    }

    public function create()
    {
        return view('management-user.create-user');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'npk' => 'required|unique:users',
            'email' => 'nullable|email',
            'password' => 'required|min:6|confirmed',
            'role' => 'required'
        ]);

        User::create([
            'name' => $request->name,
            'npk' => $request->npk,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role
        ]);

        return redirect()->route('managementUser')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('management-user.edit-user', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'npk' => 'required|unique:users,npk,'.$user->id,
            'email' => 'nullable|email',
            'role' => 'required'
        ]);

        $user->update([
            'name' => $request->name,
            'npk' => $request->npk,
            'email' => $request->email,
            'role' => $request->role
        ]);


        return redirect()->route('managementUser')->with('success', 'User berhasil diupdate.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('managementUser')->with('success', 'User berhasil dihapus.');
    }
}
