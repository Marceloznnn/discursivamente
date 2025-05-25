<?php
// src/Models/MaterialEntry.php
namespace App\Models;

class MaterialEntry
{
    private ?int $id;
    private int $materialId;
    private string $title;
    private string $contentUrl;
    private string $contentType;
    private string $publicId;
    private ?string $subtitleUrl;      // nova propriedade
    private \DateTime $createdAt;

    public function __construct(
        int $materialId,
        string $title,
        string $contentUrl,
        string $contentType,
        string $publicId,
        ?string $subtitleUrl = null   // parâmetro opcional
    ) {
        $this->id           = null;
        $this->materialId   = $materialId;
        $this->title        = $title;
        $this->contentUrl   = $contentUrl;
        $this->contentType  = $contentType;
        $this->publicId     = $publicId;
        $this->subtitleUrl  = $subtitleUrl;
        $this->createdAt    = new \DateTime();
    }

    // getters...
    public function getId(): ?int           { return $this->id; }
    public function getMaterialId(): int    { return $this->materialId; }
    public function getTitle(): string      { return $this->title; }
    public function getContentUrl(): string { return $this->contentUrl; }
    public function getContentType(): string{ return $this->contentType; }
    public function getPublicId(): string   { return $this->publicId; }
    public function getSubtitleUrl(): ?string{ return $this->subtitleUrl; }  // getter
    public function getCreatedAt(): \DateTime{ return $this->createdAt; }

    // setters...
    public function setId(int $id): void               { $this->id = $id; }
    public function setSubtitleUrl(string $url): void   { $this->subtitleUrl = $url; }  // setter

    /**
     * Retorna o tipo de conteúdo real do material.
     * Se o tipo for 'raw', tenta detectar via MIME remoto.
     */
    public function getContentTypeRobusto(): string
    {
        // Se já for um tipo conhecido, retorna direto
        if (in_array($this->contentType, ['image', 'video', 'pdf'])) {
            return $this->contentType;
        }
        // Se for raw, tenta detectar via URL remota
        $url = $this->contentUrl;
        // Só tenta se for raw e URL remota
        if ($this->contentType === 'raw' && $url) {
            // Tenta obter o header Content-Type
            $headers = @get_headers($url, 1);
            if ($headers && isset($headers['Content-Type'])) {
                $mime = is_array($headers['Content-Type']) ? $headers['Content-Type'][0] : $headers['Content-Type'];
                if (stripos($mime, 'application/pdf') !== false) {
                    return 'pdf';
                }
            }
        }
        return $this->contentType;
    }
}
