<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Kelas;
use App\Models\Level;
use App\Models\Nyawa;
use App\Models\Mahasiswa;
use App\Core\BaseResponse;
use Illuminate\Support\Str;
use App\Exports\MahasiswaExport;
use App\Imports\MahasiswaImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\DeletePencapaianMahasiswa;
use App\Jobs\GeneratePencapaianKonversi;
use App\Jobs\GeneratePencapaianMahasiswa;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Class KelasRepository.
 * 
 * @package namespace App\Repositories;
 */
class MahasiswaRepository extends BaseRepository
{

    protected $model;
    protected $userModel;
    protected $kelasModel;

    public function __construct()
    {
        $this->model = new Mahasiswa();
        $this->userModel = new User();
        $this->kelasModel = new Kelas();
    }

    /**
     * Specify the model class name.
     *
     * @return string
     */
    public function model()
    {
        return $this->model;
    }

    /**
     * Boot up the repository, pushing criteria.
     *
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function boot()
    {
        // Add your boot logic here
    }

    public function table($request)
    {
        $opr = $this->model->setView('v_mahasiswa');

        $filterKelas = $request->input('filter_kelas');
        if (!is_null($filterKelas) && $filterKelas !== '') {
            $opr = $opr->where('id_kelas', $filterKelas);
        }

        $opr = $opr->draw();

        return $opr;
    }

    public function store($data)
    {
        try {
            DB::beginTransaction();

            $dataUser = [
                'id' => (string) Str::uuid(),
                'name' => $data['nama'],
                'email' => $data['nim'] . '@gmail.com',
                'email_verified_at' => null,
                'password' => Hash::make($data['nim']),
                'is_admin' => 0,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];

            $user = $this->userModel->create($dataUser);

            if (!$user) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal membuat user', 500);
            }

            $dataMahasiswa = [
                'id_user' => $dataUser['id'],
                'id_kelas' => $data['select-kelas'] ?? null,
                'nim' => $data['nim'],
                'name' => $data['nama'],
                'jenis_kelamin' => $data['select-jenis-kelamin'] ?? null,
                'open_panduan' => 0, // default belum dibuka
            ];

            $mahasiswa = $this->model->create($dataMahasiswa);
            if (!$mahasiswa) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal menyimpan data mahasiswa', 500);
            }

            $dataNyawa = [
                'id_mahasiswa' => $mahasiswa->id,
                'id_user' => $dataUser['id'],
                'nyawa' => 100,
                'max_nyawa' => 100,
                'next_regen_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $nyawa = Nyawa::create($dataNyawa);
            if (!$nyawa) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal menyimpan data nyawa', 500);
            }

            DB::commit();

            // Dispatch job per Level
            $levelList = Level::all();
            foreach ($levelList as $level) {
                GeneratePencapaianMahasiswa::dispatch($mahasiswa, $level->id);
            }

            return BaseResponse::created($mahasiswa);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function getById($id)
    {
        $opr = $this->model->setView('v_mahasiswa')
            ->where('id', $id)
            ->first();

        if (!$opr) {
            return BaseResponse::errorMessage('Mahasiswa tidak ditemukan.', 404);
        }

        return BaseResponse::json($opr);
    }

    public function updateData($data)
    {
        try {
            DB::beginTransaction();
            $mahasiswa = $this->model->find($data['id']);
            $user = $this->userModel->find($mahasiswa->id_user);

            if (!$mahasiswa || !$user) {
                DB::rollBack();
                return BaseResponse::errorMessage('Data mahasiswa atau user tidak ditemukan.', 404);
            }
            // Update user data
            $dataUser = [
                'name' => $data['nama'] ?? $user->name,
                'email' => $data['nim'] ? $data['nim'] . '@gmail.com' : $user->email,
                'password' => isset($data['nim']) ? Hash::make($data['nim']) : $user->password,
                'updated_at' => now(),
            ];

            $opr = $user->update($dataUser);

            if (!$opr) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal memperbarui data user.', 500);
            }

            // Update mahasiswa data
            $dataMahasiswa = [
                'id_kelas' => $data['select-kelas'] ?? $mahasiswa->id_kelas,
                'nim' => $data['nim'] ?? $mahasiswa->nim,
                'name' => $data['nama'] ?? $mahasiswa->name,
                'jenis_kelamin' => $data['select-jenis-kelamin'] ?? $mahasiswa->jenis_kelamin,
                'open_panduan' => $mahasiswa->open_panduan,
                'updated_at' => now(),
            ];

            $opr = $mahasiswa->update($dataMahasiswa);
            if (!$opr) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal memperbarui data mahasiswa.', 500);
            }
            
            DB::commit();
            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $mahasiswa = $this->model->find($id);
            if (!$mahasiswa) {
                return BaseResponse::errorMessage('Mahasiswa tidak ditemukan.', 404);
            }

            // Soft delete user
            $user = $this->userModel->find($mahasiswa->id_user);
            if ($user) {
                $user->delete();
            }

            // Soft delete nyawa
            $nyawa = Nyawa::where('id_mahasiswa', $mahasiswa->id)->first();
            if ($nyawa) {
                $nyawa->delete();
            }

            // Soft delete mahasiswa
            $opr = $mahasiswa->delete();

            DB::commit();

            DeletePencapaianMahasiswa::dispatch($mahasiswa);
            return BaseResponse::deleted($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function updateDataProfile($data)
    {
        try {
            DB::beginTransaction();
            $mahasiswa = $this->model->find($data['id']);
            $user = $this->userModel->find($mahasiswa->id_user);

            if (!$mahasiswa || !$user) {
                DB::rollBack();
                return BaseResponse::errorMessage('Data mahasiswa atau user tidak ditemukan.', 404);
            }
            // Update user data
            $dataUser = [
                'name' => $data['nama'] ?? $user->name,
                'avatar' => $data['avatar_filename'] ?? $user->avatar,
                'updated_at' => now(),
            ];

            if(isset($data['new_password']) && !empty($data['new_password'])) {
                $dataUser['password'] = Hash::make($data['new_password']);
            }

            $opr = $user->update($dataUser);

            if (!$opr) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal memperbarui data user.', 500);
            }

            // Update mahasiswa data
            $dataMahasiswa = [
                'name' => $data['nama'] ?? $mahasiswa->name,
                'updated_at' => now(),
            ];

            $opr = $mahasiswa->update($dataMahasiswa);
            if (!$opr) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal memperbarui data mahasiswa.', 500);
            }
            
            DB::commit();
            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function resetMahasiswa($id)
    {
        try {
            DB::beginTransaction();
            $mahasiswa = $this->model->find($id);
            if (!$mahasiswa) {
                return BaseResponse::errorMessage('Mahasiswa tidak ditemukan.', 404);
            }

            // Reset password and email from nim
            $user = $this->userModel->find($mahasiswa->id_user);
            if (!$user) {
                DB::rollBack();
                return BaseResponse::errorMessage('User tidak ditemukan.', 404);
            }
            $dataUser = [
                'email' => $mahasiswa->nim . '@gmail.com',
                'password' => Hash::make($mahasiswa->nim),
                'updated_at' => now(),
            ];
            $opr = $user->update($dataUser);

            if (!$opr) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal mereset data user.', 500);
            }

            // Reset mahasiswa data
            $dataMahasiswa = [
                'nim' => $mahasiswa->nim,
                'name' => $mahasiswa->name,
                'jenis_kelamin' => $mahasiswa->jenis_kelamin,
                'open_panduan' => 0, // reset ke belum dibuka
                'updated_at' => now(),
            ];
            $opr = $mahasiswa->update($dataMahasiswa);
            if (!$opr) {
                DB::rollBack();
                return BaseResponse::errorMessage('Gagal mereset data mahasiswa.', 500);
            }

            DB::commit();
            return BaseResponse::updated($opr);
        } catch (\Exception $e) {
            DB::rollBack();
            return BaseResponse::errorMessage($e->getMessage());
        }
    }

    public function export($request)
    {
        $idKelas = $request->input('id_kelas');
        $namaKelas = null;

        if (!is_null($idKelas) && $idKelas !== '') {
            $kelas = $this->kelasModel->where('id', $idKelas)->first();
            $namaKelas = $kelas ? $kelas->name : null;
        }

        $export = new MahasiswaExport($idKelas);

        $fileName = 'Rekapitulasi Data Mahasiswa';
        if ($namaKelas) {
            $fileName .= ' Kelas ' . $namaKelas;
        }
        $fileName .= '.xlsx';

        return Excel::download($export, $fileName);
    }

    public function import($request)
    {
        try {
            $file = $request->file('import_mahasiswa_file');
            if (!$file) {
                return BaseResponse::errorMessage('File tidak ditemukan.', 400);
            }

            Excel::import(new MahasiswaImport(), $file);

            return BaseResponse::json([
                'message' => 'Data mahasiswa berhasil diimpor.',
                'status' => 'success'
            ]);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage('Gagal mengimpor data: ' . $e->getMessage(), 500);
        }
    }

    public function updateOpenPanduan($request)
    {
        try {
            $mahasiswa = $this->model->find($request->input('id_mahasiswa'));
            if (!$mahasiswa) {
                return BaseResponse::errorMessage('Mahasiswa tidak ditemukan.', 404);
            }

            $mahasiswa->open_panduan = $request->input('open_panduan', $mahasiswa->open_panduan);
            $mahasiswa->save();

            return BaseResponse::updated($mahasiswa);
        } catch (\Exception $e) {
            return BaseResponse::errorMessage('Gagal memperbarui status panduan: ' . $e->getMessage(), 500);
        }
    }
}
