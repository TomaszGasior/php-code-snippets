<?php

namespace App;

use RuntimeException;
use Symfony\Component\Config\Definition\NodeInterface;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class Config
{
    private ?array $data = null;

    public function __construct(private string $filepath, private NodeInterface $configTree) {}

    public function get(string $name): mixed
    {
        if (null === $this->data) {
            if (!is_file($this->filepath)) {
                throw new RuntimeException(sprintf('Configuration file "%s" is missing. Use "dump-config" command to create it.', $this->filepath));
            }

            $this->data = (new Processor)->process(
                $this->configTree,
                Yaml::parse(file_get_contents($this->filepath) ?: [])
            );
        }

        return $this->data[$name];
    }
}
