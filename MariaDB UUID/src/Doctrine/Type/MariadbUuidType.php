<?php

namespace App\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\Type;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Exclude;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV1;
use Symfony\Component\Uid\UuidV6;
use Symfony\Component\Uid\UuidV7;

/**
 * Integrates MariaDB native UUID column type and Symfony Uid component
 * into Doctrine DBAL/ORM.
 *
 * Use with MariaDB 10.11.5 or later and UUID v7 (or v6/v1).
 * Otherwise make sure to use correct combination of MariaDB version and UUID version:
 * https://mariadb.com/docs/server/reference/data-types/string-data-types/uuid-data-type
 *
 * This class does not support non-MariaDB connections (MySQL is not supported).
 * Make sure to include "MariaDB" in version parameter of "DATABASE_URL" env variable.
 *
 * If you need to support multiple database engines with one type,
 * take a look at UuidType from `symfony/doctrine-bridge`.
 */
#[Exclude]
class MariadbUuidType extends Type
{
    public const NAME = 'mariadb_uuid';

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform): string
    {
        $this->validatePlatform($platform);

        return 'UUID';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Uuid
    {
        $this->validatePlatform($platform);

        return Uuid::fromString($value);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): string
    {
        $this->validatePlatform($platform);

        if (!($value instanceof UuidV1 || $value instanceof UuidV6 || $value instanceof UuidV7)) {
            throw ConversionException::conversionFailedInvalidType($value, $this->getName(), [UuidV1::class, UuidV6::class, UuidV7::class]);
        }

        return $value->toString();
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        $this->validatePlatform($platform);

        return ['uuid'];
    }

    public function getName()
    {
        return self::NAME;
    }

    private function validatePlatform(AbstractPlatform $platform): void
    {
        if (!($platform instanceof MariaDBPlatform)) {
            throw new RuntimeException(self::class . ' supports MariaDB database only.');
        }
    }
}
