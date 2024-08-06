<?php

class ClientService {
    private $clientRepository;

    public function __construct(ClientRepository $clientRepository) {
        $this->clientRepository = $clientRepository;
    }

    public function fetchClients(array $filters): array {
        $order = $filters['sort'] ?? 'desc';
        $page = $filters['page'] ?? 1;
        $start = ($page - 1) * 30;
        $group = $filters['group'] ?? null;

        // Déterminer la vue en fonction du groupe
        $view = $this->determineView($group);

        return $this->clientRepository->getAllClients($view, $order, $start, 30);
    }

    public function determineView(?int $group): string {
        switch ($group) {
            case 1:
                return "group1_view";
            case 2:
                return "group2_view";
            case 3:
                return "group3_view";
            default:
                return "clients"; // Vue par défaut
        }
    }

    public function fetchBestClients(): array {
        return $this->clientRepository->getBestClients();
    }

    public function getTotalClients(?int $group = null): int {
        $view = $this->determineView($group);
        return $this->clientRepository->getTotalClients($view);
    }
}