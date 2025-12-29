<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    public function getAllUsers()
    {
        // Mengambil semua pengguna
        return User::all();
    }

    public function findUserById($id)
    {
        // Mencari pengguna berdasarkan ID
        return User::findOrFail($id);
    }

    public function createUser(array $data)
    {
        // Membuat pengguna baru
        return User::create($data);
    }

    public function updateUser($id, array $data)
    {
        // Memperbarui data pengguna
        $user = User::findOrFail($id);
        $user->update($data);
        return $user;
    }

    public function deleteUser($id)
    {
        // Menghapus pengguna
        $user = User::findOrFail($id);
        $user->delete();
        return true;
    }
}
