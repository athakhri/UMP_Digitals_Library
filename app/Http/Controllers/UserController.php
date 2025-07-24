<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    public function index(): JsonResponse
    {

        $dataUser = User::all();
        return response()->json($dataUser, 200);
    }
    // Menampilkan user berdasarkan ID
    public function show($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user, 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    // Menambahkan user baru
public function store(Request $request): JsonResponse
{
    $request->validate([
        'name' => 'required|string|unique:users,name',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
        'role' => 'required|in:siswa,guru', // tambahkan ini
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => $request->role,
    ]);

    return response()->json([
        'message' => 'Akun pengguna berhasil ditambahkan.',
        'data' => $user
    ], 201);
}


    // Mengupdate data user
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'name' => 'sometimes|string|max:255|unique:users,name,' . $id,
                'password' => 'sometimes|string|min:8',
                'role' => 'sometimes|in:siswa,guru', // tambahkan ini juga
            ]);

            $data = $request->only(['name', 'email', 'password', 'name', 'role']);
            if (isset($data['password'])) {
                $data['password'] = bcrypt($data['password']);
            }

            logger('Data yg dikirim', $data);
            $user->update($data);
            

            return response()->json([
                'message' => $user->wasChanged()
                    ? 'Akun pengguna berhasil diupdate.'
                    : 'Tidak ada perubahan pada data pengguna.',
                'data' => $user
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found'], 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json(['message' => 'User berhasil dihapus.']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User tidak ditemukan.'], 404);
        }
    }
            public function count()
    {
        $count = \App\Models\User::count();

        return response()->json([
            'total' => $count
        ]);
    }
    public function register(Request $request): JsonResponse
{
    $request->validate([
        'name' => 'required|string|unique:users,name',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'role' => 'siswa', // role default, misalnya "siswa"
    ]);

    return response()->json([
        'message' => 'Registrasi berhasil.',
        'data' => $user
    ], 201);
}

}