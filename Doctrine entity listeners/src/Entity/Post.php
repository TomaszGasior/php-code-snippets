<?php

namespace App\Entity;

use App\Doctrine\EntityListener\PostListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\EntityListeners([PostListener::class])]
class Post
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(type: Types::STRING, length: 500)]
    private string $name;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $author;

    public function __construct(int $id, string $name, User $author)
    {
        $this->id = $id;
        $this->name = $name;
        $this->author = $author;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }
}
