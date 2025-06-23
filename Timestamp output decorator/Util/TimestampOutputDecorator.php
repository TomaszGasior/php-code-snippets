<?php

namespace App\Util;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Decorates ConsoleOutput or StreamOutput to prepend each line of output
 * with date and time. Designed to be used in Symfony Console commands.

 * This decorator must be used only with simple text messages.
 * It is not compatible with SymfonyStyle or progress bars.
 */
class TimestampOutputDecorator implements OutputInterface
{
    private const TIMESTAMP_FORMAT = 'Y-m-d H:i:s';

    private const SKIP_FIRST_MESSAGE = 0b00001;
    private const ONLY_FIRST_MESSAGE = 0b00010;

    private $output;
    private $isJustAfterNewline = true;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function writeln(string|iterable $messages, int $options = 0): void
    {
        $messages = (array) $messages;

        if ($this->isJustAfterNewline) {
            $messages = $this->addTimestampToMessages($messages);
        }
        else {
            $messages = $this->addTimestampToMessages($messages, self::SKIP_FIRST_MESSAGE);
        }

        $this->isJustAfterNewline = true;

        $this->output->writeln($messages, $options);
    }

    public function write(string|iterable $messages, bool $newline = false, int $options = 0): void
    {
        $messages = (array) $messages;

        if ($newline && $this->isJustAfterNewline) {
            $messages = $this->addTimestampToMessages($messages);
        }
        elseif ($newline) {
            $messages = $this->addTimestampToMessages($messages, self::SKIP_FIRST_MESSAGE);
        }
        elseif ($this->isJustAfterNewline) {
            $messages = $this->addTimestampToMessages($messages, self::ONLY_FIRST_MESSAGE);
        }

        $this->isJustAfterNewline = $newline;

        $this->output->write($messages, $newline, $options);
    }

    public function setVerbosity(int $level): void
    {
        $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function getVerbosity(): int
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function isQuiet(): bool
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function isVerbose(): bool
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function isVeryVerbose(): bool
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function isDebug(): bool
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function setDecorated(bool $decorated): void
    {
        $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function isDecorated(): bool
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function setFormatter(OutputFormatterInterface $formatter): void
    {
        $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function getFormatter(): OutputFormatterInterface
    {
        return $this->output->{__FUNCTION__}(...func_get_args());
    }

    public function __call($method, $arguments)
    {
        return $this->output->$method(...$arguments);
    }

    private function addTimestampToMessages(array $messages, int $mode = 0): array
    {
        $messages = array_values($messages);

        array_walk($messages, function (&$message, $i) use ($mode) {
            if ($mode & self::ONLY_FIRST_MESSAGE && 0 !== $i) {
                return;
            }

            if ($mode & self::SKIP_FIRST_MESSAGE && 0 === $i) {
                return;
            }

            $message = sprintf('[%s] %s', date(self::TIMESTAMP_FORMAT), $message);
        });

        return $messages;
    }
}
