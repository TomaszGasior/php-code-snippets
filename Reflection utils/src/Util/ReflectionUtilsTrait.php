<?php

namespace App\Util;

/**
 * Provides utilities based on PHP reflection to work with
 * objects private fields and prefixed class constants.
 */
trait ReflectionUtilsTrait
{
    /**
     * Get value of private field from given object.
     */
    protected function getPrivateFieldOfObject(object $object, string $fieldName)
    {
        $reflection = new \ReflectionProperty(get_class($object), $fieldName);

        $reflection->setAccessible(true);

        return $reflection->getValue($object);
    }

    /**
     * Set value of private field in given object.
     */
    protected function setPrivateFieldOfObject(object $object, string $fieldName, $value): void
    {
        $reflection = new \ReflectionProperty(get_class($object), $fieldName);

        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }

    /**
     * Get all constants of given class with specified prefix.
     * Returns array with constant names as keys and constant values as values.
     */
    protected function getPrefixedConstantsOfClass(string $className, string $prefix): array
    {
        $reflection = new \ReflectionClass($className);
        $constants = $reflection->getConstants();

        return array_filter(
            $constants,
            function ($constantName) use ($prefix){
                return 0 === strpos($constantName, $prefix);
            },
            ARRAY_FILTER_USE_KEY
        );
    }
}
