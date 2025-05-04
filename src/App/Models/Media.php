<?php

namespace App\Models;

class Media
{
    private $id;
    private $userId;
    private $title;
    private $description;
    private $url;
    private $publicId;
    private $type;

    public function __construct($userId, $title, $description, $url, $publicId, $type, $id = null)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->title = $title;
        $this->description = $description;
        $this->url = $url;
        $this->publicId = $publicId;
        $this->type = $type;
    }

    // Getters and setters

    public function getId()
    {
        return $this->id;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getPublicId()
    {
        return $this->publicId;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setPublicId($publicId)
    {
        $this->publicId = $publicId;
    }

    public function setType($type)
    {
        $this->type = $type;
    }
}
