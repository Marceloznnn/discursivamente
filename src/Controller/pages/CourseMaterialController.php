<?php
// src/Controllers/CourseMaterialController.php

namespace Controller\Pages;

use Repositories\CourseMaterialRepository;
use Services\CloudinaryService;
use App\Models\CourseMaterial;
use Twig\Environment;

class CourseMaterialController
{
    private CourseMaterialRepository $repository;
    private CloudinaryService $cloudinary;
    private Environment $twig;

    public function __construct(CourseMaterialRepository $repository, CloudinaryService $cloudinary, Environment $twig)
    {
        $this->repository = $repository;
        $this->cloudinary = $cloudinary;
        $this->twig = $twig;
    }

    /**
     * Exibe a lista de materiais de um curso.
     */
    public function index(int $courseId): void
    {
        $materials = $this->repository->findByCourseId($courseId);
        $role = $_SESSION['user_role'] ?? 'student';

        // Renderiza a view com Twig
        if ($role === 'professor') {
            echo $this->twig->render('curso/material/professor/index.twig', ['materiais' => $materials, 'courseId' => $courseId]);
        } else {
            echo $this->twig->render('curso/material/aluno/index.twig', ['materiais' => $materials, 'courseId' => $courseId]);
        }
    }

    /**
     * Exibe o formulário de criação de material.
     */
    public function create(int $courseId): void
    {
        // Renderiza a view com Twig
        echo $this->twig->render('curso/material/professor/create.twig', ['courseId' => $courseId]);
    }

    /**
     * Persiste um novo material.
     */
    public function store(int $courseId, array $data, array $file): void
    {
        // Upload no Cloudinary
        $upload = $this->cloudinary->upload($file['tmp_name'], "courses/{$courseId}");

        $material = new CourseMaterial(
            $courseId,
            $data['title'],
            $upload['resource_type'],
            $upload['url'],
            $data['description'] ?? '',
            $upload['public_id']
        );

        $this->repository->save($material);
        header("Location: /curso/{$courseId}/materiais");
        exit;
    }

    /**
     * Exibe um material específico.
     */
    public function show(int $id): void
    {
        $material = $this->repository->findById($id);
        if (!$material) {
            http_response_code(404);
            echo "Material não encontrado.";
            return;
        }

        $role = $_SESSION['user_role'] ?? 'student';
        // Renderiza a view com Twig
        if ($role === 'professor') {
            echo $this->twig->render('curso/material/professor/show.twig', ['material' => $material, 'courseId' => $material->getCourseId()]);
        } else {
            echo $this->twig->render('curso/material/aluno/show.twig', ['material' => $material, 'courseId' => $material->getCourseId()]);
        }
    }

    /**
     * Exibe o formulário de edição de material.
     */
    public function edit(int $id): void
    {
        $material = $this->repository->findById($id);
        if (!$material) {
            http_response_code(404);
            echo "Material não encontrado.";
            return;
        }

        // Renderiza a view com Twig
        echo $this->twig->render('curso/material/professor/edit.twig', ['material' => $material, 'courseId' => $material->getCourseId()]);
    }

    /**
     * Atualiza um material existente.
     */
    public function update(int $id, array $data, ?array $file = null): void
    {
        $material = $this->repository->findById($id);
        if (!$material) {
            http_response_code(404);
            echo "Material não encontrado.";
            return;
        }

        // Se foi enviado um novo arquivo, deleta o anterior e faz novo upload
        if ($file && $file['tmp_name']) {
            $this->cloudinary->deleteFile($material->getCloudinaryPublicId());
            $upload = $this->cloudinary->upload($file['tmp_name'], "courses/{$material->getCourseId()}");
            $material->setMediaType($upload['resource_type']);
            $material->setMediaUrl($upload['url']);
            $material->setCloudinaryPublicId($upload['public_id']);
        }

        $material->setTitle($data['title']);
        $material->setDescription($data['description'] ?? '');
        $this->repository->save($material);

        header("Location: /materiais/{$id}");
        exit;
    }

    /**
     * Remove um material.
     */
    public function destroy(int $id): void
    {
        $material = $this->repository->findById($id);
        if ($material) {
            // Remove do Cloudinary
            $this->cloudinary->deleteFile($material->getCloudinaryPublicId());
            // Remove do banco
            $this->repository->delete($id);
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
