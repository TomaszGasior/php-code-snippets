<?php

namespace App\Twig;

use App\Util\PasswordGenerator;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class PasswordGeneratorExtension extends AbstractExtension
{
    private $passwordGenerator;

    public function __construct(PasswordGenerator $passwordGenerator)
    {
        $this->passwordGenerator = $passwordGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_random_password', [$this->passwordGenerator, 'getRandomPassword']),
        ];
    }
}
