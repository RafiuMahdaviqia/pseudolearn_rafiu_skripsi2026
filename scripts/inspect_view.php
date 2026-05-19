<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=new_pseudolearn;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$row = $pdo->query('SHOW CREATE VIEW v_bank_soal_konversi')->fetch(PDO::FETCH_ASSOC);
print_r($row);
