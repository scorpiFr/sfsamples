<?php
// src/Entity/BookFormEntity.php
namespace App\Entity;

use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class BookFormEntity
{
    private ?string $title = '';

    private ?string $abstract = '';

    private ?int $authorId = 0;

    /**
     * @return int
     */
    public function getAuthorId(): int
    {
        return $this->authorId;
    }

    /**
     * @param int $authorId
     * @return BookFormEntity
     */
    public function setAuthorId(int $authorId): BookFormEntity
    {
        $this->authorId = $authorId;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return BookFormEntity
     */
    public function setTitle(string $title): BookFormEntity
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getAbstract(): string
    {
        return $this->abstract;
    }

    /**
     * @param string $abstract
     * @return BookFormEntity
     */
    public function setAbstract(string $abstract): BookFormEntity
    {
        $this->abstract = $abstract;
        return $this;
    }

}
