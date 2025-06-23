<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Response for streaming StreamedText contents without buffering.
 */
class StreamedTextResponse extends StreamedResponse
{
    public function __construct(private StreamedText $streamedText)
    {
        parent::__construct();

        $this->setCallback($this->stream(...));

        $this->headers->set('Content-type', 'text/plain');
        $this->headers->set('Cache-Control', 'no-cache');
        $this->headers->set('Connection', 'keep-alive');
        $this->headers->set('X-Accel-Buffering', 'no');
    }

    private function stream(): void
    {
        set_time_limit(0);

        foreach ($this->streamedText->stream() as $string) {
            echo $string;

            flush();

            if (ob_get_level() > 0) {
                ob_end_flush();
            }

            if (connection_aborted()) {
                break;
            }
        }
    }
}
