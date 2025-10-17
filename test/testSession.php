<?php
define('ROOT_PATH', dirname(__DIR__));
require_once ROOT_PATH . '/includes/config.php';
echo "Session aktif, değer: " . $_SESSION['user']['role'];
