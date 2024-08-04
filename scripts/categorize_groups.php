<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Client.php';

$clientHandler = new Client($pdo);
$clientHandler->categorizeGroups();

echo "Clients categorized successfully.";
