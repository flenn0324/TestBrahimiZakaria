<?php
class Client {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function calculateScore($client) {
        $openRate = $client['open_rate'];
        $unsubRate = $client['unsubscription_rate'];
        $bounceRate = $client['bounce_rate'];
        $complaintRate = $client['complaint_rate'];
        
        // Formule pour calculer le score
        $score = 10;
        return max($score, 0);
    }

    public function categorizeGroups() {
        // Récupérer tous les clients
        $stmt = $this->pdo->query("SELECT * FROM clients ORDER BY score DESC");
        $clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Calculer les seuils pour les groupes
        $totalEmails = array_sum(array_column($clients, 'total_sent'));
        $groupSize = $totalEmails / 3;
        $cumulativeEmails = 0;

        foreach ($clients as &$client) {
            $cumulativeEmails += $client['total_sent'];

            if ($cumulativeEmails <= $groupSize) {
                $groupe = 1;
            } elseif ($cumulativeEmails <= 2 * $groupSize) {
                $groupe = 2;
            } else {
                $groupe = 3;
            }

            // Mettre à jour la base de données
            $stmt = $this->pdo->prepare("UPDATE clients SET groupe = ? WHERE client_id = ?");
            $stmt->execute([$groupe, $client['client_id']]);
        }
    }
}




