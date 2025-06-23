<?php

namespace App\Twig;

use GenderDetector\Gender;
use GenderDetector\GenderDetector;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
 * Provides integration with tuqqu/gender-detector for Twig templates.
 *
 * Use these tests to detect gender of first name:
 * ```
 * {% if 'Anna' is woman %}Anna is a woman{% endif %}
 * {% if 'Anna' is not man %}Anna is not a man{% endif %}
 * {% if 'Jack' is man %}Jack is a man{% endif %}
 * {% if 'Jack' is not woman %}Jack is not a woman{% endif %}
 * ```
 *
 * Use `text_gender()` function to print conditional text:
 * ```
 * {{ 'Anna ' ~ text_gender('Anna', 'is a man', 'is a woman', 'fallback') }}
 * ```
 * Fallback text is optional, man text will be used instead.
 */
class GenderExtension extends AbstractExtension
{
    private $detector;

    public function getTests(): array
    {
        return [
            new TwigTest('woman', [$this, 'isWomanName']),
            new TwigTest('man', [$this, 'isManName']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('text_gender', [$this, 'getTextForGender']),
        ];
    }

    public function getTextForGender($name, string $textMan, string $textWoman,
                                     string $textFallback = null): string
    {
        if (null === $textFallback) {
            $textFallback = $textMan;
        }

        if ($this->isWomanName($name)) {
            return $textWoman;
        }
        elseif ($this->isManName($name)) {
            return $textMan;
        }

        return $textFallback;
    }

    public function isWomanName($name): bool
    {
        return $this->isExpectedGender($name, Gender::FEMALE);
    }

    public function isManName($name): bool
    {
        return $this->isExpectedGender($name, Gender::MALE);
    }

    private function isExpectedGender(string $name, string $expectedGender): bool
    {
        if (false === is_string($name)) {
            return false;
        }

        if (null === $this->detector) {
            $this->detector = new GenderDetector;
        }

        return $this->detector->detect($name) === $expectedGender;
    }
}
