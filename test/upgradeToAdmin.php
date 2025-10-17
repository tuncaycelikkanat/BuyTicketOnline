<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
try {
    $stmt = $db->prepare("UPDATE Users SET role = 'admin' WHERE email = 'admin@admin'");
    $stmt->execute();
} catch (PDOException $e) {
    echo $e->getMessage();
}
