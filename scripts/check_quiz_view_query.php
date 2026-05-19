<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=new_pseudolearn;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$levelId = '019863c4-59f9-7319-9104-08267fc3c551';
$sql = "SELECT COUNT(*) AS aggregate FROM v_bank_soal_konversi WHERE id_level = :level AND status IN (1, '1', 'active') AND deleted_at IS NULL";
$stmt = $pdo->prepare($sql);
$stmt->execute(['level' => $levelId]);
print_r($stmt->fetch(PDO::FETCH_ASSOC));
