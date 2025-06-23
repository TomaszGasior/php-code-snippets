<?php

namespace App\Dto;

use App\Entity\User;
use App\Serializer\ObjectToIdNormalizer;
use Symfony\Component\Serializer\Attribute\Context;

class RequestDto
{
    #[Context([ObjectToIdNormalizer::ALLOW_MISSING_OBJECT => true])]
    public User $user;
}
