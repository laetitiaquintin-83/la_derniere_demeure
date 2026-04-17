<?php

class AuthModel
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAdminUser(string $username): ?array
    {
        $statement = $this->pdo->prepare("SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1");
        $statement->execute([$username]);
        $user = $statement->fetch(PDO::FETCH_ASSOC);

        return $user ?: null;
    }
}