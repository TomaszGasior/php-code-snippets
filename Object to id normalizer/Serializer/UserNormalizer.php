<?php

namespace App\Serializer;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserNormalizer extends ObjectToIdNormalizer
{
    public function __construct(private EntityManagerInterface $entityManager) {}

    public function getClass(): string
    {
        return User::class;
    }

    /**
     * @param User $object
     */
    public function getIdFromObject(object $object): string|int
    {
        return $object->name;
    }

    /**
     * @return ?User
     */
    public function getObjectById(string|int $id): ?object
    {
        return $this->entityManager->find(User::class, (string) $id);
    }
}
