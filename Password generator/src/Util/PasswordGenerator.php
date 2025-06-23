<?php

namespace App\Util;

/**
 * Password generator based on:
 * https://paragonie.com/blog/2015/07/how-safely-generate-random-strings-and-integers-in-php
 */
class PasswordGenerator
{
    private const ALPHABET = '!"#$%&\'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstuvwxyz{|}~';

    /**
     * Generate random password with specified length.
     */
    public function getRandomPassword(int $passwordLength = 20): string
    {
        if ($passwordLength < 1) {
            throw new \InvalidArgumentException;
        }

        $alphabetLength = strlen(self::ALPHABET) - 1;

        $password = '';

        for ($i = 0; $i < $passwordLength; $i++) {
            $password .= self::ALPHABET[random_int(0, $alphabetLength)];
        }

        return $password;
    }
}
