<?php
$f = __DIR__ . '/../db-backups/pseudolearn2026.sql';
if (!file_exists($f)) { echo ""; exit(0); }
$s = file_get_contents($f);
if (preg_match('/USE\s+`([^`]+)`/i', $s, $m)) {
    echo $m[1];
    exit(0);
}
if (preg_match('/USE\s+([^;\s]+)\s*;/i', $s, $m)) {
    echo trim($m[1], "`\"'");
    exit(0);
}
if (preg_match('/CREATE\s+DATABASE\s+IF\s+NOT\s+EXISTS\s+`([^`]+)`/i', $s, $m)) {
    echo $m[1];
    exit(0);
}
if (preg_match('/CREATE\s+DATABASE\s+`([^`]+)`/i', $s, $m)) {
    echo $m[1];
    exit(0);
}
exit(0);
