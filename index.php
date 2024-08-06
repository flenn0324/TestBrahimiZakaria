<?php
include 'db_connection.php';
include 'ClientRepository.php';
include 'ClientService.php';

$clientRepository = new ClientRepository($pdo);
$clientService = new ClientService($clientRepository);

$client_id = isset($_GET['client_id']) ? (int)$_GET['client_id'] : null;
$group = isset($_GET['group']) ? (int)$_GET['group'] : null;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'desc';
$best_clients = isset($_GET['best_clients']);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$results_per_page = 30;


if ($best_clients) {
    $clients = $clientService->fetchBestClients();
    $total_clients = count($clients);
    $total_pages = 1; 
} elseif ($client_id) {
    $view = $clientService->determineView($group);
    $client = $clientRepository->getClientById($client_id,$view);
    $clients = $client ? [$client] : [];
    $total_clients = count($clients);
    $total_pages = 1;
} else {
    $filters = [
        'sort' => $sort,
        'page' => $page,
        'group' => $group
    ];
    $clients = $clientService->fetchClients($filters);
    $total_clients = $clientService->getTotalClients($group);
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