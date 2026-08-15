<?php
$host = "127.0.0.1";
$db_name = "jutawnas_prompts_db";
$user = "jutawnas_rafi";
$password = "rafi2006.";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $sql = "CREATE TABLE IF NOT EXISTS `visitor_logs` (
      `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `ip_address` VARCHAR(45) NOT NULL,
      `user_agent` TEXT NULL,
      `url` VARCHAR(500) NOT NULL,
      `method` VARCHAR(10) DEFAULT 'GET',
      `referer` VARCHAR(500) NULL,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      INDEX `idx_ip` (`ip_address`),
      INDEX `idx_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);
    echo "SUCCESS: `visitor_logs` table created or verified successfully!";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
