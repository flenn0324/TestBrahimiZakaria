<?php

class ClientRepository {
    private $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    

    /**
     * Get client by ID.
     *
     * @param int $clientId
     * @return array|null
     */
    public function getClientById(int $clientId, string $view): ?array {
        $stmt = $this->pdo->prepare("SELECT * FROM $view WHERE client_id = :client_id");
        $stmt->bindValue(':client_id', $clientId, PDO::PARAM_INT);
        $stmt->execute();
        $client = $stmt->fetch(PDO::FETCH_ASSOC);
        return $client ?: null;
    }

    /**
     * Get all clients from a specific view with sorting.
     *
     * @param string $view
     * @param string $order 
     * @param int $start 
     * @param int $limit 
     * @return array
     */
    public function getAllClients(string $view, string $order = 'DESC', int $start = 0, int $limit = 30): array {
        $stmt = $this->pdo->prepare("SELECT * FROM $view ORDER BY score $order LIMIT :start, :limit");
        $stmt->bindValue(':start', $start, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * get best client in each group.
     *
     * @return array
     */
    public function getBestClients(): array {
        $stmt = $this->pdo->prepare("
            SELECT * FROM clients c
            INNER JOIN (
                SELECT groupe, MAX(score) as max_score
                FROM clients
                GROUP BY groupe
            ) m
            ON c.groupe = m.groupe AND c.score = m.max_score
            ORDER BY c.groupe
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get num of clients.
     *
     * @return int
     */
    public function getTotalClients(string $view): int {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM $view");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}