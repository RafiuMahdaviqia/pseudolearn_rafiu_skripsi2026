<?php

namespace App\Services;

use App\Core\BaseResponse;
use Illuminate\Support\Facades\DB;
use App\Repositories\MahasiswaRepository;
use App\Models\Mahasiswa;
use App\Models\User;

class MahasiswaService
{
    protected $mahasiswaRepository;
    protected $mahasiswaModel;
    protected $userModel;

    public function __construct()
    {
        $this->mahasiswaRepository = new MahasiswaRepository();
        $this->mahasiswaModel = new Mahasiswa();
        $this->userModel = new User();
    }

    public function table($request)
    {
        $opr = $this->mahasiswaRepository->table($request);

        return $opr;
    }

    public function store($data)
    {
        // Check duplicate name in mahasiswa
        $existsMahasiswa = $this->mahasiswaModel
            ->whereRaw('LOWER(name) = ?', [strtolower($data->name)])
            ->exists();

        if ($existsMahasiswa) {
            return BaseResponse::errorMessage('Mahasiswa dengan nama tersebut sudah ada.', 409);
        }

        // Check duplicate email in user
        $email = strtolower($data->nim . '@gmail.com');
        $existsUserEmail = $this->userModel
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if ($existsUserEmail) {
            return BaseResponse::errorMessage('User dengan email tersebut sudah ada.', 409);
        }

        // Check duplicate name in user
        $existsUserName = $this->userModel
            ->whereRaw('LOWER(name) = ?', [strtolower($data->name)])
            ->exists();

        if ($existsUserName) {
            return BaseResponse::errorMessage('User dengan nama tersebut sudah ada.', 409);
        }

        $opr = $this->mahasiswaRepository->store($data);

        return $opr;
    }

    public function getById($id)
    {
        $opr = $this->mahasiswaRepository->getById($id);

        if (!$opr) {
            return BaseResponse::errorMessage('Mahasiswa tidak ditemukan.', 404);
        }

        return $opr;
    }

    public function update($data)
    {
        $opr = $this->mahasiswaRepository->updatedata($data);

        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->mahasiswaRepository->destroy($id);

        return $opr;
    }

    public function updateProfile($data)
    {
        if ($data->hasFile('avatar')) {
            $file = $data->file('avatar');

            $allowedMime = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
            if (!in_array($file->getMimeType(), $allowedMime)) {
                return BaseResponse::errorMessage('Avatar harus berupa file gambar webp, jpg, jpeg, atau png.', 422);
            }

            if ($file->getSize() > 2 * 1024 * 1024) {
                return BaseResponse::errorMessage('Ukuran avatar maksimal 2MB.', 422);
            }

            // Generate a more random filename
            $filename = 'avatar_' . bin2hex(random_bytes(8)) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('assets/media/avatars'), $filename);
            $data['avatar_filename'] = $filename; // <-- pastikan ini string nama file
        } elseif ($data->filled('avatar_default')) {
            $basename = basename($data->input('avatar_default'));
            $allowed = ['avatar1.webp', 'avatar2.webp', 'avatar3.webp', 'avatar4.webp', 'avatar5.webp', 'avatar6.webp', 'avatar7.webp', 'avatar8.webp', 'blank.webp'];
            if (!in_array($basename, $allowed)) {
                return BaseResponse::errorMessage('Avatar default tidak valid.', 422);
            }

            $data['avatar_filename'] = $basename === 'blank.png' ? null : $basename;
        }

        return $this->mahasiswaRepository->updatedataProfile($data);
    }

    public function resetMahasiswa($id)
    {
        $opr = $this->mahasiswaRepository->resetMahasiswa($id);
        
        return $opr;
    }

    public function export($request){
        return $this->mahasiswaRepository->export($request);
    }

    public function import($request)
    {
        return $this->mahasiswaRepository->import($request);
    }

    public function updateOpenPanduan($request)
    {
        return $this->mahasiswaRepository->updateOpenPanduan($request);
    }
}
