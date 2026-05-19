<?php
// Dump specified tables (soal and soal_backup_*) from current DB into SQL files
$envFile = __DIR__ . '/../.env';
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
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) { fwrite(STDERR, "DB connect failed: " . $e->getMessage() . "\n"); exit(1); }
$tables = ['soal'];
// find backup tables
$stmt = $pdo->query("SHOW TABLES LIKE 'soal_backup_%'");
while ($r = $stmt->fetch(PDO::FETCH_NUM)) $tables[] = $r[0];
$ts = date('Ymd_His');
$outDir = __DIR__ . "/dumps_$ts";
if (!is_dir($outDir)) mkdir($outDir,0755,true);
foreach ($tables as $t) {
    try {
        $create = $pdo->query("SHOW CREATE TABLE `{$t}`")->fetch(PDO::FETCH_ASSOC);
        $createSql = $create['Create Table'] ?? $create['Create View'] ?? null;
        $out = "-- Dump for $t\nDROP TABLE IF EXISTS `{$t}`;\n";
        if ($createSql) $out .= $createSql . ";\n\n";
        $sel = $pdo->query("SELECT * FROM `{$t}`");
        $rows = $sel->fetchAll(PDO::FETCH_ASSOC);
        if (count($rows) > 0) {
            $cols = array_map(function($c){ return "`$c`"; }, array_keys($rows[0]));
            $out .= "INSERT INTO `{$t}` (" . implode(',', $cols) . ") VALUES\n";
            $vals = [];
            foreach ($rows as $r) {
                $v = array_map(function($x) use ($pdo){ if ($x === null) return 'NULL'; return $pdo->quote((string)$x); }, array_values($r));
                $vals[] = '(' . implode(',', $v) . ')';
            }
            $out .= implode(",\n", $vals) . ";\n";
        }
        $file = $outDir . "/{$t}.sql";
        file_put_contents($file, $out);
        echo "Wrote $file\n";
    } catch (Exception $e) {
        echo "Skipping $t: " . $e->getMessage() . "\n";
    }
}
echo "Dumps saved to $outDir\n";
