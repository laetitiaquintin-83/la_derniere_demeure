<?php

class GardenModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addMessage(array $data, ?string $photoPath): void
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO livre_dor_animaux (nom_proprietaire, nom_animal, message, photo_path)
             VALUES (?, ?, ?, ?)"
        );
        $statement->execute([
            $data['nom_proprietaire'],
            $data['nom_animal'],
            $data['message'],
            $photoPath,
        ]);
    }
}