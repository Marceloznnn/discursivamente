<?php
// App/Models/Course.php
namespace App\Models;

class Course
{
    private ?int $id;
    private string $title;
    private string $shortDescription;
    private ?string $longDescription;
    private ?int $categoryId;
    private int $creatorId;
    private string $status;
    private ?\DateTime $createdAt;
    private ?\DateTime $updatedAt;
    private ?string $coverImage;
    private ?string $courseRequirements;
    private ?string $learningObjectives;
    private string $enrollmentMethod;

    // Novas métricas
    private int $searchHits;
    private int $clickThroughs;

    // Novo atributo para armazenar o número de membros
    private int $memberCount = 0;

    public function __construct(
        string $title,
        string $shortDescription,
        int $creatorId,
        string $status = 'draft',
        string $enrollmentMethod = 'open',
        ?int $categoryId = null,
        ?string $longDescription = null,
        ?string $coverImage = null,
        ?string $courseRequirements = null,
        ?string $learningObjectives = null,
        ?int $id = null,
        ?\DateTime $createdAt = null,
        ?\DateTime $updatedAt = null,
        int $searchHits = 0,
        int $clickThroughs = 0
    ) {
        $this->id                 = $id;
        $this->title              = $title;
        $this->shortDescription   = $shortDescription;
        $this->longDescription    = $longDescription;
        $this->categoryId         = $categoryId;
        $this->creatorId          = $creatorId;
        $this->status             = $status;
        $this->createdAt          = $createdAt;
        $this->updatedAt          = $updatedAt;
        $this->coverImage         = $coverImage;
        $this->courseRequirements = $courseRequirements;
        $this->learningObjectives = $learningObjectives;
        $this->enrollmentMethod   = $enrollmentMethod;
        $this->searchHits         = $searchHits;
        $this->clickThroughs      = $clickThroughs;
    }
    // Getters and Setters

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getMemberCount(): int
    {
        return $this->memberCount;
    }

    public function setMemberCount(int $count): void
    {
        $this->memberCount = $count;
    }

    public function getShortDescription(): string
    {
        return $this->shortDescription;
    }
    public function setShortDescription(string $desc): void
    {
        $this->shortDescription = $desc;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }
    public function setLongDescription(?string $desc): void
    {
        $this->longDescription = $desc;
    }

    public function getCategoryId(): ?int
    {
        return $this->categoryId;
    }
    public function setCategoryId(?int $id): void
    {
        $this->categoryId = $id;
    }

    public function getCreatorId(): int
    {
        return $this->creatorId;
    }
    public function setCreatorId(int $id): void
    {
        $this->creatorId = $id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function getCoverImage(): ?string
    {
        return $this->coverImage;
    }
    public function setCoverImage(?string $image): void
    {
        $this->coverImage = $image;
    }

    public function getCourseRequirements(): ?string
    {
        return $this->courseRequirements;
    }
    public function setCourseRequirements(?string $req): void
    {
        $this->courseRequirements = $req;
    }

    public function getLearningObjectives(): ?string
    {
        return $this->learningObjectives;
    }
    public function setLearningObjectives(?string $obj): void
    {
        $this->learningObjectives = $obj;
    }

    public function getEnrollmentMethod(): string
    {
        return $this->enrollmentMethod;
    }
    public function setEnrollmentMethod(string $method): void
    {
        $this->enrollmentMethod = $method;
    }

    // Métricas
    public function getSearchHits(): int
    {
        return $this->searchHits;
    }
    public function setSearchHits(int $hits): void
    {
        $this->searchHits = $hits;
    }
    public function incrementSearchHits(int $by = 1): void
    {
        $this->searchHits += $by;
    }

    public function getClickThroughs(): int
    {
        return $this->clickThroughs;
    }
    public function setClickThroughs(int $clicks): void
    {
        $this->clickThroughs = $clicks;
    }
    public function incrementClickThroughs(int $by = 1): void
    {
        $this->clickThroughs += $by;
    }

    public function getFormattedPrice(): string
    {
        return 'Gratuito';
    }
}
