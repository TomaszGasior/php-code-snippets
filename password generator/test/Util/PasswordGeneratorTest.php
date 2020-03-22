<?php

namespace App\Tests\Util;

use App\Util\PasswordGenerator;
use PHPUnit\Framework\TestCase;

class PasswordGeneratorTest extends TestCase
{
    /** @var PasswordGenerator|MockObject */
    private $passwordGenerator;

    public function setUp(): void
    {
        $this->passwordGenerator = new PasswordGenerator;
    }

    public function testGetRandomPassword(): void
    {
        $firstPassword = $this->passwordGenerator->getRandomPassword(33);
        $secondPassword = $this->passwordGenerator->getRandomPassword(43);

        $this->assertTrue(33 === mb_strlen($firstPassword));
        $this->assertTrue(43 === mb_strlen($secondPassword));
        $this->assertNotEquals($secondPassword, $firstPassword);
    }

    public function testThrowExceptionOnZeroLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->passwordGenerator->getRandomPassword(0);
    }

    public function testThrowExceptionOnNegativeLength(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->passwordGenerator->getRandomPassword(-10);
    }
}
