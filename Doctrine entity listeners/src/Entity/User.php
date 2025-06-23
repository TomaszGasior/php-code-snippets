<?php

namespace App\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: Types::STRING, length: 50, unique: true)]
    private string $name;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private DateTimeImmutable $lastActivityDate;

    public function __construct(string $name)
    {
        $this->name = $name;

        $this->lastActivityDate = new DateTimeImmutable;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getLastActivityDate(): DateTimeInterface
    {
        return $this->lastActivityDate;
    }

    public function refreshLastActivityDate(): self
    {
        $this->lastActivityDate = new DateTimeImmutable;

        return $this;
    }
}
