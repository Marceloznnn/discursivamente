<?php

namespace Repositories;

use App\Models\User;
use PDO;

class UserRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function findById(int $id): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function save(User $user): void
    {
        if ($user->getId()) {
            $stmt = $this->pdo->prepare('UPDATE users SET name=:name, email=:email, password=:password, type=:type, avatar=:avatar, bio=:bio, updated_at=NOW() WHERE id=:id');
            $stmt->execute([
                ':name' => $user->getName(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword(),
                ':type' => $user->getType(),
                ':avatar' => $user->getAvatar(),
                ':bio' => $user->getBio(),
                ':id' => $user->getId()
            ]);
        } else {
            $stmt = $this->pdo->prepare('INSERT INTO users (name, email, password, type, avatar, bio, created_at, updated_at) VALUES (:name, :email, :password, :type, :avatar, :bio, NOW(), NOW())');
            $stmt->execute([
                ':name' => $user->getName(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword(),
                ':type' => $user->getType(), // será null se não informado
                ':avatar' => $user->getAvatar(),
                ':bio' => $user->getBio()
            ]);
        }
    }

    public function setRecoveryCode(int $userId, string $code, string $expiresAt): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET recovery_code=:code, recovery_code_expires_at=:expiresAt WHERE id=:id');
        $stmt->execute([
            ':code' => $code,
            ':expiresAt' => $expiresAt,
            ':id' => $userId
        ]);
    }

    public function findByRecoveryCode(string $code): ?User
    {
        $stmt = $this->pdo->prepare('SELECT * FROM users WHERE recovery_code = :code AND recovery_code_expires_at > NOW() LIMIT 1');
        $stmt->execute([':code' => $code]);
        $row = $stmt->fetch();
        return $row ? $this->hydrate($row) : null;
    }

    public function updatePassword(int $userId, string $hash): void
    {
        $stmt = $this->pdo->prepare('UPDATE users SET password=:password, recovery_code=NULL, recovery_code_expires_at=NULL WHERE id=:id');
        $stmt->execute([
            ':password' => $hash,
            ':id' => $userId
        ]);
    }
    public function findAll(): array
{
    $stmt = $this->pdo->query('SELECT * FROM users ORDER BY name');
    $rows = $stmt->fetchAll();
    return array_map(fn($row) => $this->hydrate($row), $rows);
}


private function hydrate(array $row): User
{
    return new User(
        // __construct(
        //   string $name,
        //   string $email,
        //   string $password,
        //   ?string $type = null,
        //   ?int $id = null,
        //   ?string $recoveryCode = null,
        //   ?string $recoveryCodeExpiration = null,
        //   ?string $avatar = null,
        //   ?string $createdAt = null,
        //   ?string $updatedAt = null,
        //   ?string $bio = null,
        //   ?string $recoveryCodeExpiresAt = null
        // )
        $row['name'],
        $row['email'],
        $row['password'],
        $row['type'],                         // 4) type
        (int)$row['id'],                      // 5) id
        $row['recovery_code'],                // 6) recoveryCode
        $row['recovery_code_expiration'],     // 7) recoveryCodeExpiration
        $row['avatar'],                       // 8) avatar
        $row['created_at'],                   // 9) createdAt
        $row['updated_at'],                   // 10) updatedAt
        $row['bio'],                          // 11) bio
        $row['recovery_code_expires_at']      // 12) recoveryCodeExpiresAt
    );
}

}