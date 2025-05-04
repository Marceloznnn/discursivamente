<?php

namespace App\Models;

class AuditLog
{
    private ?int $id;
    private int $adminId;
    private string $action;
    private string $resource;
    private ?int $resourceId;
    private ?string $meta;      // JSON string com dados extras
    private ?string $createdAt;

    public function __construct(
        int $adminId,
        string $action,
        string $resource,
        ?int $resourceId = null,
        ?string $meta = null,
        ?int $id = null,
        ?string $createdAt = null
    ) {
        $this->id         = $id;
        $this->adminId    = $adminId;
        $this->action     = $action;
        $this->resource   = $resource;
        $this->resourceId = $resourceId;
        $this->meta       = $meta;
        $this->createdAt  = $createdAt;
    }

    // Getters
    public function getId(): ?int        { return $this->id; }
    public function getAdminId(): int    { return $this->adminId; }
    public function getAction(): string  { return $this->action; }
    public function getResource(): string { return $this->resource; }
    public function getResourceId(): ?int { return $this->resourceId; }
    public function getMeta(): ?string    { return $this->meta; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // toArray para persistência ou debugging
    public function toArray(): array
    {
        return [
            'id'           => $this->id,
            'admin_id'     => $this->adminId,
            'action'       => $this->action,
            'resource'     => $this->resource,
            'resource_id'  => $this->resourceId,
            'meta'         => $this->meta,
            'created_at'   => $this->createdAt,
        ];
    }
}
