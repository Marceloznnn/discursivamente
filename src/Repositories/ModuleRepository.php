<?php
namespace Repositories;

use App\Models\Module;
use PDO;

class ModuleRepository
{
    private PDO $pdo;
    
    public function __construct(PDO $pdo) 
    { 
        $this->pdo = $pdo; 
    }

    public function findById(int $id): ?Module
    {
        $stmt = $this->pdo->prepare('SELECT * FROM modules WHERE id = :id');
        $stmt->execute([':id' => $id]);
        
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$data) {
            return null;
        }
        
        return $this->hydrate($data);
    }
    
    public function findByCourse(int $courseId): array
    {
        $stmt = $this->pdo->prepare('
            SELECT * FROM modules 
            WHERE course_id = :courseId 
            ORDER BY position ASC
        ');
        
        $stmt->execute([':courseId' => $courseId]);
        $modules = [];
        
        while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $modules[] = $this->hydrate($data);
        }
        
        return $modules;
    }

    /**
     * Retorna a próxima posição disponível para um novo módulo no curso.
     */
    public function getNextPosition(int $courseId): int
    {
        $stmt = $this->pdo->prepare('
            SELECT MAX(position) AS max_position 
            FROM modules 
            WHERE course_id = :courseId
        ');
        $stmt->execute([':courseId' => $courseId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return ((int)$result['max_position']) + 1;
    }
    
    public function save(Module $module): void
    {
        if ($module->getId() === null) {
            $this->insert($module);
        } else {
            $this->update($module);
        }
    }
    
    private function insert(Module $module): void
    {
        $stmt = $this->pdo->prepare('
            INSERT INTO modules (course_id, title, description, position)
            VALUES (:courseId, :title, :description, :position)
        ');
        
        $stmt->execute([
            ':courseId' => $module->getCourseId(),
            ':title' => $module->getTitle(),
            ':description' => $module->getDescription(),
            ':position' => $module->getPosition()
        ]);
        
        $module->setId($this->pdo->lastInsertId());
    }
    
    private function update(Module $module): void
    {
        $stmt = $this->pdo->prepare('
            UPDATE modules 
            SET title = :title, 
                description = :description, 
                position = :position,
                updated_at = CURRENT_TIMESTAMP
            WHERE id = :id
        ');
        
        $stmt->execute([
            ':title' => $module->getTitle(),
            ':description' => $module->getDescription(),
            ':position' => $module->getPosition(),
            ':id' => $module->getId()
        ]);
    }
    
    public function delete(int $id): void
    {
        $stmt = $this->pdo->prepare('DELETE FROM modules WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }
    
    private function hydrate(array $data): Module
    {
        return new Module(
            $data['course_id'],
            $data['title'],
            $data['description'],
            $data['position'],
            $data['id'],
            $data['created_at'],
            $data['updated_at']
        );
    }
    
    public function reorderModules(int $courseId, array $moduleOrder): void
    {
        $this->pdo->beginTransaction();
        
        try {
            $stmt = $this->pdo->prepare('
                UPDATE modules
                SET position = :position
                WHERE id = :id AND course_id = :courseId
            ');
            
            foreach ($moduleOrder as $position => $moduleId) {
                $stmt->execute([
                    ':position' => $position + 1,
                    ':id' => $moduleId,
                    ':courseId' => $courseId
                ]);
            }
            
            $this->pdo->commit();
        } catch (\Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }
}
