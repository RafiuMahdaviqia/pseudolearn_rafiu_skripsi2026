<?php
$envFile = __DIR__ . '/../.env';
if (!file_exists($envFile)) {
    fwrite(STDERR, ".env not found\n");
    exit(1);
}
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$db = [];
foreach ($lines as $line) {
    if (strpos(trim($line), '#') === 0) continue;
    if (!str_contains($line, '=')) continue;
    list($k, $v) = explode('=', $line, 2);
    $k = trim($k);
    $v = trim($v);
    $v = trim($v, "'\"");
    if (str_starts_with($k, 'DB_')) {
        $db[strtolower(substr($k, 3))] = $v;
    }
}
$host = $db['host'] ?? '127.0.0.1';
$port = $db['port'] ?? '3306';
$database = $db['database'] ?? '';
$user = $db['username'] ?? 'root';
$pass = $db['password'] ?? '';
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    fwrite(STDERR, "DB connection failed: " . $e->getMessage() . "\n");
    exit(1);
}
$tables = ['bank_soal_konversi','soal','log_ujian_kode','ars_result'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM `$t`");
        $cnt = $stmt->fetchColumn();
        echo "$t: $cnt\n";
    } catch (Exception $e) {
        echo "$t: ERROR - " . $e->getMessage() . "\n";
    }
}
