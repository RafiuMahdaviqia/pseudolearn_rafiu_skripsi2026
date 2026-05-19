<?php
// Backup current `soal` table (rename) and import extracted soal.sql into current DB
$base = __DIR__;
$envFile = $base . '/../.env';
if (!file_exists($envFile)) { fwrite(STDERR, ".env not found\n"); exit(1); }
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$db = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (!str_contains($line, '=')) continue;
    list($k,$v) = explode('=', $line, 2);
    $k = trim($k); $v = trim($v); $v = trim($v, "'\"");
    if (str_starts_with($k, 'DB_')) $db[strtolower(substr($k,3))] = $v;
}
$host = $db['host'] ?? '127.0.0.1'; $port = $db['port'] ?? '3306'; $database = $db['database'] ?? ''; $user = $db['username'] ?? 'root'; $pass = $db['password'] ?? '';
// locate the extracted soal.sql (prefer extracted_ folder)
$candidates = [];
foreach (glob($base . '/extracted_*') as $d) {
    $f = $d . '/soal.sql'; if (file_exists($f)) $candidates[$d] = $f;
}
if (empty($candidates)) {
    // fallback to exports
    foreach (glob($base . '/exports*') as $d) {
        $f = $d . '/soal.sql'; if (file_exists($f)) $candidates[$d] = $f;
    }
}
if (empty($candidates)) { fwrite(STDERR, "No soal.sql found in scripts/ (extracted or exports)\n"); exit(1); }
// pick latest by directory name
ksort($candidates);
$sqlFile = end($candidates);
echo "Using import file: $sqlFile\n";
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) { fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n"); exit(1); }
// check if table exists
$exists = (bool)$pdo->query("SHOW TABLES LIKE 'soal'")->fetchColumn();
$ts = date('Ymd_His');
if ($exists) {
    $backupName = 'soal_backup_' . $ts;
    echo "Renaming existing table `soal` to `$backupName`...\n";
    $pdo->exec("RENAME TABLE `soal` TO `$backupName`");
    echo "Renamed to $backupName\n";
}
// import SQL statements
$content = file_get_contents($sqlFile);
// remove USE / CREATE DATABASE statements
$content = preg_replace('/USE `[^`]+`;\n?/i', '', $content);
$content = preg_replace('/CREATE DATABASE .*?;\n?/is', '', $content);
$content = str_replace(['\r\n','\r'],"\n", $content);
$parts = preg_split('/;\n/', $content);
foreach ($parts as $part) {
    $part = trim($part);
    if ($part === '') continue;
    try { $pdo->exec($part); } catch (Exception $e) { echo "-- stmt failed: " . substr($part,0,120) . " => " . $e->getMessage() . "\n"; }
}
$cnt = $pdo->query("SELECT COUNT(*) FROM `soal`")->fetchColumn();
echo "Imported `soal` rows: $cnt\n";
echo "Done.\n";
