<?php
include 'db_connection.php';

$client_id = isset($_GET['client_id']) ? $_GET['client_id'] : null;
$group = isset($_GET['group']) ? (int)$_GET['group'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'desc'; // Par défaut, tri décroissant (meilleur score d'abord)
$order = ($sort === 'asc') ? 'ASC' : 'DESC';
$best_clients = isset($_GET['best_clients']);

// Pagination
$results_per_page = 30;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Choix de la vue en fonction du groupe
$view = null;
switch ($group) {
    case 1:
        $view = "group1_view";
        break;
    case 2:
        $view = "group2_view";
        break;
    case 3:
        $view = "group3_view";
        break;
    default:
        $view = "clients"; // Si aucun groupe n'est sélectionné, on utilise la table principale
        break;
}

if ($best_clients) {
    // Recherche des meilleurs clients pour chaque groupe
    $best_clients_query = "
        SELECT * FROM clients c
        INNER JOIN (
            SELECT groupe, MAX(score) as max_score
            FROM clients
            GROUP BY groupe
        ) m
        ON c.groupe = m.groupe AND c.score = m.max_score
        ORDER BY c.groupe
    ";
    $stmt = $pdo->prepare($best_clients_query);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_clients = count($clients);
    $total_pages = 1; 
} elseif ($client_id) {
    // Recherche par ID de client
    $stmt = $pdo->prepare("SELECT * FROM $view WHERE client_id = :client_id ORDER BY score $order LIMIT :start_from, :results_per_page");
    $stmt->bindValue(':client_id', $client_id, PDO::PARAM_INT);
    $stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
    $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total_clients = count($clients);
    $total_pages = 1;
} else {
    // Filtrage par groupe et tri par score
    $stmt = $pdo->prepare("SELECT * FROM $view ORDER BY score $order LIMIT :start_from, :results_per_page");
    $stmt->bindValue(':start_from', $start_from, PDO::PARAM_INT);
    $stmt->bindValue(':results_per_page', $results_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtenir le nombre total de clients pour la pagination
    $total_stmt = $pdo->prepare("SELECT COUNT(*) FROM $view");
    $total_stmt->execute();
    $total_clients = $total_stmt->fetchColumn();
    $total_pages = ceil($total_clients / $results_per_page);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clients</title>
</head>

<body>
    <h1>Clients List</h1>
    <form method="GET" action="index.php">
        <label for="client_id">Search by Client ID:</label>
        <input type="text" id="client_id" name="client_id">
        <label for="group">Select Group:</label>
        <select id="group" name="group">
            <option value="">All</option>
            <option value="1" <?php echo ($group == 1) ? 'selected' : ''; ?>>Group 1</option>
            <option value="2" <?php echo ($group == 2) ? 'selected' : ''; ?>>Group 2</option>
            <option value="3" <?php echo ($group == 3) ? 'selected' : ''; ?>>Group 3</option>
        </select>
        <label for="sort">Sort by:</label>
        <select id="sort" name="sort">
            <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Best Score</option>
            <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Worst Score</option>
        </select>
        <input type="submit" value="Search">
        <input type="submit" name="best_clients" value="Ideal profiles">
    </form>
    <table border="1">
        <thead>
            <tr>
                <th>ID</th>
                <th>Emails Sent</th>
                <th>Average Open Rate</th>
                <th>Average Unsubscription Rate</th>
                <th>Average Bounce Rate</th>
                <th>Average Complaint Rate</th>
                <th>Score</th>
                <th>Group</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clients as $client) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($client['client_id']); ?></td>
                    <td><?php echo htmlspecialchars($client['total_sent']); ?></td>
                    <td><?php echo htmlspecialchars($client['open_rate']); ?></td>
                    <td><?php echo htmlspecialchars($client['unsubscription_rate']); ?></td>
                    <td><?php echo htmlspecialchars($client['bounce_rate']); ?></td>
                    <td><?php echo htmlspecialchars($client['complaint_rate']); ?></td>
                    <td><?php echo htmlspecialchars($client['score']); ?></td>
                    <td><?php echo htmlspecialchars($client['groupe']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    if ($total_clients == 0) {
        echo "Client ID $client_id not found in Group $group";
    }
    ?>

    <!-- Pagination Links -->
    <div>
        <?php if ($page > 1) : ?>
            <a href="index.php?page=<?php echo $page - 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&group=<?php echo htmlspecialchars($group); ?>&client_id=<?php echo htmlspecialchars($client_id); ?>">Previous</a>
        <?php endif; ?>

        <?php if ($page < $total_pages) : ?>
            <a href="index.php?page=<?php echo $page + 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&group=<?php echo htmlspecialchars($group); ?>&client_id=<?php echo htmlspecialchars($client_id); ?>">Next</a>
        <?php endif; ?>
    </div>
</body>

</html>