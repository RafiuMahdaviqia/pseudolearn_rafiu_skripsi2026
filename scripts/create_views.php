<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$views = [
    'v_bank_soal_konversi' => <<<'SQL'
CREATE OR REPLACE VIEW v_bank_soal_konversi AS
SELECT
    bsk.id,
    bsk.id_level,
    l.name AS level_name,
    bsk.id_soal,
    s.judul AS judul_soal,
    s.soal AS soal_name,
    bsk.jawaban,
    bsk.output,
    s.`order` AS `order`,
    CASE
        WHEN bsk.difficulty IS NOT NULL THEN bsk.difficulty
        WHEN s.difficulty IS NOT NULL THEN s.difficulty
        ELSE 'easy'
    END AS difficulty,
    bsk.created_at,
    bsk.updated_at,
    bsk.deleted_at,
    s.status
FROM bank_soal_konversi bsk
LEFT JOIN level l ON bsk.id_level = l.id
LEFT JOIN soal s ON bsk.id_soal = s.id
SQL,
    'v_ujian_kode' => <<<'SQL'
CREATE OR REPLACE VIEW v_ujian_kode AS
SELECT
    uk.id,
    uk.id_level,
    uk.id_bank_soal_konversi,
    bsk.id_soal,
    uk.id_mahasiswa,
    m.id_user,
    m.id_kelas,
    l.name AS level_name,
    k.name AS kelas_name,
    s.judul AS judul_soal,
    m.nim,
    m.name,
    uk.jawaban,
    uk.output,
    uk.nilai,
    uk.waktu,
    uk.created_at,
    uk.updated_at,
    uk.deleted_at
FROM ujian_kode uk
LEFT JOIN bank_soal_konversi bsk ON uk.id_bank_soal_konversi = bsk.id
LEFT JOIN soal s ON bsk.id_soal = s.id
LEFT JOIN mahasiswa m ON uk.id_mahasiswa = m.id
LEFT JOIN kelas k ON m.id_kelas = k.id
JOIN level l ON uk.id_level = l.id
SQL,
    'v_pseudo_konversicode' => <<<'SQL'
CREATE OR REPLACE VIEW v_pseudo_konversicode AS SELECT * FROM (
    SELECT
        u.id_mahasiswa,
        u.id_level,
        s.id AS id_soal,
        NULL AS id_konversi,
        'pseudo' AS jenis_soal,
        s.difficulty,
        COUNT(ld.id) AS langkah,
        u.waktu AS durasi,
        u.created_at AS created_at,
        ROW_NUMBER() OVER (PARTITION BY u.id_mahasiswa, u.id_level, s.id ORDER BY u.created_at) AS attempt_index,
        ROW_NUMBER() OVER (PARTITION BY u.id_mahasiswa, u.id_level ORDER BY u.created_at) AS pair_index,
        ROW_NUMBER() OVER (PARTITION BY u.id_mahasiswa, u.id_level ORDER BY u.created_at) AS event_index
    FROM ujian u
    JOIN soal s ON s.id = u.id_soal
    LEFT JOIN log_data ld
        ON ld.id_soal = s.id
        AND ld.id_mahasiswa = u.id_mahasiswa
    GROUP BY
        u.id_mahasiswa,
        u.id_level,
        s.id,
        s.difficulty,
        u.waktu,
        u.created_at

    UNION ALL

    SELECT
        uk.id_mahasiswa,
        uk.id_level,
        bsk.id_soal AS id_soal,
        bsk.id AS id_konversi,
        'konversi' AS jenis_soal,
        bsk.difficulty,
        COUNT(lk.id) AS langkah,
        uk.waktu AS durasi,
        uk.created_at AS created_at,
        ROW_NUMBER() OVER (PARTITION BY uk.id_mahasiswa, uk.id_level, bsk.id_soal ORDER BY uk.created_at) AS attempt_index,
        ROW_NUMBER() OVER (PARTITION BY uk.id_mahasiswa, uk.id_level ORDER BY uk.created_at) AS pair_index,
        ROW_NUMBER() OVER (PARTITION BY uk.id_mahasiswa, uk.id_level ORDER BY uk.created_at) AS event_index
    FROM ujian_kode uk
    JOIN bank_soal_konversi bsk ON bsk.id = uk.id_bank_soal_konversi
    LEFT JOIN log_ujian_kode lk
        ON lk.id_bank_soal_konversi = bsk.id
        AND lk.id_mahasiswa = uk.id_mahasiswa
    GROUP BY
        uk.id_mahasiswa,
        uk.id_level,
        bsk.id_soal,
        bsk.id,
        bsk.difficulty,
        uk.waktu,
        uk.created_at
) base ORDER BY created_at ASC
SQL,
    'v_ujian_konversi' => <<<'SQL'
CREATE OR REPLACE VIEW v_ujian_konversi AS
SELECT
    uk.id,
    uk.id_level,
    uk.id_soal_konversi,
    uk.id_mahasiswa,
    uk.jawaban,
    uk.output,
    uk.waktu,
    uk.nilai,
    uk.created_at,
    uk.updated_at,
    uk.deleted_at
FROM ujian_konversi uk
SQL,
    'v_konversi' => <<<'SQL'
CREATE OR REPLACE VIEW v_konversi AS
SELECT
    k.id,
    k.id_level,
    l.name AS level_name,
    k.id_soal,
    s.judul AS judul_soal,
    s.soal AS soal_name,
    k.jawaban,
    k.output,
    k.bobot,
    k.created_at,
    k.updated_at,
    k.deleted_at,
    s.status
FROM konversi k
LEFT JOIN level l ON k.id_level = l.id
LEFT JOIN soal s ON k.id_soal = s.id
SQL,
];

foreach ($views as $name => $sql) {
    try {
        echo "Creating view: {$name}\n";
        \Illuminate\Support\Facades\DB::statement("DROP VIEW IF EXISTS {$name}");
        \Illuminate\Support\Facades\DB::statement($sql);
        echo "Created: {$name}\n";
    } catch (Throwable $e) {
        echo "Failed to create {$name}: {$e->getMessage()}\n";
    }
}

echo "Done.\n";
