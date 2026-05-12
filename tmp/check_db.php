<?php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
require_once __DIR__ . '/src/Core/Database.php';

use App\Core\Database;

try {
    $res = Database::query("SHOW COLUMNS FROM compras")->fetchAll();
    echo "COLUMNS IN compras:\n";
    foreach ($res as $row) {
        echo "- " . $row['Field'] . "\n";
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
