<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Models\User;
use App\Core\BaseResponse;
use Illuminate\Http\Request;
use App\Services\KelasService;
use App\Services\MahasiswaService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MahasiswaController extends Controller
{
    protected $mahasiswaService;
    protected $kelasService;
    public function __construct()
    {
        $this->mahasiswaService = new MahasiswaService();
        $this->kelasService = new KelasService();
    }
    public function index()
    {
        $response = $this->kelasService->getData();
        // Jika response adalah JsonResponse, ambil data aslinya
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            $dataKelas = $response->getData(true);
        } else {
            $dataKelas = $response;
        }

        
        // Tambahkan opsi "Semua Kelas" di awal array
        array_unshift($dataKelas, [
            'id' => '',
            'name' => 'Semua Kelas',
            'angkatan' => ''
        ]);

        // Tata kolom name menjadi "name (angkatan)"
        foreach ($dataKelas as &$kelas) {
            if (!empty($kelas['name']) && !empty($kelas['angkatan'])) {
                $kelas['name'] = $kelas['name'] . ' (' . $kelas['angkatan'] . ')';
            }
        }
        unset($kelas);

        return view('pages.mahasiswa.index', [
            'title' => 'Data Mahasiswa', // Judul untuk ditampilkan di navbar
            'dataKelas' => $dataKelas
        ]);
    }

    public function profile()
    {
        return view('pages.mahasiswa.profile', [
            'title' => 'Profile Mahasiswa' // Judul untuk ditampilkan di navbar
        ]);
    }

    public function table(Request $request)
    {
        $opr = $this->mahasiswaService->table($request);
        return $opr;
    }

    public function store(Request $request)
    {
        $opr = $this->mahasiswaService->store($request);
        return $opr;
    }

    public function getById($id)
    {
        $opr = $this->mahasiswaService->getById($id);
        return $opr;
    }

    public function update(Request $request)
    {
        $opr = $this->mahasiswaService->update($request);
        return $opr;
    }

    public function destroy($id)
    {
        $opr = $this->mahasiswaService->destroy($id);
        return $opr;
    }

    public function getProfileData(Request $request)
    {
        $id = $request->input('id');
        $opr = $this->mahasiswaService->getById($id);
        return $opr;
    }

    public function updateProfile(Request $request)
    {
        $opr = $this->mahasiswaService->updateProfile($request);
        return $opr;
    }
    
    public function resetMahasiswa($id)
    {
        $opr = $this->mahasiswaService->resetMahasiswa($id);
        return $opr;
    }

    public function export(Request $request)
    {
        $opr = $this->mahasiswaService->export($request);
        return $opr;
    }

    public function import(Request $request)
    {
        $opr = $this->mahasiswaService->import($request);
        return $opr;
    }

    public function updateOpenPanduan(Request $request)
    {
        $opr = $this->mahasiswaService->updateOpenPanduan($request);
        return $opr;
    }

    public function settingAdmin()
    {
        $user = Auth::user();

        return view('pages.mahasiswa.settingAdmin', [
            'title' => 'Setting Admin',
            'user' => $user
        ]);
    }

    public function updateSettingAdmin(Request $request)
    {
        $userData = User::find($request->input('id'));
        
        if (!$userData) {
            return BaseResponse::errorMessage('User tidak ditemukan.', 404);
        }

        DB::beginTransaction();
        try {
            $avatarFilename = $userData->avatar; // default pakai yang lama

            // === Jika upload file baru ===
            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');

                $allowedMime = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
                if (!in_array($file->getMimeType(), $allowedMime)) {
                    return BaseResponse::errorMessage('Avatar harus berupa file gambar webp, jpg, jpeg, atau png.', 422);
                }

                if ($file->getSize() > 2 * 1024 * 1024) {
                    return BaseResponse::errorMessage('Ukuran avatar maksimal 2MB.', 422);
                }

                $filename = 'avatar_' . bin2hex(random_bytes(8)) . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('assets/media/avatars'), $filename);

                $avatarFilename = $filename;
            }
            // === Jika pilih avatar default ===
            elseif ($request->filled('avatar_default')) {
                $basename = basename($request->input('avatar_default'));
                $allowed = [
                    'avatar1.webp', 'avatar2.webp', 'avatar3.webp', 'avatar4.webp',
                    'avatar5.webp', 'avatar6.webp', 'avatar7.webp', 'avatar8.webp', 'blank.png'
                ];

                if (!in_array($basename, $allowed)) {
                    return BaseResponse::errorMessage('Avatar default tidak valid.', 422);
                }

                $avatarFilename = $basename === 'blank.png' ? null : $basename;
            }

            // === Update data user ===
            $userData->name = $request->input('name', $userData->name);
            $userData->email = $request->input('email', $userData->email);

            if ($request->filled('new_password')) {
                $userData->password = Hash::make($request->input('new_password'));
            }

            $userData->avatar = $avatarFilename;
            $userData->save();

            DB::commit();
            return BaseResponse::updated('User updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage('Failed to update user. ' . $e->getMessage(), 500);
        }
    }

}
