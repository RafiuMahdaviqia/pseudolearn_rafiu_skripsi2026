<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Quiz\QuizController;
use App\Http\Controllers\Soal\SoalController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Guide\GuideController;
use App\Http\Controllers\Kelas\KelasController;
use App\Http\Controllers\Level\LevelController;
use App\Http\Controllers\Nyawa\NyawaController;
use App\Http\Controllers\Ujian\UjianController;
use App\Http\Controllers\Scoring\ScoringController;
use App\Http\Controllers\Konversi\KonversiController;
use App\Http\Controllers\Labeling\LabelingController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Mahasiswa\MahasiswaController;
use App\Http\Controllers\Confidence\ConfidenceController;
use App\Http\Controllers\Ujian\UjianCodeProgramController;
use App\Http\Controllers\Leaderboard\LeaderboardController;
use App\Http\Controllers\LogActivity\LogActivityController;
use App\Http\Controllers\LogChatbot\LogChatbotController;
use App\Http\Controllers\LogDataChatbot\LogDataChatbot as LogDataChatbotController;
use App\Http\Controllers\Overlapping\OverlappingController;
use App\Http\Controllers\UjianKonversi\UjianKonversiController;
use App\Models\Setting;
use App\Http\Controllers\Chatbot\ChatbotController;

