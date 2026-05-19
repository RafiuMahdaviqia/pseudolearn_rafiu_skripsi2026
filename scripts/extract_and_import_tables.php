<?php
// Extract CREATE TABLE and INSERTs for specific tables from backup and import into a new temp DB
$backupFile = __DIR__ . '/../db-backups/pseudolearn2026.sql';
if (!file_exists($backupFile)) { fwrite(STDERR, "Backup not found\n"); exit(1); }
$tables = ['bank_soal_konversi','soal','log_ujian_kode','ars_result'];
$sql = file_get_contents($backupFile);
$origDb = 'pseudolearn2026';
// Normalize
$sql = str_replace(["\r\n","\r"], "\n", $sql);
// Remove DELIMITER lines to simplify
$sql = preg_replace('/DELIMITER\s+\S+/i', "", $sql);
$outDir = __DIR__ . '/extracted_' . date('Ymd_His');
mkdir($outDir, 0755, true);
foreach ($tables as $t) {
    $patternCreate = '/CREATE\s+TABLE\s+(?:`' . preg_quote($origDb, '/') . '`\.)?`' . preg_quote($t, '/') . '`.*?;\n/s';
    $patternCreate2 = '/CREATE\s+TABLE\s+(?:`' . preg_quote($t, '/') . '`).*?;\n/s';
    $create = '';
    if (preg_match($patternCreate, $sql, $m)) $create = $m[0];
    elseif (preg_match($patternCreate2, $sql, $m)) $create = $m[0];
    // collect INSERTs
    $patternInsert = '/INSERT\s+INTO\s+(?:`' . preg_quote($origDb, '/') . '`\.)?`' . preg_quote($t, '/') . '`.*?;\n/s';
    preg_match_all($patternInsert, $sql, $ins);
    $insertSql = '';
    if (!empty($ins[0])) $insertSql = implode("\n", $ins[0]);
    if ($create === '' && $insertSql === '') {
        echo "$t: not found in dump\n";
        continue;
    }
    // Replace qualified names with unqualified
    $create = preg_replace('/`' . preg_quote($origDb, '/') . '`\./', '', $create);
    $insertSql = preg_replace('/`' . preg_quote($origDb, '/') . '`\./', '', $insertSql);
    $content = "-- Extracted $t\n" . $create . "\n" . $insertSql . "\n";
    $file = "$outDir/$t.sql";
    file_put_contents($file, $content);
    echo "Wrote $file\n";
}

// Now import into a fresh temp DB
$envFile = __DIR__ . '/../.env';
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$db = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (!str_contains($line, '=')) continue;
    list($k, $v) = explode('=', $line, 2);
    $k = trim($k); $v = trim($v); $v = trim($v, "'\"");
    if (str_starts_with($k, 'DB_')) $db[strtolower(substr($k,3))] = $v;
}
$host = $db['host'] ?? '127.0.0.1'; $port = $db['port'] ?? '3306'; $user = $db['username'] ?? 'root'; $pass = $db['password'] ?? '';
$tempDb = 'pseudolearn_restore_extracted_' . date('Ymd_His');
try {
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$tempDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Created temp DB: $tempDb\n";
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$tempDb;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) { fwrite(STDERR, "DB error: " . $e->getMessage() . "\n"); exit(1); }

foreach (glob($outDir . '/*.sql') as $f) {
    $content = file_get_contents($f);
    // split by ;\n
    $parts = preg_split('/;\n/', $content);
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p === '') continue;
        try { $pdo->exec($p); } catch (Exception $e) { echo "Import failed for " . substr($p,0,120) . " => " . $e->getMessage() . "\n"; }
    }
}

// Report counts
foreach ($tables as $t) {
    try { $cnt = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn(); echo "$t: $cnt\n"; } catch (Exception $e) { echo "$t: missing\n"; }
}

echo "Extract+import complete. Exports: $outDir, Temp DB: $tempDb\n";
