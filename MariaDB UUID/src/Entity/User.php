<?php

namespace App\Entity;

use App\Doctrine\Type\MariadbUuidType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: MariadbUuidType::NAME)]
    private Uuid $id;

    #[ORM\Column(type: Types::STRING, length: 50)]
    private string $name;

    public function __construct(string $name)
    {
        $this->id = Uuid::v7();

        $this->name = $name;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }
}
