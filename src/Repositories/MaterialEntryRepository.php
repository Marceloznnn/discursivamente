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
                (material_id, title, content_url, content_type, public_id, created_at)
             VALUES 
                (:materialId, :title, :contentUrl, :contentType, :publicId, :createdAt)'
        );

        $stmt->execute([
            ':materialId'  => $entry->getMaterialId(),
            ':title'       => $entry->getTitle(),
            ':contentUrl'  => $entry->getContentUrl(),
            ':contentType' => $entry->getContentType(),
            ':publicId'    => $entry->getPublicId(),
            ':createdAt'   => $entry->getCreatedAt()->format('Y-m-d H:i:s')
        ]);

        // seta ID gerado
        $entry->setId((int)$this->pdo->lastInsertId());
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
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            fn(array $row) => $this->hydrate($row),
            $rows
        );
    }

    /**
     * Busca uma entrada específica pelo ID
     */
    public function findById(int $id): ?MaterialEntry
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM material_entries WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->hydrate($row);
    }

    /**
     * Exclui uma entrada de material
     */
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM material_entries WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * Converte um registro do banco em objeto MaterialEntry
     */
    private function hydrate(array $row): MaterialEntry
    {
        $entry = new MaterialEntry(
            (int)$row['material_id'],      
            $row['title'],
            $row['content_url'],
            $row['content_type'],
            $row['public_id']
        );
        $entry->setId((int)$row['id']);
        // created_at já é criado no construtor, mas se quiser manter original, pode adicionar setter
        return $entry;
    }
}