<?php
namespace Repositories;

use App\Models\MaterialEntry;
use PDO;

class MaterialEntryRepository 
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Salva uma nova entrada de material no banco de dados
     */
    public function save(MaterialEntry $entry): void
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO material_entries 
                (material_id, title, content_url, content_type, created_at)
             VALUES 
                (:materialId, :title, :contentUrl, :contentType, :createdAt)'
        );

        $stmt->execute([
            ':materialId'  => $entry->getMaterialId(),
            ':title'      => $entry->getTitle(),
            ':contentUrl' => $entry->getContentUrl(),
            ':contentType' => $entry->getContentType(),
            ':createdAt'  => $entry->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * Busca todas as entradas de um material
     */
    public function findByMaterialId(int $materialId): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM material_entries WHERE material_id = :materialId ORDER BY created_at'
        );
        $stmt->execute([':materialId' => $materialId]);
        
        return array_map(
            fn(array $row) => $this->hydrate($row),
            $stmt->fetchAll(PDO::FETCH_ASSOC)
        );
    }

    /**
     * Converte um registro do banco em objeto MaterialEntry
     */
    private function hydrate(array $row): MaterialEntry
    {
        return new MaterialEntry(
            (int)$row['material_id'],
            $row['title'],
            $row['content_url'],
            $row['content_type']
        );
    }
}
