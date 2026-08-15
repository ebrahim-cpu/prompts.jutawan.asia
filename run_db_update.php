<?php

header('Content-Type: text/plain');

$host = '127.0.0.1';
$db   = 'jutawnas_prompts_db';
$user = 'jutawnas_rafi';
$pass = 'rafi2006.';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    echo "1. Connected to MySQL successfully.\n";

    // 1. Add google_id column
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `google_id` VARCHAR(255) NULL AFTER `email`");
        echo "2. Added google_id column.\n";
    } catch (Exception $e) {
        echo "2. google_id column note: " . $e->getMessage() . "\n";
    }

    // 2. Add avatar column
    try {
        $pdo->exec("ALTER TABLE `users` ADD COLUMN `avatar` VARCHAR(255) NULL AFTER `google_id`");
        echo "3. Added avatar column.\n";
    } catch (Exception $e) {
        echo "3. avatar column note: " . $e->getMessage() . "\n";
    }

    // 3. Make password nullable
    try {
        $pdo->exec("ALTER TABLE `users` MODIFY COLUMN `password` VARCHAR(255) NULL");
        echo "4. Modified password column to nullable.\n";
    } catch (Exception $e) {
        echo "4. password column note: " . $e->getMessage() . "\n";
    }

    echo "\nDB Schema Update Complete!\n";

} catch (\PDOException $e) {
    echo "PDO Error: " . $e->getMessage() . "\n";
}
