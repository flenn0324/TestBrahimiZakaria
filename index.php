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
    $client = $clientRepository->getClientById($client_id, $view);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/public/styles.css">
    <title>Brevo Clients</title>
</head>

<body>
    <nav class="navbar bg-success">
        <div class="container-fluid">
            <span class="navbar-brand mb-0 h1 ps-3 text-white">Brevo Test Case</span>
        </div>
    </nav>

    <div class="container my-5 content">
        <h1 class="mb-4">Clients List</h1>
        <form method="GET" action="index.php" class="row gy-2 gx-3 align-items-center mb-4">
            <div>
                <p>You can search by filter or combine multiple filters:</p>
                <div class="col-auto">
                    <label for="client_id" class="form-label">Search by Client ID:</label>
                    <div class="input-group">
                        <span class="input-group-text" id="basic-addon1">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" id="client_id" name="client_id" class="form-control" aria-describedby="basic-addon1">
                    </div>
                </div>
                <div class="col-auto">
                    <label for="group" class="form-label">Select Group:</label>
                    <select id="group" name="group" class="form-select">
                        <option value="">All</option>
                        <option value="1" <?php echo ($group == 1) ? 'selected' : ''; ?>>Group 1</option>
                        <option value="2" <?php echo ($group == 2) ? 'selected' : ''; ?>>Group 2</option>
                        <option value="3" <?php echo ($group == 3) ? 'selected' : ''; ?>>Group 3</option>
                    </select>
                </div>
                <div class="col-auto">
                    <label for="sort" class="form-label">Sort by:</label>
                    <select id="sort" name="sort" class="form-select">
                        <option value="desc" <?php echo ($sort == 'desc') ? 'selected' : ''; ?>>Best Score</option>
                        <option value="asc" <?php echo ($sort == 'asc') ? 'selected' : ''; ?>>Worst Score</option>
                    </select>
                </div>
                <div class="col-auto my-3 text-end">
                    <button type="submit" class="btn btn-success">Search</button>
                </div>
            </div>
            <div class="col-auto">
                <p>You can directly access the ideal profile for each group here: <button type="submit" name="best_clients" class="btn btn-sm btn-outline-success">Ideal profiles</button></p>
            </div>
        </form>
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
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
        <?php if ($total_clients == 0) : ?>
            <div class="alert alert-danger m-3" role="alert">
                Client ID <?php echo htmlspecialchars($client_id); ?> not found in Group <?php echo htmlspecialchars($group); ?>
            </div>
        <?php endif; ?>
        <nav>
            <ul class="pagination">
                <?php if ($page > 1) : ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?php echo $page - 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&group=<?php echo htmlspecialchars($group); ?>&client_id=<?php echo htmlspecialchars($client_id); ?>">Previous</a>
                    </li>
                <?php endif; ?>
                <?php if ($page < $total_pages) : ?>
                    <li class="page-item">
                        <a class="page-link" href="index.php?page=<?php echo $page + 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&group=<?php echo htmlspecialchars($group); ?>&client_id=<?php echo htmlspecialchars($client_id); ?>">Next</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

</html>