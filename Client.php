<?php
class Client
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    function calculateScore($client)
    {
        $bounceRate = $client['bounce_rate'];
        $deliveredRate = 100 - $bounceRate;
        $openRate = $client['open_rate'];
        $restRate = 100 - $openRate;
        $unsubscriptionRate = $client['unsubscription_rate'];
        $complaintRate = $client['complaint_rate'];

        $openWithoutActionsRate = $openRate - (($openRate / 100 * $unsubscriptionRate / 100) * 100) - (($openRate / 100 * $complaintRate / 100) * 100);
        $restWithoutActionsRate = $restRate - (($restRate / 100 * $unsubscriptionRate / 100) * 100) - (($restRate / 100 * $complaintRate / 100) * 100);

        $openWithoutActionsRate_total = ($openWithoutActionsRate * $deliveredRate) / 100;
        $restWithoutActionsRate_total = ($restWithoutActionsRate * $deliveredRate) / 100;
        $unsubscriptionRate_total = ($unsubscriptionRate * $deliveredRate) / 100;
        $complaintRate_total = ($complaintRate * $deliveredRate) / 100;



        // Définir les poids
        $weight_openWithoutActionsRate = 0.40;
        $weight_restWithoutActionsRate = 0.10;
        $weight_unsubscriptionRate = 0.20;
        $weight_complaintRate = 0.10;
        $weight_bounceRate = 0.20;

        // Calculer le score normalisé
        $score = ($openWithoutActionsRate_total * $weight_openWithoutActionsRate) +
            ($restWithoutActionsRate_total * $weight_restWithoutActionsRate) +
            ((100 - $unsubscriptionRate_total) * $weight_unsubscriptionRate) +
            ((100 - $complaintRate_total) * $weight_complaintRate) +
            ((100 - $bounceRate) * $weight_bounceRate);

        $scoreNormalized = max(min($score, 100), 0);

        return $scoreNormalized;
    }

    public function categorizeGroups()
    {
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
