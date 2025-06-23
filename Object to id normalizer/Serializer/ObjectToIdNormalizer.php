<?php

namespace App\Serializer;

use ArrayObject;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Exception\UnexpectedValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * This utility integrates with Symfony Serializer to
 * normalize object into plain string/id containing its identifier
 * and denormalize string/id back into proper object.
 *
 * Normalization example:
 * - From: ResponseDto object, ResponseDto::$user containing User object for "foo" user.
 * - To: JSON code `{ user: "foo" }`.
 *
 * Denormalization example:
 * - From: JSON code `{ user: "foo" }`.
 * - To: RequestDto object, RequestDto::$user containing User object for "foo" user.
 */
abstract class ObjectToIdNormalizer implements NormalizerInterface, DenormalizerInterface
{
    public const ALLOW_MISSING_OBJECT = 'allow_missing_object';

    abstract function getClass(): string;

    abstract function getIdFromObject(object $object): string|int;

    abstract function getObjectById(string|int $id): object|null;

    public function normalize(mixed $data, ?string $format = null, array $context = []): array|string|int|float|bool|ArrayObject|null
    {
        if (!is_a($data, $this->getClass())) {
            throw new InvalidArgumentException();
        }

        return $this->getIdFromObject($data);
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return is_a($data, $this->getClass());
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        if (!(is_string($data) || is_int($data))) {
            throw new UnexpectedValueException('Invalid object id.');
        }

        $object = $this->getObjectById($data);

        if (!$object && !($context[self::ALLOW_MISSING_OBJECT] ?? false)) {
            throw new NotNormalizableValueException('Object with specified id could not be found.');
        }

        return $object;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_a($type, $this->getClass(), true);
    }

    public function getSupportedTypes(?string $format): array
    {
        return [$this->getClass() => true];
    }
}
