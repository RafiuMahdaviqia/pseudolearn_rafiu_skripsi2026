<?php
$dir = __DIR__ . '/../db-backups';
$files = glob($dir . '/*.sql');
$targets = ['bank_soal_konversi','log_ujian_kode','ars_result'];
foreach ($files as $f) {
    echo "Checking $f\n";
    $s = file_get_contents($f);
    foreach ($targets as $t) {
        $count = preg_match_all('/`'.preg_quote($t,'/').'`/i', $s, $m);
        echo "  $t occurrences: $count\n";
    }
}
