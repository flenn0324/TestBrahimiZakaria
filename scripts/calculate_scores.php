<?php
require_once __DIR__ . '/../db_connection.php';
require_once __DIR__ . '/../Client.php';

$clientHandler = new Client($pdo);

// Recalculer les scores pour tous les clients
$stmt = $pdo->query("SELECT * FROM clients");
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($clients as $client) {
    $score = $clientHandler->calculateScore($client);
    $stmt = $pdo->prepare("UPDATE clients SET score = ? WHERE client_id = ?");
    $stmt->execute([$score, $client['client_id']]);
}

echo "Scores recalculated successfully.";
