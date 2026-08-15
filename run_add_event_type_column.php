<?php
$host = "127.0.0.1";
$db_name = "jutawnas_prompts_db";
$user = "jutawnas_rafi";
$password = "rafi2006.";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Check if event_type column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM user_access_logs LIKE 'event_type'");
    if ($stmt->rowCount() == 0) {
        $pdo->exec("ALTER TABLE user_access_logs ADD COLUMN `event_type` VARCHAR(50) DEFAULT 'LOGIN' AFTER `user_email`");
        echo "SUCCESS: `event_type` column added to `user_access_logs` table!";
    } else {
        echo "INFO: `event_type` column already exists!";
    }

    // Truncate old URL-tracking records so table starts fresh with only Login & Logout records
    $pdo->exec("TRUNCATE TABLE user_access_logs");
    echo " Table truncated for clean Login/Logout logging.";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
