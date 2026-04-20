<?php

class RecommandationModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function ensureTable(): void
    {
        $this->pdo->exec(
            "CREATE TABLE IF NOT EXISTS recommandations_confiance (
                id INT PRIMARY KEY AUTO_INCREMENT,
                nom VARCHAR(120) NOT NULL,
                email VARCHAR(255) NOT NULL,
                service VARCHAR(120) NOT NULL,
                message TEXT NOT NULL,
                consentement TINYINT(1) NOT NULL DEFAULT 0,
                approuve TINYINT(1) NOT NULL DEFAULT 1,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_approuve_created (approuve, created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );
    }

    public function addRecommandation(array $data): void
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO recommandations_confiance (nom, email, service, message, consentement, approuve)
             VALUES (?, ?, ?, ?, ?, 0)"
        );

        $statement->execute([
            $data['nom'],
            $data['email'],
            $data['service'],
            $data['message'],
            $data['consentement'],
        ]);
    }

    public function getApprovedRecommandations(int $limit = 9): array
    {
        $statement = $this->pdo->prepare(
            "SELECT nom, service, message, created_at
             FROM recommandations_confiance
             WHERE approuve = 1
             ORDER BY created_at DESC
             LIMIT ?"
        );
        $statement->bindValue(1, $limit, PDO::PARAM_INT);
        $statement->execute();

        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}