Route::get('/', function () {
    return Auth::check() ? redirect('/dashboard') : redirect('/login');
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

Route::get('/maintenance-mahasiswa', function () {
    return view('auth.maintanceMahasiswa');
})->name('maintenance.mahasiswa');

Route::middleware(['auth', 'maintenance.mahasiswa'])->group(function() {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    // Dashboard untuk semua user
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/pencapaian-list', [DashboardController::class, 'dashboardPencapaianList'])->name('dashboard.pencapaian.list');
    Route::get('/dashboard/getDataGuide', [DashboardController::class, 'getDataGuide'])->name('dashboard.getDataGuide');
    
    // Role mahasiswa
    Route::middleware('role:mahasiswa')->group(function() {
        Route::prefix('quiz')->name('quiz.')->group(function() {
            Route::get('/', [QuizController::class, 'index'])->name('index');
            Route::get('/question-list', [QuizController::class, 'questionList'])->name('question-list');
            Route::post('/calculateAvgSkor', [QuizController::class, 'calculateAvgSkor'])->name('calculateAvgSkor');
        });

        Route::prefix('leaderboard')->name('leaderboard.')->group(function() {
            Route::get('/', [LeaderboardController::class, 'index'])->name('index');
            Route::post('/table', [LeaderboardController::class, 'table'])->name('table');
        });

        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function() {
            Route::get('/profile', [MahasiswaController::class, 'profile'])->name('profile');
            Route::post('/profile/get-data', [MahasiswaController::class, 'getProfileData'])->name('getDataProfile');
            Route::post('/profile/update', [MahasiswaController::class, 'updateProfile'])->name('updateProfile');
            Route::post('/update-open-panduan', [MahasiswaController::class, 'updateOpenPanduan'])->name('updateOpenPanduan');
        });

        Route::prefix('pencapaian')->name('pencapaian.')->group(function () {
            Route::get('/', [DashboardController::class, 'pencapaian'])->name('index');
            Route::get('/data', [DashboardController::class, 'getPencapaian']);
            Route::post('/claim', [DashboardController::class, 'claimPencapaian'])->name('claim');
            Route::get('getById', [DashboardController::class, 'getPencapaianById'])->name('getById');
        });

        Route::prefix('ujian')->name('ujian.')->group(function () {
            Route::get('/', [UjianController::class, 'index'])->name('index');
            Route::post('/submit', [UjianController::class, 'submit'])->name('submit');
            Route::post('/send-log', [UjianController::class, 'sendLog'])->name('send-log');
        });
        Route::prefix('code-program')->name('code-program.')->group(function () {
            Route::get('/', [UjianCodeProgramController::class, 'index'])->name('code-program.index');
            Route::post('submit-konversi', [UjianCodeProgramController::class, 'submitKonversi'])->name('code-program.submit-konversi');
        });
        Route::prefix('code-program')->name('code-program.')->group(function () {
            Route::get('/', [UjianCodeProgramController::class, 'index'])->name('code-program.index');
            Route::post('submit-konversi', [UjianCodeProgramController::class, 'submitKonversi'])->name('code-program.submit-konversi');
        });
        Route::prefix('nyawa')->name('nyawa.')->group(function () {
            Route::get('status', [NyawaController::class, 'status'])->name('nyawa.status');
        });

        // chatbot
        Route::prefix('chatbot')->name('chatbot.')->group(function() {
            Route::post('/send', [ChatbotController::class, 'send'])->name('send');
            Route::post('/open', [ChatbotController::class, 'open'])->name('open');
            Route::post('/close', [ChatbotController::class, 'close'])->name('close');
        });
    });

    // Role admin (dosen)
    Route::middleware('role:admin')->group(function() {
        Route::post('/dashboard/toggle-maintenance', [DashboardController::class, 'toggleMaintenance'])->name('dashboard.toggleMaintenance');
        
        // Dashboard
        Route::get('/dashboard/data-filter-admin', [DashboardController::class, 'dataFilterAdmin'])->name('dashboard.data-filter-admin');
        Route::get('/dashboard/chart-labeling', [DashboardController::class, 'chartLabeling'])->name('dashboard.chart-labeling');
        Route::get('/dashboard/chart-scoring', [DashboardController::class, 'chartScoring'])->name('dashboard.chart-scoring');
        Route::get('/dashboard/chart-confidence', [DashboardController::class, 'chartConfidence'])->name('dashboard.chart-confidence');
        Route::get('/dashboard/chart-aktivitas-ujian', [DashboardController::class, 'chartAktivitasUjian'])->name('dashboard.chart-aktivitas-ujian');
        Route::get('/dashboard/mahasiswa-online', [DashboardController::class, 'mahasiswaOnline'])->name('dashboard.mahasiswa-online');

        // Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function() {
            Route::get('/', [MahasiswaController::class, 'index'])->name('index');
            Route::post('/table', [MahasiswaController::class, 'table'])->name('table');
            Route::post('/store', [MahasiswaController::class, 'store'])->name('store');
            Route::get('/{id}', [MahasiswaController::class, 'getById'])->name('getById');
            Route::post('/update', [MahasiswaController::class, 'update'])->name('update');
            Route::delete('/{id}', [MahasiswaController::class, 'destroy'])->name('destroy');
            Route::post('/get-data', [MahasiswaController::class, 'getData'])->name('getData');
            Route::post('/reset/{id}', [MahasiswaController::class, 'resetMahasiswa'])->name('resetMahasiswa');
            Route::post('/export', [MahasiswaController::class, 'export'])->name('export');
            Route::post('/import', [MahasiswaController::class, 'import'])->name('import');
        });

        // Kelas
        Route::prefix('kelas')->name('kelas.')->group(function() {
            Route::get('/', [KelasController::class, 'index'])->name('index');
            Route::post('/table', [KelasController::class, 'table'])->name('table');
            Route::post('/store', [KelasController::class, 'store'])->name('store');
            Route::get('/{id}', [KelasController::class, 'getById'])->name('getById');
            Route::post('/update', [KelasController::class, 'update'])->name('update');
            Route::delete('/{id}', [KelasController::class, 'destroy'])->name('destroy');
            Route::post('/get-data', [KelasController::class, 'getData'])->name('getData');
        });

        // Soal
        Route::prefix('soal')->name('soal.')->group(function() {
            Route::get('/', [SoalController::class, 'index'])->name('index');
            Route::get('/order', [SoalController::class, 'order'])->name('order');
            Route::post('/table', [SoalController::class, 'table'])->name('table');
            Route::get('/form', [SoalController::class, 'form'])->name('form');
            Route::get('/form/{id}', [SoalController::class, 'form'])->name('formEdit');
            Route::post('/store', [SoalController::class, 'store'])->name('store');
            Route::delete('/{id}', [SoalController::class, 'destroy'])->name('destroy');
            Route::get('/{id}', [SoalController::class, 'getById'])->name('getById');
            Route::post('/saveOrder', [SoalController::class, 'saveOrder'])->name('saveOrder');
            Route::post('/updateStatusSoal', [SoalController::class, 'updateStatusSoal'])->name('updateStatusSoal');
        });

        // Level
        Route::prefix('level')->name('level.')->group(function() {
            Route::get('/', [LevelController::class, 'index'])->name('index');
            Route::get('/form', [LevelController::class, 'form'])->name('form');
            Route::get('/form/{id}', [LevelController::class, 'form'])->name('formEdit');
            Route::get('/order', [LevelController::class, 'order'])->name('order');
            Route::post('/table', [LevelController::class, 'table'])->name('table');
            Route::post('/store', [LevelController::class, 'store'])->name('store');
            Route::get('/{id}', [LevelController::class, 'getById'])->name('getById');
            Route::post('/update', [LevelController::class, 'update'])->name('update');
            Route::delete('/{id}', [LevelController::class, 'destroy'])->name('destroy');
            Route::post('/get-data', [LevelController::class, 'getData'])->name('getData');
            Route::post('/update-order', [LevelController::class, 'updateOrder'])->name('updateOrder');
            Route::post('/update-active', [LevelController::class, 'updateActive'])->name('updateActive');
        });

        // Konversi
        Route::prefix('konversi')->name('konversi.')->group(function(){
            Route::get('/', [KonversiController::class, 'index'])->name('index');
            Route::get('/form', [KonversiController::class, 'form'])->name('form');
            Route::get('/form/{id}', [KonversiController::class, 'form'])->name('formEdit');
            Route::get('/getSoalByLevel', [KonversiController::class, 'getSoalByLevel'])->name('getSoalByLevel');
            Route::post('/table', [KonversiController::class, 'table'])->name('table');
            Route::post('/store', [KonversiController::class, 'store'])->name('store');
            // Route::get('/{id}', [KonversiController::class, 'getById'])->name('getById');
            Route::post('/update', [KonversiController::class, 'update'])->name('update');
            Route::delete('/{id}', [KonversiController::class, 'destroy'])->name('destroy');
            // Route::post('/get-data', [KonversiController::class, 'getData'])->name('getData');
            Route::post('/runJava', [KonversiController::class, 'runKonversi'])->name('runJava');
        });

        // Overlapping
        Route::prefix('overlapping')->name('overlapping.')->group(function() {
            Route::get('/', [OverlappingController::class, 'index'])->name('index');
            Route::post('/tableSoal', [OverlappingController::class, 'tableSoal'])->name('tableSoal');
            // Route::get('/analysis/{id}', [OverlappingController::class, 'analysis'])->name('analysis');
            // Route::post('/analysis/data', [OverlappingController::class, 'data'])->name('analysis.data');
            // Route::get('/analysis/detail', [OverlappingController::class, 'detail'])->name('analysis.detail');
            // Route::post('/tableDetail', [OverlappingController::class, 'tableDetail'])->name('tableDetail');
        });

        Route::prefix('overlapping/analysis')->group(function () {
            // STATIS lebih dulu
            Route::get('detail', [OverlappingController::class, 'detail'])
                ->name('overlapping.analysis.detail');
            Route::post('data', [OverlappingController::class, 'data'])
                ->name('overlapping.analysis.data');
            Route::post('table-detail', [OverlappingController::class,'tableDetail'])
                ->name('overlapping.analysis.tableDetail');

            // Route dinamis terakhir + constraint (ULID/UUID campuran)
            Route::get('{id}', [OverlappingController::class, 'analysis'])
                ->where('id', '[A-Za-z0-9\-]+')
                ->name('overlapping.analysis');
        });

        // confidence
        Route::prefix('confidence')->name('confidence.')->group(function() {
            Route::get('/', [ConfidenceController::class, 'index'])->name('index');
            Route::post('/table', [ConfidenceController::class, 'table'])->name('table');
            Route::get('/detail/{id}', [ConfidenceController::class, 'detail'])->name('detail');
            Route::get('/detailLevel/{id}', [ConfidenceController::class, 'detailLevel'])->name('detailLevel');
            Route::get('/detailSoal/{id}', [ConfidenceController::class, 'detailSoal'])->name('detailSoal');
            Route::post('/tableDetail', [ConfidenceController::class, 'tableDetail'])->name('tableDetail');
            Route::post('/tableDetailSoal', [ConfidenceController::class, 'tableDetailSoal'])->name('tableDetailSoal');
            Route::post('/tableConfidence', [ConfidenceController::class, 'tableConfidence'])->name('tableConfidence');
        });

        // log-activity
        Route::prefix('log-activity')->name('log-activity.')->group(function() {
            Route::get('/', [LogActivityController::class, 'index'])->name('index');
            Route::post('/table', [LogActivityController::class, 'table'])->name('table');
            Route::post('/tableDetail', [LogActivityController::class, 'tableDetail'])->name('tableDetail');
            Route::post('/tableDetailSoal', [LogActivityController::class, 'tableDetailSoal'])->name('tableDetailSoal');
            Route::post('/tableDetailLog', [LogActivityController::class, 'tableDetailLog'])->name('tableDetailLog');
            Route::get('/detail/{id}', [LogActivityController::class, 'detail'])->name('detail');
            Route::get('/detailLevel/{id}', [LogActivityController::class, 'detailLevel'])->name('detailLevel');
            Route::get('/detailSoal/{id}', [LogActivityController::class, 'detailSoal'])->name('detailSoal'); 
            Route::get('/getSoalByLevel', [LogActivityController::class, 'getSoalByLevel'])->name('log-activity.getSoalByLevel');
            Route::post('/export', [LogActivityController::class, 'export'])->name('export');
        });

        // log-data-chatbot
        Route::prefix('log-data-chatbot')->name('log-data-chatbot.')->group(function() {
            Route::get('/', [LogDataChatbotController::class, 'index'])->name('index');
            Route::post('/table', [LogDataChatbotController::class, 'table'])->name('table');
            Route::get('/detail/{id}', [LogDataChatbotController::class, 'detail'])->name('detail');
            Route::get('/getSoalByLevel', [LogDataChatbotController::class, 'getSoalByLevel'])->name('getSoalByLevel');
            Route::post('/export', [LogDataChatbotController::class, 'export'])->name('export');
        });

        // log-chatbot
        Route::prefix('log-chatbot')->name('log-chatbot.')->group(function() {
            Route::get('/', [LogChatbotController::class, 'index'])->name('index');
            Route::post('/table', [LogChatbotController::class, 'table'])->name('table');
            Route::get('/detail/{id}', [LogChatbotController::class, 'detail'])->name('detail');
            Route::get('/detail-pesan/{id}', [LogChatbotController::class, 'detailPesan'])->name('detailPesan');
            Route::get('/getSoalByLevel', [LogChatbotController::class, 'getSoalByLevel'])->name('getSoalByLevel');
            Route::post('/export', [LogChatbotController::class, 'export'])->name('export');
        });

        // labeling
        Route::prefix('labeling')->name('labeling.')->group(function() {
            Route::get('/', [LabelingController::class, 'index'])->name('index');
            Route::post('/table', [LabelingController::class, 'table'])->name('table');
            Route::post('/update-test', [LabelingController::class, 'updateTest'])->name('labeling.update-test');
            Route::post('/calculate-manual', [LabelingController::class, 'calculateManual'])->name('calculate-manual');
            Route::post('/export', [LabelingController::class, 'export'])->name('export');
        });

        // scoring
        Route::prefix('scoring')->name('scoring.')->group(function() {
            Route::get('/', [ScoringController::class, 'index'])->name('index');
            Route::post('/table', [ScoringController::class, 'table'])->name('table');
            Route::post('/update-test', [ScoringController::class, 'updateTest'])->name('scoring.update-test');
            Route::post('/calculate-manual', [ScoringController::class, 'calculateManual'])->name('calculate-manual');
            Route::post('/calculate-average', [ScoringController::class, 'calculateAverage'])->name('calculate-average');
            Route::post('/export', [ScoringController::class, 'export'])->name('export');
        });

        // ujian konversi
        Route::prefix('ujian-konversi')->name('ujian-konversi.')->group(function() {
            Route::get('/', [UjianKonversiController::class, 'index'])->name('index');
            Route::post('/table', [UjianKonversiController::class, 'table'])->name('table');
            Route::get('/detail/{id}', [UjianKonversiController::class, 'detail'])->name('detail');
            Route::post('/table-detail', [UjianKonversiController::class, 'tableDetail'])->name('tableDetail');
            Route::get('/detail-konversi/{id}', [UjianKonversiController::class, 'detailKonversi'])->name('detailKonversi');
        });

        // Guide
        Route::prefix('guide')->name('guide.')->group(function() {
            Route::get('/', [GuideController::class, 'index'])->name('index');
            Route::get('getData', [GuideController::class, 'getData'])->name('getData');
            Route::get('getDataById/{id}', [GuideController::class, 'getDataById'])->name('getDataById');
            Route::post('saveData', [GuideController::class, 'saveData'])->name('saveData');
        });

        // Setting Admin
        Route::prefix('setting-admin')->name('setting-admin.')->group(function() {
            Route::get('/', [MahasiswaController::class, 'settingAdmin'])->name('index');
            Route::post('/update', [MahasiswaController::class, 'updateSettingAdmin'])->name('updateSettingAdmin');
        });
    });
});