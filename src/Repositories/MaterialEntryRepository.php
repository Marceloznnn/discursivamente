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
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [save] materialId={$entry->getMaterialId()} title={$entry->getTitle()}\n", FILE_APPEND);
        $stmt = $this->pdo->prepare(
            'INSERT INTO material_entries 
                (material_id, title, content_url, content_type, public_id, subtitle_url, created_at)
             VALUES 
                (:materialId, :title, :contentUrl, :contentType, :publicId, :subtitleUrl, :createdAt)'
        );
        $stmt->execute([
            ':materialId'  => $entry->getMaterialId(),
            ':title'       => $entry->getTitle(),
            ':contentUrl'  => $entry->getContentUrl(),
            ':contentType' => $entry->getContentType(),
            ':publicId'    => $entry->getPublicId(),
            ':subtitleUrl' => $entry->getSubtitleUrl(),
            ':createdAt'   => $entry->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [save] INSERT executado\n", FILE_APPEND);
        $entry->setId((int) $this->pdo->lastInsertId());
    }

    /**
     * Atualiza apenas o subtitle_url de uma entrada existente
     */
    public function updateSubtitle(int $entryId, string $subtitleUrl): void
    {
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [updateSubtitle] entryId={$entryId} subtitleUrl={$subtitleUrl}\n", FILE_APPEND);
        $stmt = $this->pdo->prepare(
            'UPDATE material_entries 
             SET subtitle_url = :subtitleUrl 
             WHERE id = :id'
        );
        $stmt->execute([
            ':subtitleUrl' => $subtitleUrl,
            ':id'          => $entryId,
        ]);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [updateSubtitle] UPDATE executado\n", FILE_APPEND);
    }

    /**
     * Busca todas as entradas de um material
     * Inclui subtitle_url
     */
    public function findByMaterialId(int $materialId): array
    {
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [findByMaterialId] materialId={$materialId}\n", FILE_APPEND);
        $stmt = $this->pdo->prepare(
            'SELECT * FROM material_entries WHERE material_id = :materialId ORDER BY created_at'
        );
        $stmt->execute([':materialId' => $materialId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [findByMaterialId] encontrados=" . count($rows) . "\n", FILE_APPEND);
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
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [findById] id={$id}\n", FILE_APPEND);
        $stmt = $this->pdo->prepare(
            'SELECT * FROM material_entries WHERE id = :id'
        );
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [findById] encontrado=" . ($row ? 'sim' : 'nao') . "\n", FILE_APPEND);
        if (! $row) {
            return null;
        }
        return $this->hydrate($row);
    }

    /**
     * Conta o número total de entradas de um material
     */
    public function countByMaterialId(int $materialId): int
    {
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [countByMaterialId] materialId={$materialId}\n", FILE_APPEND);
        $stmt = $this->pdo->prepare(
            'SELECT COUNT(*) FROM material_entries WHERE material_id = :materialId'
        );
        $stmt->execute([':materialId' => $materialId]);
        $count = (int) $stmt->fetchColumn();
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [countByMaterialId] count={$count}\n", FILE_APPEND);
        return $count;
    }

    /**
     * Exclui uma entrada de material
     */
    public function delete(int $id): void
    {
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [delete] id={$id}\n", FILE_APPEND);
        $stmt = $this->pdo->prepare('DELETE FROM material_entries WHERE id = :id');
        $stmt->execute([':id' => $id]);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [delete] DELETE executado\n", FILE_APPEND);
    }

    /**
     * Converte um registro do banco em objeto MaterialEntry
     */
    private function hydrate(array $row): MaterialEntry
    {
        $logFile = __DIR__ . '/../logs/material_entry.log';
        file_put_contents($logFile, date('Y-m-d H:i:s') . " [hydrate] id=" . (isset($row['id']) ? $row['id'] : 'null') . "\n", FILE_APPEND);
        $entry = new MaterialEntry(
            (int) $row['material_id'],      
            $row['title'],
            $row['content_url'],
            $row['content_type'],
            $row['public_id'],
            $row['subtitle_url'] ?? null
        );
        $entry->setId((int) $row['id']);
        return $entry;
    }
}