<?php

namespace App\Models;

class User
{
    private ?int $id;
    private string $name;
    private string $email;
    private string $password;
    private ?string $type; // agora pode ser null
    private ?string $recoveryCode;
    private ?string $recoveryCodeExpiration;
    private ?string $avatar;
    private ?string $bio;
    private ?string $createdAt;
    private ?string $updatedAt;
    private ?string $recoveryCodeExpiresAt;

    public function __construct(
        string $name,
        string $email,
        string $password,
        ?string $type = null, // padrão null
        ?int $id = null,
        ?string $recoveryCode = null,
        ?string $recoveryCodeExpiration = null,
        ?string $avatar = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        ?string $bio = null,
        ?string $recoveryCodeExpiresAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->type = $type;
        $this->recoveryCode = $recoveryCode;
        $this->recoveryCodeExpiration = $recoveryCodeExpiration;
        $this->avatar = $avatar;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->bio = $bio;
        $this->recoveryCodeExpiresAt = $recoveryCodeExpiresAt;
    }

    // Getters e setters
    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getPassword(): string { return $this->password; }
    public function getType(): ?string { return $this->type; }
    public function getRecoveryCode(): ?string { return $this->recoveryCode; }
    public function getRecoveryCodeExpiration(): ?string { return $this->recoveryCodeExpiration; }
    public function getAvatar(): ?string { return $this->avatar; }
    public function getCreatedAt(): ?string { return $this->createdAt; }
    public function getUpdatedAt(): ?string { return $this->updatedAt; }
    public function getBio(): ?string { return $this->bio; }
    public function getRecoveryCodeExpiresAt(): ?string { return $this->recoveryCodeExpiresAt; }

    public function setPassword(string $hash): void { $this->password = $hash; }
    public function setRecoveryCode(?string $code): void { $this->recoveryCode = $code; }
    public function setRecoveryCodeExpiration(?string $exp): void { $this->recoveryCodeExpiration = $exp; }
    public function setAvatar(?string $avatar): void { $this->avatar = $avatar; }
    public function setBio(?string $bio): void { $this->bio = $bio; }
    public function setUpdatedAt(?string $updatedAt): void { $this->updatedAt = $updatedAt; }
    public function setRecoveryCodeExpiresAt(?string $exp): void { $this->recoveryCodeExpiresAt = $exp; }

    public function setName(string $name): void
{
    $this->name = $name;
}

public function setEmail(string $email): void
{
    $this->email = $email;
}

public function setType(?string $type): void
{
    $this->type = $type;
}


    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'type' => $this->type,
            'recovery_code' => $this->recoveryCode,
            'recovery_code_expiration' => $this->recoveryCodeExpiration,
            'avatar' => $this->avatar,
            'created_at' => $this->createdAt,
            'updated_at' => $this->updatedAt,
            'bio' => $this->bio,
            'recovery_code_expires_at' => $this->recoveryCodeExpiresAt,
        ];
    }
}