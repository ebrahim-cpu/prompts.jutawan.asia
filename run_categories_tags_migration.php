<?php
$host = "127.0.0.1";
$db_name = "jutawnas_prompts_db";
$user = "jutawnas_rafi";
$password = "rafi2006.";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // Create Categories Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `categories` (
      `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(255) NOT NULL UNIQUE,
      `icon` VARCHAR(50) DEFAULT '🎨',
      `color` VARCHAR(50) DEFAULT 'purple',
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Create Tags Table
    $pdo->exec("CREATE TABLE IF NOT EXISTS `tags` (
      `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(255) NOT NULL,
      `slug` VARCHAR(255) NOT NULL UNIQUE,
      `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    // Seed default categories if empty
    $catCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($catCount == 0) {
        $defaultCats = [
            ['Umum', 'general', '🎨', 'gray'],
            ['Potret', 'portrait', '🧑', 'pink'],
            ['Landskap', 'landscape', '🏔️', 'green'],
            ['Anime', 'anime', '⛩️', 'purple'],
            ['Realistik', 'realistic', '📷', 'blue'],
            ['Abstrak', 'abstract', '🌀', 'indigo'],
            ['Fantasi', 'fantasy', '🐉', 'yellow'],
            ['Sci-Fi', 'scifi', '🚀', 'cyan'],
            ['Arkitektur', 'architecture', '🏛️', 'amber'],
            ['Makanan', 'food', '🍜', 'orange'],
            ['Alam Semula Jadi', 'nature', '🌿', 'emerald'],
            ['Logo & Ikon', 'logo', '✏️', 'rose'],
        ];
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, color) VALUES (?, ?, ?, ?)");
        foreach ($defaultCats as $c) {
            $stmt->execute($c);
        }
    }

    // Seed default tags from prompts table if empty
    $tagCount = $pdo->query("SELECT COUNT(*) FROM tags")->fetchColumn();
    if ($tagCount == 0) {
        $stmtPrompt = $pdo->query("SELECT tags FROM prompts WHERE tags IS NOT NULL AND tags != ''");
        $allTags = [];
        while ($row = $stmtPrompt->fetch(PDO::FETCH_ASSOC)) {
            $parts = explode(',', $row['tags']);
            foreach ($parts as $t) {
                $t = trim($t);
                if ($t && !in_array($t, $allTags)) {
                    $allTags[] = $t;
                }
            }
        }
        
        // Add default tags if none in prompts
        $defaultTags = ['cyberpunk', 'neon', 'futuristic', 'dark', 'portrait', 'realistic', 'fantasy', 'cinematic', 'anime', '8k'];
        foreach ($defaultTags as $dt) {
            if (!in_array($dt, $allTags)) {
                $allTags[] = $dt;
            }
        }

        $stmtTag = $pdo->prepare("INSERT IGNORE INTO tags (name, slug) VALUES (?, ?)");
        foreach ($allTags as $tName) {
            $slug = preg_replace('/[^a-z0-9]+/i', '-', strtolower($tName));
            $stmtTag->execute([$tName, $slug]);
        }
    }

    echo "SUCCESS: `categories` and `tags` tables created and seeded successfully!";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
