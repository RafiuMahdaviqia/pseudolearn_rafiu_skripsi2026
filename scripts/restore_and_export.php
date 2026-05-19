<?php
// Restore db-backups/pseudolearn2026.sql into a temporary DB and export specific tables
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
$user = $db['username'] ?? 'root';
$pass = $db['password'] ?? '';
$backupFile = __DIR__ . '/../db-backups/pseudolearn2026.sql';
if (!file_exists($backupFile)) {
    fwrite(STDERR, "Backup file not found: $backupFile\n");
    exit(1);
}
$ts = date('Ymd_His');
$tempDb = "pseudolearn_restore_$ts";
try {
    $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::MYSQL_ATTR_MULTI_STATEMENTS => false]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$tempDb` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "Created temp DB: $tempDb\n";
    // reconnect to temp DB
    $dsnDb = "mysql:host=$host;port=$port;dbname=$tempDb;charset=utf8mb4";
    $pdo = new PDO($dsnDb, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    fwrite(STDERR, "DB error: " . $e->getMessage() . "\n");
    exit(1);
}
$sql = file_get_contents($backupFile);
// Remove DELIMITER lines and leading CREATE DATABASE/USE statements
$sql = preg_replace('/DELIMITER\s+\S+/i', "", $sql);
$sql = preg_replace('/CREATE DATABASE .*?;/is', "", $sql);
$sql = preg_replace('/USE `[^`]+`;/is', "", $sql);
// Normalize line endings
$sql = str_replace(["\r\n","\r"], "\n", $sql);
// Split by semi-colon followed by newline
$parts = preg_split('/;\n/', $sql);
echo "Importing SQL statements into $tempDb...\n";
$count = 0;
foreach ($parts as $part) {
    $part = trim($part);
    if ($part === '') continue;
    try {
        $pdo->exec($part);
        $count++;
    } catch (Exception $e) {
        // skip statements that fail but report them
        echo "-- Statement failed: " . substr($part,0,120) . "... => " . $e->getMessage() . "\n";
    }
}
echo "Imported approx $count statements.\n";
$tables = ['bank_soal_konversi','soal','log_ujian_kode','ars_result'];
$exportDir = __DIR__ . "/exports_$ts";
if (!is_dir($exportDir)) mkdir($exportDir, 0755, true);
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as cnt FROM `$t`");
        $cnt = $stmt->fetchColumn();
        echo "$t: $cnt rows in restored DB\n";
        // Export structure
        $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_ASSOC);
        $createSql = $create['Create Table'] ?? $create['Create View'] ?? null;
        if ($createSql) {
            $out = "-- Dump for $t\n" . $createSql . ";\n\n";
            // Export data in batches
            $select = $pdo->query("SELECT * FROM `$t`");
            $rows = $select->fetchAll(PDO::FETCH_ASSOC);
            if (count($rows) > 0) {
                $cols = array_map(function($c){ return "`$c`"; }, array_keys($rows[0]));
                $out .= "INSERT INTO `$t` (" . implode(',', $cols) . ") VALUES\n";
                $values = [];
                foreach ($rows as $r) {
                    $vals = array_map(function($v) use ($pdo){
                        if ($v === null) return 'NULL';
                        return $pdo->quote((string)$v);
                    }, array_values($r));
                    $values[] = '(' . implode(',', $vals) . ')';
                }
                $out .= implode(",\n", $values) . ";\n";
            }
            $file = $exportDir . "/$t.sql";
            file_put_contents($file, $out);
            echo "Exported $t to $file\n";
        } else {
            echo "$t: no create statement found\n";
        }
    } catch (Exception $e) {
        echo "$t: ERROR - " . $e->getMessage() . "\n";
    }
}
echo "Exports saved to $exportDir\n";
echo "Done.\n";
